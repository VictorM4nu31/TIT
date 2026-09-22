<?php
require('fpdf185/fpdf.php');

class PDF extends FPDF
{
// Cabecera de página
function Header()
{
    $this->SetFont('Times','',16);
    $this->Image('tephe.png',10,8,32);
    $this->SetXY(80,15);
    $this->Cell(55,8,'Reporte de Entrada y Salida de Transporte Publico',0,0,'C',0);
    $this->Ln(45);
}

// Pie de página
function Footer()
{
    // Posición: a 1,5 cm del final
    $this->SetY(-15);
    // Arial italic 8
    $this->SetFont('Arial','I',8);
    // Número de página
    $this->Cell(20,10,utf8_decode('Pagina ').$this->PageNo().'/{nb}',0,0,'C'); 
}
//----------------------------METODO PARA ADAPTAR LAS CELDAS-----------------------------------
protected $widths;
    protected $aligns;

    function SetWidths($w)
    {
        // Set the array of column widths
        $this->widths = $w;
    }

    function SetAligns($a)
    {
        // Set the array of column alignments
        $this->aligns = $a;
    }

    function Row($data,$setX)
    {
        // Calculate the height of the row
        $nb = 0;
        for($i=0;$i<count($data);$i++)
            $nb = max($nb,$this->NbLines($this->widths[$i],$data[$i]));
        $h = 5*$nb;
        // Issue a page break first if needed
        $this->CheckPageBreak($h);
        // Draw the cells of the row
        for($i=0;$i<count($data);$i++)
        {
            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            // Save the current position
            $x = $this->GetX();
            $y = $this->GetY();
            // Draw the border
            $this->Rect($x,$y,$w,$h);
            // Print the text
            $this->MultiCell($w,5,$data[$i],0,$a);
            // Put the position to the right of the cell
            $this->SetXY($x+$w,$y);
        }
        // Go to the next line
        $this->Ln($h);
    }

    function CheckPageBreak($h)
    {
        // If the height h would cause an overflow, add a new page immediately
        if($this->GetY()+$h>$this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }

    function NbLines($w, $txt)
    {
        // Compute the number of lines a MultiCell of width w will take
        if(!isset($this->CurrentFont))
            $this->Error('No font has been set');
        $cw = $this->CurrentFont['cw'];
        if($w==0)
            $w = $this->w-$this->rMargin-$this->x;
        $wmax = ($w-2*$this->cMargin)*1000/$this->FontSize;
        $s = str_replace("\r",'',(string)$txt);
        $nb = strlen($s);
        if($nb>0 && $s[$nb-1]=="\n")
            $nb--;
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while($i<$nb)
        {
            $c = $s[$i];
            if($c=="\n")
            {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if($c==' ')
                $sep = $i;
            $l += $cw[$c];
            if($l>$wmax)
            {
                if($sep==-1)
                {
                    if($i==$j)
                        $i++;
                }
                else
                    $i = $sep+1;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            }
            else
                $i++;
        }
        return $nl;
    }
//-----------------------------------------------------------
}

// Creación del objeto de la clase heredada
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage(); //añade la pagina en blanco
$pdf->SetMargins(10,10,10);
$pdf->SetAutoPageBreak(true,30); //salto de pagina en automatico
$pdf->SetX(17);
$pdf->SetFont('Helvetica','',10);
$pdf->Cell(29,8,'Id de transporte','B',0,'C',0);
$pdf->Cell(20,8,'Ruta','B',0,'C',0);
$pdf->Cell(29,8,'Hora de Salida','B',0,'C',0);
$pdf->Cell(29,8,'Hora de Entrada','B',0,'C',0);
$pdf->Cell(29,8,'Id de transporte','B',0,'C',0);
$pdf->Cell(27,8,'Fecha',1,1,'C',0);

$pdf->SetFont('Arial','',10);
//ancho de las celdas
$pdf->SetWidths(array(30, 50, 30, 40));

$pdf->Ln(0.5);
for($i=1;$i<=5;$i++)
$pdf->SetX(15);

$pdf->AddPage();

$pdf->Output();
?>