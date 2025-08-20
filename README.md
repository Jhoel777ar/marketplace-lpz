Tu ex Market
Sistema de Marketplace desarrollado con Laravel 12, que permite la gestión de productos, usuarios y roles diferenciados. El sistema incluye paneles independientes para Emprendedores y Administradores.

🔹 Requisitos
Antes de instalar, asegúrate de tener:

PHP >= 8.1

Composer

MySQL o MariaDB

Node.js y npm

Git

🔹 Instalación
Clonar el repositorio

bash
git clone https://github.com/Jhoel777ar/marketplace-lpz.git
cd marketplace-lpz
Instalar dependencias de PHP

bash
composer install
Instalar dependencias de Node.js

bash
npm install
npm run build
Configurar archivo .env

bash
cp .env.example .env
Configura tu base de datos y otros parámetros en el archivo .env:

env
APP_URL=http://127.0.0.1:8000
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nombre_base
DB_USERNAME=usuario
DB_PASSWORD=contraseña
Generar clave de aplicación

bash
php artisan key:generate
Ejecutar migraciones y seeders

bash
php artisan migrate --seed
Esto crea todas las tablas necesarias y carga datos iniciales (roles, usuarios de ejemplo, etc.).

Levantar el servidor

bash
php artisan serve
El servidor estará disponible en: http://127.0.0.1:8000

🔹 Acceso al sistema
Emprendedor
URL: http://127.0.0.1:8000/emprendedor

Función: Gestión de productos, revisión de ventas y estadísticas.

Logout: Botón de cerrar sesión en el header.

Administrador
URL: http://127.0.0.1:8000/admin

Función: Gestión completa de usuarios, productos, ventas y configuración general del sistema.

Logout: Botón de cerrar sesión en el header.

🔹 Comandos útiles
Ejecutar tests:

bash
php artisan test
Limpiar cache:

bash
php artisan optimize:clear
Migrar base de datos:

bash
php artisan migrate
Crear nuevo seeder:

bash
php artisan make:seeder NombreSeeder
🔹 Estructura del proyecto
text
marketplace-lpz/
├── app/
├── database/
├── resources/
├── routes/
├── public/
└── ...