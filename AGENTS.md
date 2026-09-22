# Reglas obligatorias IA — Tranzit (PHP puro)

> Este archivo es el guardrail operativo. La norma completa es prescriptiva. Todo agente DEBE obedecer esto.

## 1. Alcance
- PHP >= 8.1 puro, sin frameworks. PSR-12 + PSR-4 + `declare(strict_types=1);` en todo archivo con lógica.
- `fpdf185/` y `vendor/` quedan excluidos del análisis estricto.

## 2. Umbrales duros (BLOQUEANTE si se violan)
- Complejidad ciclomática <= 5 (nunca > 10).
- Método/función <= 30 líneas lógicas (nunca > 50).
- Parámetros <= 4 (nunca > 5; si no, DTO).
- Anidamiento <= 3. Clase <= 300 líneas. Línea <= 120 chars.

## 3. Seguridad (OWASP Top 10) — RECHAZADO si falla
- DEBE: PDO + prepared statements + `EMULATE_PREPARES=false`, validación server-side, escape por contexto (`htmlspecialchars` / `json_encode` / `urlencode`).
- DEBE: `password_hash(ARGON2ID)` + `password_verify`, `session_regenerate_id(true)`, cookies `HttpOnly;Secure;SameSite`, CSRF con `random_bytes` + `hash_equals`.
- NO DEBE: concatenar SQL, `eval`, `unserialize($_...)`, `md5/sha1` para passwords, secretos en código, `$_GET/$_POST` directo en dominio, `@`, `die/exit` en dominio, `==` en seguridad.

## 4. Calidad / Testing
- DEBE: tipos 100% (params, props, returns), `===`, early returns, `final` por defecto, sin `global`.
- DEBE: tests unitarios + integración + negativos. Cobertura >= 80% (>= 90% dominio), MSI >= 75%.
- Orden QA: `cs:fix` → `cs:check` → `stan` → `psalm` → `test` → `mut` → `md`.

## 5. Comandos
```bash
composer install
composer cs:check
composer stan
composer psalm
composer test
composer qa
```

## 6. Veredicto
- La IA DEBE reportar: `archivo:línea`, severidad `[BLOQUEANTE]/[CRÍTICO]/[MAYOR]/[MENOR]`, evidencia y fix.
- Veredicto único: `APROBADO` / `APROBADO CON OBSERVACIONES` / `RECHAZADO`.
- `RECHAZADO` si hay 1 BLOQUEANTE/CRÍTICO, o phpstan/psalm con errores, o cobertura crítica < 80%.
- NO DEBE usar `// @phpstan-ignore` ni `psalm-suppress` sin justificar falso positivo.
