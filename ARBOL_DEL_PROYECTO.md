# 🌳 Árbol del Proyecto - **Selloscelaya**

```
selloscelaya/
│
├── 📄 Raíz
│   ├── .env
│   ├── .gitattributes
│   ├── .gitignore
│   ├── .htaccess
│   ├── composer.json / composer.lock
│   ├── error_log
│   ├── index.php              ← Punto de entrada principal
│   ├── LICENSE
│   ├── phpunit.xml.dist
│   ├── preload.php
│   ├── retrive.php
│   ├── sitemap.xml
│   └── spark                  ← CLI de CodeIgniter 4
│
├── 📁 app/                    ← CÓDIGO DE LA APLICACIÓN
│   ├── Common.php
│   ├── Controllers.zip / Database.zip / Views.zip
│   │
│   ├── 📁 Cells/              ← View Cells
│   │   └── CategoryMenuCell.php
│   │
│   ├── 📁 Config/             ← Configuración del framework
│   │   ├── App.php, Autoload.php, Cache.php, Constants.php
│   │   ├── ContentSecurityPolicy.php, Cookie.php, CURLRequest.php
│   │   ├── Database.php, DocTypes.php, Email.php
│   │   ├── Encryption.php, Events.php, Exceptions.php
│   │   ├── Feature.php, Filters.php, ForeignCharacters.php
│   │   ├── Format.php, Generators.php, Honeypot.php
│   │   ├── Images.php, Kint.php, Logger.php
│   │   ├── Migrations.php, Mimes.php, Modules.php
│   │   ├── Pager.php, Paths.php, Publisher.php
│   │   ├── Routes.php, Routing.php, Security.php
│   │   ├── Services.php, Session.php, Toolbar.php
│   │   ├── UserAgents.php, Validation.php, View.php
│   │   └── 📁 Boot/
│   │       ├── development.php
│   │       ├── production.php
│   │       └── testing.php
│   │
│   ├── 📁 Controllers/        ← Controladores
│   │   ├── BaseController.php
│   │   ├── Home.php
│   │   │
│   │   ├── 📁 Admin/          ← Módulo de Administración
│   │   │   ├── Admin.php
│   │   │   ├── AdministracionController.php
│   │   │   ├── Articulos.php
│   │   │   ├── BalanceController.php
│   │   │   ├── CategoriasController.php
│   │   │   ├── Clientes.php
│   │   │   ├── Compras.php
│   │   │   ├── Cotizaciones.php
│   │   │   ├── CuentasController.php
│   │   │   ├── EjecutarMigraciones.php
│   │   │   ├── Existencias.php
│   │   │   ├── FacturaControllerTest.php
│   │   │   ├── FacturasController.php
│   │   │   ├── GastosController.php
│   │   │   ├── ImportClientesController.php
│   │   │   ├── inventario_form.php
│   │   │   ├── OrdenTrabajoController.php
│   │   │   ├── PedidosController.php
│   │   │   ├── Proveedores.php
│   │   │   ├── PuntoVentaController.php
│   │   │   └── Ventas.php
│   │   │
│   │   └── 📁 Auth/           ← Módulo de Autenticación
│   │       └── ...
│   │
│   ├── 📁 Database/           ← Migraciones y Seeders
│   │   ├── Migraciones/
│   │   └── Seeds/
│   │
│   ├── 📁 Filters/            ← Filtros del framework
│   │
│   ├── 📁 Helpers/            ← Helper functions
│   │
│   ├── 📁 Language/           ← Archivos de idioma
│   │
│   ├── 📁 Libraries/          ← Librerías personalizadas
│   │
│   ├── 📁 Models/             ← Modelos (ORM)
│   │
│   ├── 📁 Services/           ← Capa de Servicios
│   │
│   ├── 📁 ThirdParty/         ← Librerías de terceros
│   │
│   └── 📁 Views/              ← Vistas (HTML + PHP)
│       ├── Panel/             ← Vistas del Panel Admin
│       │   ├── facturas.php
│       │   └── ...
│       └── ...
│
├── 📁 public/                 ← Archivos públicos (CSS, JS, imágenes)
│   ├── css/
│   ├── img/
│   └── js/
│
├── 📁 system/                 ← NÚCLEO DE CODEIGNITER 4
│   ├── BaseModel.php
│   ├── bootstrap.php
│   ├── CodeIgniter.php
│   ├── Common.php
│   ├── Controller.php
│   ├── Entity.php
│   ├── Model.php
│   └── 📁 API/, Autoloader/, Cache/, CLI/, Commands/,
│            Config/, Cookie/, Database/, Debug/, Email/,
│            Encryption/, Entity/, Events/, Exceptions/,
│            Files/, Filters/, Format/, Helpers/, Honeypot/,
│            HotReloader/, HTTP/, I18n/, Images/, Language/,
│            Log/, Modules/, Pager/, Publisher/, RESTful/,
│            Router/, Security/, Session/, Test/, ThirdParty/,
│            Throttle/, Traits/, Typography/, Validation/, View/
│
├── 📁 tests/                  ← Pruebas unitarias y de integración
│   ├── _support/
│   ├── database/
│   ├── session/
│   └── unit/
│
├── 📁 writable/               ← Archivos escribibles (logs, sesiones, uploads)
│   ├── cache/
│   ├── logs/
│   ├── session/
│   ├── uploads/
│   │   └── ordenes/          ← Imágenes de órdenes de trabajo
│   └── ...
│
└── 📁 vendor/                 ← Dependencias (Composer)
    └── ...
```

---

## 📊 Resumen de la Arquitectura

**Framework:** CodeIgniter 4 (PHP MVC)

| Capa | Descripción |
|------|-------------|
| **`app/Controllers/`** | Controladores que manejan la lógica de las peticiones HTTP |
| **`app/Models/`** | Modelos para la interacción con la base de datos |
| **`app/Views/`** | Vistas con la interfaz de usuario |
| **`app/Services/`** | Lógica de negocio separada de los controladores |
| **`app/Database/`** | Migraciones y seeders para la BD |
| **`app/Config/`** | Archivos de configuración del sistema |
| **`app/Libraries/`** | Librerías propias del proyecto |
| **`app/Helpers/`** | Funciones auxiliares globales |
| **`app/Filters/`** | Filtros para middleware (autenticación, etc.) |

### 🧩 Módulos Principales (Admin)

| Módulo | Controlador |
|--------|-------------|
| 🛒 **Ventas** | `Ventas.php` |
| 📦 **Compras** | `Compras.php` |
| 👥 **Clientes** | `Clientes.php` |
| 📋 **Artículos** | `Articulos.php` |
| 🏷️ **Categorías** | `CategoriasController.php` |
| 📄 **Facturación** | `FacturasController.php` |
| 🔧 **Órdenes de Trabajo** | `OrdenTrabajoController.php` |
| 💰 **Gastos** | `GastosController.php` |
| 📊 **Balance** | `BalanceController.php` |
| 💳 **Cuentas** | `CuentasController.php` |
| 🚚 **Proveedores** | `Proveedores.php` |
| 📝 **Cotizaciones** | `Cotizaciones.php` |
| 📦 **Pedidos** | `PedidosController.php` |
| 🏪 **Punto de Venta** | `PuntoVentaController.php` |
| 📉 **Existencias** | `Existencias.php` |
| ⚙️ **Administración** | `AdministracionController.php` |
| 📥 **Importación Clientes** | `ImportClientesController.php` |

---

*Última actualización: Mayo 2026*
