# Prueba Técnica - Four Sides Group

Este proyecto fue desarrollado como parte del proceso de selección para la posición de Desarrollador PHP Laravel en Four Sides Group. Implementa las funcionalidades solicitadas en las historias de usuario HUPRU001 (Adjuntar foto de usuario) y HUPRU002 (Recuperar o restablecer contraseña).

## Tecnologías Utilizadas

- PHP 8.2
- Laravel 10.x
- MySQL/MariaDB
- Bootstrap 5
- Docker (opcional)

## Estructura del Proyecto

```
├── app
│   ├── Console
│   │   └── Commands
│   ├── Exceptions
│   ├── Http
│   │   ├── Controllers              # Controladores para gestionar las solicitudes
│   │   └── Middleware               # Middleware para procesamiento de solicitudes
│   ├── Mail                         # Clases para el envío de correos electrónicos
│   ├── Models                       # Modelos de la aplicación
│   ├── Observers                    # Observadores para modelos
│   ├── Policies                     # Políticas de autorización
│   ├── Providers                    # Proveedores de servicios
│   └── Services                     # Servicios de la aplicación
├── bootstrap
│   └── cache
├── config                           # Archivos de configuración
├── database
│   ├── factories                    # Factories para tests
│   ├── migrations                   # Migraciones de base de datos
│   └── seeders                      # Seeders para datos iniciales
├── docker                           # Configuración de Docker
│   ├── mysql
│   └── nginx
├── public
│   ├── build
│   │   └── assets
│   ├── css
│   ├── images
│   │   └── profiles                 # Almacenamiento de imágenes de perfil
│   └── js
├── resources
│   ├── css
│   ├── js
│   └── views
│       ├── auth
│       │   └── passwords            # Vistas para la recuperación de contraseña
│       ├── emails                   # Plantillas de correo electrónico
│       ├── layouts                  # Plantillas maestras para las vistas
│       └── users                    # Vistas para la gestión de usuarios
├── routes                           # Definición de rutas
├── storage
│   ├── app
│   │   ├── public
│   │   │   └── profiles             # Almacenamiento de perfiles de usuario
│   │   └── google-calendar
│   ├── framework
│   │   ├── cache
│   │   ├── sessions
│   │   ├── testing
│   │   └── views
│   └── logs
└── tests
    ├── Feature                      # Pruebas de características
    └── Unit                         # Pruebas unitarias
```

## Rutas Principales

- `/users` - Listado de usuarios (GET)
- `/users/{id}` - Detalle de usuario (GET)
- `/users/{id}/photo` - Formulario para subir foto (GET)
- `/users/{id}/photo` - Guardar foto de usuario (POST)
- `/forgot-password` - Formulario de recuperación de contraseña (GET)
- `/forgot-password` - Enviar correo de recuperación (POST)
- `/verify-code` - Verificar código de recuperación (GET)
- `/verify-code` - Validar código (POST)
- `/reset-password` - Formulario para nueva contraseña (GET)
- `/reset-password` - Actualizar contraseña (POST)

## Controladores

### UserController

Maneja las operaciones relacionadas con usuarios:
- `index()` - Muestra listado de usuarios
- `show($id)` - Muestra detalle de un usuario
- `create()` - Muestra formulario para crear usuario
- `store(Request $request)` - Guarda nuevo usuario
- `edit($id)` - Muestra formulario para editar usuario
- `update(Request $request, $id)` - Actualiza datos de usuario
- `destroy($id)` - Elimina usuario
- `uploadPhotoForm($id)` - Muestra formulario para subir foto
- `uploadPhoto(Request $request, $id)` - Procesa y guarda la foto

### PasswordResetController

Gestiona el proceso de recuperación de contraseña (Historia de Usuario HUPRU002):
- `showForgotForm()` - Muestra formulario inicial
- `sendResetCode(Request $request)` - Envía correo con código de validación
- `showVerifyCodeForm()` - Muestra formulario para ingresar código
- `verifyCode(Request $request)` - Valida el código ingresado
- `showResetForm()` - Muestra formulario para nueva contraseña
- `updatePassword(Request $request)` - Actualiza la contraseña

## Migraciones y Seeders

La migración principal añade campos necesarios a la tabla de usuarios:
- `usuarioEmail` - Correo electrónico del usuario
- `usuarioPassword` - Contraseña del usuario
- `profile_photo_path` - Ruta a la imagen de perfil

## Credenciales de Prueba

| Usuario | Correo | Contraseña |
|---------|--------|------------|
| admin   | admin@mail.com | admin123 |
| user    | user@mail.com | admin123 |

## Instalación

### Opción 1: Con Docker

1. Clonar el repositorio:
   ```bash
   git clone https://github.com/Xaviierkasvar/four-sides-group-prueba.git
   cd four-sides-group-prueba
   ```

2. Crear archivo `.env`:
   ```bash
   cp .env.example .env
   ```

3. Iniciar los contenedores:
   ```bash
   docker-compose up --build -d 
   ```

4. Acceder a la aplicación en http://localhost:8000

### Opción 2: Sin Docker

1. Clonar el repositorio:
   ```bash
   git clone https://github.com/Xaviierkasvar/four-sides-group-prueba.git
   cd four-sides-group-prueba
   ```

2. Crear archivo `.env`:
   ```bash
   cp .env.example .env
   ```

3. Configurar variables de entorno en el archivo `.env` con tus credenciales de base de datos y correo.

4. Instalar dependencias:
   ```bash
   composer install
   ```

5. Generar clave de aplicación:
   ```bash
   php artisan key:generate
   ```

6. Ejecutar migraciones y seeders:
   ```bash
   php artisan migrate --seed
   ```

7. Crear enlace simbólico para almacenamiento:
   ```bash
   php artisan storage:link
   ```

8. Iniciar servidor de desarrollo:
   ```bash
   php artisan serve
   ```

9. Acceder a la aplicación en http://localhost:8000

## Notas Importantes

- Las fotos de perfil se almacenan en la carpeta `public/profile_photos`
- Los códigos de verificación para restablecer contraseña tienen una duración de 15 minutos
- La contraseña debe tener al menos 8 caracteres según los requisitos

## Contacto

Para cualquier consulta sobre este proyecto, contactar a:

Nombre: Francisco Javier Castillo Barrios
Email: javier_castillo_15@hotmail.es