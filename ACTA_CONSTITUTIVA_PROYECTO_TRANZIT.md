# ACTA CONSTITUTIVA DEL PROYECTO TRANZIT

## 1. INFORMACIÓN GENERAL

| Campo | Descripción |
|---|---|
| **Nombre del Proyecto** | Tranzit - Sistema de Control de Transporte Público |
| **Fecha de Constitución** | 21 de Septiembre de 2026 |
| **Cliente / Patrocinador** | Empresa de Transporte Público TEPHE |
| **Responsable del Proyecto** | Viktor |
| **Metodología** | Desarrollo Propietario (PHP/HTML) |
| **Herramientas** | PHP 7+, MySQL, FPDF 1.85 |
| **Estado Actual** | En desarrollo (MVP funcional) |

---

## 2. CONTEXTO Y JUSTIFICACIÓN

El proyecto **Tranzit** nace de la necesidad de digitalizar y controlar el registro de entrada y salida del servicio de transporte público de la empresa **TEPHE**. Actualmente, la gestión de horarios, conductores, checadores y rutas se realiza de forma manual o fragmentada, lo que genera:

- Pérdida de control sobre los horarios de rutas
- Dificultad para generar reportes de movimiento de transporte
- Registro ineficiente de personas asociadas al servicio
- Ausencia de un sistema unificado de autenticación por roles

**Objetivo principal**: Crear un sistema web que permita gestionar de forma centralizada los registros de transporte público, autenticación por roles y generación de reportes en PDF.

---

## 3. ALCANCE DEL PROYECTO

### 3.1 Alcance Actual (MVP)
- ✅ Login por roles (Administrador y Checador)
- ✅ Registro de personas (checadores) con datos personales
- ✅ Registro de horarios de salida de combis (`hora_sal_tb`)
- ✅ Registro de horarios de entrada de combis (`hora_ent_tb`)
- ✅ Generación de reportes en PDF con FPDF
- ✅ Ruta configurada: **TEPHE-IXMI**

### 3.2 Alcance Futuro (Plan de Acción)
- [ ] Mejora de seguridad (contraseñas encriptadas, consultas preparadas)
- [ ] Gestión de múltiples rutas
- [ ] Dashboard de estadísticas en tiempo real
- [ ] Exportación de reportes en Excel/CSV
- [ ] Notificaciones y alertas de retrasos
- [ ] Módulo de gestión de conductores
- [ ] Módulo de reportes de incidencias
- [ ] Interfaz móvil responsive

---

## 4. STAKEHOLDERS

| Rol | Nombre/Identificación | Responsabilidad |
|---|---|---|
| **Patrocinador** | Empresa TEPHE | Aprobación del proyecto y recursos |
| **Responsable del Proyecto** | Viktor | Planificación, ejecución y seguimiento |
| **Desarrollador** | Viktor | Codificación, bases de datos, integración |
| **Usuarios Finales** | Administradores y Checadores | Uso operativo del sistema |
| **Tester/QA** | *(Pendiente asignar)* | Validación de funcionalidades |

---

## 5. OBJETIVOS DEL PROYECTO

### Objetivo General
Desarrollar un sistema web de gestión de transporte público que permita controlar las entradas y salidas de unidades de la empresa TEPHE de manera eficiente y con registro digital.

### Objetivos Específicos
1. Implementar un sistema de autenticación segura con roles diferenciados
2. Automatizar el registro de horarios de transporte
3. Generar reportes digitales (PDF) para auditoría y control
4. Centralizar la información de personas, conductores y rutas en una sola base de datos
5. Garantizar la escalabilidad del sistema para múltiples rutas y usuarios

---

## 6. ANÁLISIS DE RIESGOS

| # | Riesgo | Probabilidad | Impacto | Mitigación |
|---|---|---|---|---|
| R1 | Inyección SQL por consultas no preparadas | Alta | Crítico | Migración a consultas parametrizadas (PDO/prepared statements) |
| R2 | Contraseñas almacenadas en texto plano | Alta | Alto | Implementar `password_hash()` y `password_verify()` |
| R3 | Fallo de conectividad a base de datos | Media | Alto | Implementar manejadores de errores robustos |
| R4 | Base de datos desnormalizada / múltiples conexiones | Media | Medio | Unificar conexión a un solo archivo `conexion.php` centralizado |
| R5 | Ausencia de validación de inputs del usuario | Alta | Medio | Implementar validación server-side en todos los formularios |
| R6 | Falta de responsive en la interfaz | Alta | Medio | Adaptar HTML/CSS a diseño responsive con frameworks |
| R7 | Dependencia de FPDF sin actualizaciones | Baja | Bajo | Mantener versión estable y considerar alternativas |

---

## 7. ARQUITECTURA TÉCNICA

### 7.1 Estructura de Archivos
```
tranzit/
├── inicio.html                  # Página de entrada (selección de rol)
├── reporte.php                  # Generador de reportes PDF (FPDF)
├── tephe.png                    # Logo de la empresa
├── fpdf185/                     # Librería FPDF para generación de PDFs
│   ├── fpdf.php
│   ├── font/
│   ├── makefont/
│   └── doc/
├── admi/                        # Módulo de Administrador
│   ├── administrador.html       # Login del administrador
│   ├── login.php                # Validación contra bd_tranzit/admin_tb
│   ├── conexion.php             # Conexión a bd_tranzit
│   └── estilo.css
├── chec/                        # Módulo de Checadores
│   ├── checador.html            # Login del checador
│   ├── login.php                # Validación contra tit_bd2/asistente_trans_public
│   ├── conexion.php             # Conexión a tit_bd2
│   └── estilo.css
├── registr_pers/                # Registro de Personas
│   ├── registro.html            # Formulario de registro
│   ├── registro.php             # Inserta en checador_tb
│   ├── conexion.php             # Conexión a tit_bd2
│   └── estilo.css
└── registr_combis/              # Registro de Combis/Horarios
    ├── horario_combi.html       # Formulario de horarios (salida/entrada)
    ├── agregar.php              # Inserta en hora_sal_tb
    ├── editar.php               # Inserta en hora_ent_tb
    └── estilo.css
```

### 7.2 Bases de Datos
| Base de Datos | Tablas Principales | Descripción |
|---|---|---|
| `bd_tranzit` | `admin_tb` | Credenciales del administrador |
| `tit_bd2` | `asistente_trans_public` | Usuarios checadores |
| `tit_bd2` | `checador_tb` | Personas registradas (datos personales) |
| `tit_bd2` | `hora_sal_tb` | Registros de hora de salida |
| `tit_bd2` | `hora_ent_tb` | Registros de hora de entrada |

### 7.3 Stack Tecnológico
- **Frontend**: HTML5, CSS, formularios PHP
- **Backend**: PHP 7+
- **Base de Datos**: MySQL / MariaDB
- **Generación de PDF**: FPDF 1.85
- **Servidor Web**: Apache con PHP

---

## 8. CRONOGRAMA Y PLAN DE ACCIÓN

### Fase 1: Consolidación y Estabilización (Semanas 1-2)
| Tarea | Responsable | Entregable |
|---|---|---|
| Unificar conexión a BD centralizada | Viktor | 1 solo `conexion.php` compartido |
| Corregir inyección SQL (consultas preparadas) | Viktor | Códigos seguros en login.php, registro.php, agregar.php, editar.php |
| Implementar encriptación de contraseñas | Viktor | `password_hash()` en registro, `password_verify()` en login |
| Validar todos los inputs del formulario | Viktor | Validaciones server-side en todos los forms |

### Fase 2: Mejora de Interfaz y Experiencia (Semanas 3-4)
| Tarea | Responsable | Entregable |
|---|---|---|
| Diseño responsive de todas las páginas | Viktor | HTML/CSS adaptado a móviles y tablets |
| Crear navegación entre módulos post-login | Viktor | Dashboards por rol (admin y checador) |
| Implementar sesiones activas (session management) | Viktor | `session_start()` con control de acceso |

### Fase 3: Funcionalidades Avanzadas (Semanas 5-6)
| Tarea | Responsable | Entregable |
|---|---|---|
| Dashboard con estadísticas del transporte | Viktor | Gráficos/resúmenes de rutas y horarios |
| Gestión de múltiples rutas | Viktor | Opción de agregar/editar rutas |
| Módulo de gestión de conductores | Viktor | CRUD completo de conductores |

### Fase 4: Optimización y Despliegue (Semanas 7-8)
| Tarea | Responsable | Entregable |
|---|---|---|
| Exportación de reportes (PDF + Excel/CSV) | Viktor | Formatos múltiples de reportes |
| Pruebas de seguridad y rendimiento | Viktor | Documentación de pruebas |
| Despliegue en servidor productivo | Viktor | Sistema en línea |
| Documentación técnica y manual de usuario | Viktor | Guía completa |

---

## 9. PRESUPUESTO ESTIMADO

| Recurso | Costo Estimado |
|---|---|
| Hosting/Servidor | *(TBD)* |
| Dominio | *(TBD)* |
| Licencias | Gratuito (PHP, MySQL, FPDF) |
| Tiempo de desarrollo | *(TBD - interno)* |

---

## 10. ACEPTACIÓN DEL PROYECTO

El presente documento será la base para la planificación y ejecución del proyecto **Tranzit**. Se requiere la aprobación de todas las partes involucradas para dar inicio formal al plan de acción.

| Firma | Nombre | Rol | Fecha |
|---|---|---|---|
| _______________ | Viktor | Responsable del Proyecto | ____/____/______ |
| _______________ | Patrocinador TEPHE | Patrocinador | ____/____/______ |

---

## 11. VERSIONES DEL DOCUMENTO

| Versión | Fecha | Descripción | Autor |
|---|---|---|---|
| 1.0 | 21/09/2026 | Acta Constitutiva Inicial | Viktor |

---

> **Nota**: Este documento servirá como base para el **Plan de Acción detallado** del proyecto Tranzit, el cual se desarrollará en la fase de ejecución posterior a la aprobación de este comité.
