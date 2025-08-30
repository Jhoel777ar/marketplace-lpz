# TuEx Market - Marketplace Universitario

Sistema de **Marketplace** desarrollado con **Laravel 12**, que permite la gestión de productos, usuarios y roles diferenciados. El sistema incluye paneles independientes para **Emprendedores** y **Administradores**.

---

## 📋 Resumen Ejecutivo Sprint 1

### Sprint Goal Alcanzado ✅
**Objetivo:** Implementar la gestión básica de usuarios mediante el registro y autenticación de emprendedores universitarios y clientes, asegurando un acceso confiable a la plataforma TuEx Market.

**Estado:** COMPLETADO - 21/21 Story Points entregados

### Principales Logros Técnicos
- ✅ Sistema de registro diferenciado para emprendedores y clientes
- ✅ Validaciones de formularios backend y frontend
- ✅ CRUD completo de productos para emprendedores
- ✅ Dashboard funcional del emprendedor
- ✅ Recuperación de contraseñas implementada
- ✅ Carrito de compras con funcionalidades básicas (calcular, actualizar, eliminar)
- ✅ Soporte móvil responsive

### Decisiones Arquitectónicas Clave
- **Framework:** Laravel 12 con Blade + Livewire + Filament UI
- **Base de datos:** MySQL con Eloquent ORM
- **Frontend:** TailwindCSS para responsive design
- **Autenticación:** Laravel Sanctum para futuras integraciones API
- **Testing:** PHPUnit con cobertura del 50% alcanzada

### Próximos Pasos (Sprint 2)
- Implementación de verificación de correo electrónico
- Integración con redes sociales (Facebook, Instagram)
- Sistema de notificaciones en tiempo real
- Aplicación de cupones de descuento
- Calificación de compras
- Mejoras en testing y ambiente de pruebas

---

## 📊 Documentación de Demostración

### Script Utilizado en Sprint Review
**Duración total:** 45 minutos
- **Introducción:** 5 min - Presentación del objetivo y logros
- **Demo funcionalidades:** 30 min - Demostración en vivo de cada User Story
- **Métricas y aprendizajes:** 10 min - Análisis de velocidad y retrospectiva

### Funcionalidades Demostradas
1. **US-001: Registro de Emprendedor (5 SP)**
   - Demostración del formulario de registro universitario
   - Validaciones de campos obligatorios
   - Creación exitosa de cuenta emprendedor

2. **US-002: Registro de Cliente (3 SP)**
   - Proceso de registro simplificado para clientes
   - Integración con sistema de autenticación
   - Acceso al catálogo de productos

3. **US-004: Crear Producto (8 SP)**
   - Interfaz de creación de productos
   - Subida de imágenes y descripciones
   - Integración con dashboard del emprendedor

4. **US-006: Editar Producto (5 SP)**
   - Funcionalidad de edición de productos existentes
   - Actualización en tiempo real en el catálogo
   - Validaciones de cambios

### Screenshots de Funcionalidades

- **Registro de emprendedores:**  
<img src="https://drive.google.com/uc?export=view&id=1QY9TLtGiEa5z-YG905cf0f6rIMCDuEgR" alt="Registro de emprendedores" width="600"/>

- **Dashboard emprendedor:**  
<img src="https://drive.google.com/uc?export=view&id=15WUnjgdYNhMNBFGFAaav5sBkq7dx3F-V" alt="Dashboard emprendedor" width="600"/>

- **Creación de productos:**  
<img src="https://drive.google.com/uc?export=view&id=1sLQ5NZddMMU3dtKmLfEsYQyH1Tgw98yC" alt="Creación de productos" width="600"/>

- **Vista móvil responsive:**  
<img src="https://drive.google.com/uc?export=view&id=1pJYa36nQ05lBaHGb6i2I5dLz0jfUjRfm" alt="Vista móvil responsive" width="600"/>

### Feedback de Stakeholders

**Grupo 1 - Evaluación promedio: 7.75/10**  
- Fortalezas identificadas: Interfaz elegante y amigable  
- Áreas de mejora: Verificación de correo, completar funciones de edición  
- Prioridad alta para próximo sprint: Verificación de documentos universitarios  

[Ver feedback completo - Grupo 1 en Google Sheets](https://docs.google.com/spreadsheets/d/1Klf9KSsABJHBgCLOu5Aa_EbGO9mF-UbR/edit?usp=drive_link&ouid=102186777378516461202&rtpof=true&sd=true)

**Grupo 3 - Evaluación: 10/10**  
- Destacaron: Calidad del sistema de registro  
- Sugerencia: Incorporar logo del equipo

### Métricas de Demo
- **Duración:** 45 minutos (objetivo cumplido)
- **Participantes:** 15 stakeholders
- **Q&A:** 15 preguntas respondidas
- **Tasa de satisfacción:** 85% (basada en feedback recibido)

---

## 🔹 Requisitos del Sistema

Antes de instalar, asegúrate de tener:
- PHP >= 8.1
- Composer
- MySQL o MariaDB
- Node.js y npm
- Git

---

## 🔹 Instalación

1. **Clonar el repositorio**
```bash
git clone https://github.com/Jhoel777ar/marketplace-lpz.git
cd marketplace-lpz
```

2. **Instalar dependencias de PHP**
```bash
composer install
```

3. **Instalar dependencias de Node.js**
```bash
npm install
npm run build
```

4. **Configurar archivo .env**
```bash
cp .env.example .env
```
Configura tu base de datos y otros parámetros:
```env
APP_URL=http://127.0.0.1:8000
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nombre_base
DB_USERNAME=usuario
DB_PASSWORD=contraseña
```

5. **Generar clave de aplicación**
```bash
php artisan key:generate
```

6. **Ejecutar migraciones y seeders**
```bash
php artisan migrate --seed
```
Esto crea todas las tablas necesarias y carga datos iniciales (roles, usuarios de ejemplo, etc.).

7. **Levantar el servidor**
```bash
php artisan serve
```
El servidor estará disponible en: http://127.0.0.1:8000

---

## 🔹 Acceso al Sistema

### Emprendedor
- **URL:** http://127.0.0.1:8000/emprendedor
- **Función:** Gestión de productos, revisión de ventas y estadísticas
- **Logout:** Botón de cerrar sesión en el header

### Administrador
- **URL:** http://127.0.0.1:8000/admin
- **Función:** Gestión completa de usuarios, productos, ventas y configuración general del sistema
- **Logout:** Botón de cerrar sesión en el header

---

## 🔹 Comandos Útiles

```bash
# Ejecutar tests
php artisan test

# Limpiar cache
php artisan optimize:clear

# Migrar base de datos
php artisan migrate

# Crear nuevo seeder
php artisan make:seeder NombreSeeder
```

---

## 📈 Métricas Sprint 1

- **Velocidad:** 21 SP completados de 21 SP comprometidos
- **Burndown:** En línea ideal al cierre del sprint
- **Cobertura de testing:** 50%
- **Bugs encontrados:** 1 (resuelto)
- **Deuda técnica:** 1 item pendiente

---

## 👥 Equipo Stark-Next

- **Joel Andres** - Scrum Master, Desarrollador Backend
- **Shamir Erick Condori Troche** - Desarrollador Frontend, Diseñador UX
- **Luis Fernando Villca Mamani** - Diseñador UI, Desarrollador Backend
- **Leonardo Fidel Arana Isita** - Diseñador UX, Desarrollador Frontend
- **Danner Alejandro Calle Mamani** - QA Tester

**Product Owner:** Lic. Rosalía Lopez Montalvo