<?php

declare(strict_types=1);

// phpcs:ignoreFile PSR1.Files.SideEffects,PSR1.Classes.ClassDeclaration.MissingNamespace,Generic.Files.LineLength

require __DIR__ . '/fpdf185/fpdf.php';

use App\Infrastructure\Database\PdoFactory;

/**
 * @psalm-suppress MissingClass
 * @psalm-suppress PropertyNotSetInConstructor
 * @psalm-suppress MixedOperand
 * @psalm-suppress MixedArrayAccess
 * @psalm-suppress MixedAssignment
 * @psalm-suppress MixedArgument
 * @psalm-suppress MixedArrayOffset
 */
final class PDF extends FPDF
{
    /** @var array<int,int> */
    protected $widths = [];
    /** @var array<int,string> */
    protected $aligns = [];

    public function Header(): void
    {
        $this->SetFont('Times', '', 16);
        $this->Image('tephe.png', 10, 8, 32);
        $this->SetXY(80, 15);
        $this->Cell(55, 8, 'Reporte de Entrada y Salida de Transporte Publico', 0, 0, 'C', 0);
        $this->Ln(45);
    }

    public function Footer(): void
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(20, 10, utf8_decode('Pagina ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    /**
     * @param array<int,int> $w
     */
    public function SetWidths(array $w): void
    {
        $this->widths = $w;
    }

    /**
     * @param array<int,string> $a
     */
    public function SetAligns(array $a): void
    {
        $this->aligns = $a;
    }

    /**
     * @param array<int,string> $data
     */
    public function Row(array $data, int $setX = 0): void
    {
        $nb = 0;
        $count = \count($data);
        for ($i = 0; $i < $count; $i++) {
            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
        }
        $h = 5 * $nb;
        $this->CheckPageBreak($h);
        for ($i = 0; $i < $count; $i++) {
            $w = $this->widths[$i];
            $a = $this->aligns[$i] ?? 'L';
            $x = $this->GetX();
            $y = $this->GetY();
            $this->Rect($x, $y, $w, $h);
            $this->MultiCell($w, 5, $data[$i], 0, $a);
            $this->SetXY($x + $w, $y);
        }
        $this->Ln($h);
    }

    public function CheckPageBreak(int $h): void
    {
        if ($this->GetY() + $h > $this->PageBreakTrigger) {
            $this->AddPage($this->CurOrientation);
        }
    }

    public function NbLines(int $w, string $txt): int
    {
        if (!isset($this->CurrentFont)) {
            $this->Error('No font has been set');
        }
        $cw = $this->CurrentFont['cw'];
        if ($w === 0) {
            $w = (int) ($this->w - $this->rMargin - $this->x);
        }
        $wmax = (int) (($w - 2 * $this->cMargin) * 1000 / $this->FontSize);
        $s = str_replace("\r", '', $txt);
        $nb = \strlen($s);
        if ($nb > 0 && $s[$nb - 1] === "\n") {
            $nb--;
        }
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c === "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if ($c === ' ') {
                $sep = $i;
            }
            $l += $cw[$c];
            if ($l > $wmax) {
                if ($sep === -1) {
                    if ($i === $j) {
                        $i++;
                    }
                } else {
                    $i = $sep + 1;
                }
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else {
                $i++;
            }
        }

        return $nl;
    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(true, 30);
$pdf->SetX(17);
$pdf->SetFont('Helvetica', '', 10);
$pdf->Cell(29, 8, 'Id de transporte', 'B', 0, 'C', 0);
$pdf->Cell(20, 8, 'Ruta', 'B', 0, 'C', 0);
$pdf->Cell(29, 8, 'Hora de Salida', 'B', 0, 'C', 0);
$pdf->Cell(29, 8, 'Hora de Entrada', 'B', 0, 'C', 0);
$pdf->Cell(29, 8, 'Id de transporte', 'B', 0, 'C', 0);
$pdf->Cell(27, 8, 'Fecha', '1', 1, 'C', 0);

$pdf->SetFont('Arial', '', 10);
$pdf->SetWidths([29, 20, 29, 29, 29, 27]);
$pdf->Ln(0.5);

try {
    $pdo = PdoFactory::create();
    $stmt = $pdo->query(
        'SELECT s.id_trans, s.ruta, s.hr_sal, e.hr_ent, s.fecha '
        . 'FROM hora_sal_tb s LEFT JOIN hora_ent_tb e '
        . 'ON s.id_trans = e.id_trans AND s.fecha = e.fecha LIMIT 100',
    );
    $rows = $stmt->fetchAll();
    foreach ($rows as $row) {
        $pdf->SetX(15);
        $data = [
            htmlspecialchars((string) ($row['id_trans'] ?? ''), ENT_QUOTES, 'UTF-8'),
            htmlspecialchars((string) ($row['ruta'] ?? ''), ENT_QUOTES, 'UTF-8'),
            htmlspecialchars((string) ($row['hr_sal'] ?? ''), ENT_QUOTES, 'UTF-8'),
            htmlspecialchars((string) ($row['hr_ent'] ?? '-'), ENT_QUOTES, 'UTF-8'),
            htmlspecialchars((string) ($row['id_trans'] ?? ''), ENT_QUOTES, 'UTF-8'),
            htmlspecialchars((string) ($row['fecha'] ?? ''), ENT_QUOTES, 'UTF-8'),
        ];
        $pdf->Row($data, 15);
    }
} catch (PDOException $e) {
    $pdf->SetX(15);
    $pdf->Cell(0, 8, 'Sin datos disponibles', 0, 1, 'C', false);
}

$pdf->Output();
