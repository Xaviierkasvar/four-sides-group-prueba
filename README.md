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
│   ├── Exceptions
│   ├── Http
│   │   ├── Controllers
│   │   │   ├── ForgotPasswordController.php   # Controlador para restablecimiento de contraseña
│   │   │   ├── UserController.php                 # Controlador para gestión de usuarios
│   │   │   └── ProfileController.php              # Controlador para gestión de perfiles
│   │   ├── Middleware
│   │   └── Requests
│   │       └── UserPhotoRequest.php               # Validaciones para carga de fotos
│   ├── Mail
│   │   └── ResetPasswordMail.php                  # Email para recuperación de contraseña
│   └── Models
│       └── User.php                              # Modelo de usuario
├── config
├── database
│   ├── migrations
│   │   └── 2023_xx_xx_create_users_table.php     # Migración para tabla de usuarios
│   └── seeders
│       └── UserSeeder.php                        # Seeder para usuarios de prueba
├── public
│   └── profile_photos                            # Carpeta donde se almacenan las fotos
├── resources
│   ├── css
│   ├── js
│   └── views
│       ├── auth
│       │   ├── forgot-password.blade.php         # Vista para solicitar restablecimiento
│       │   ├── reset-password.blade.php          # Vista para ingresar nueva contraseña
│       │   └── verify-code.blade.php             # Vista para verificar código
│       └── users
│           ├── index.blade.php                   # Lista de usuarios
│           ├── show.blade.php                    # Detalle de usuario
│           └── upload-photo.blade.php            # Formulario para cargar foto
├── routes
│   ├── api.php
│   └── web.php                                   # Definición de rutas
├── docker-compose.yml                            # Configuración de Docker
├── Dockerfile                                    # Instrucciones para construir la imagen
└── entrypoint.sh                                 # Script de entrada para Docker
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

Maneja las operaciones CRUD relacionadas con usuarios:
- `index()` - Muestra listado de usuarios
- `show($id)` - Muestra detalle de un usuario
- `uploadPhotoForm($id)` - Muestra formulario para subir foto
- `uploadPhoto(UserPhotoRequest $request, $id)` - Procesa y guarda la foto

### ForgotPasswordController

Gestiona el proceso de recuperación de contraseña:
- `showForgotPasswordForm()` - Muestra formulario inicial
- `sendResetLink(Request $request)` - Envía correo con código
- `showVerifyCodeForm()` - Muestra formulario para ingresar código
- `verifyCode(Request $request)` - Valida el código ingresado
- `showResetPasswordForm()` - Muestra formulario para nueva contraseña
- `resetPassword(Request $request)` - Actualiza la contraseña

## Migraciones y Seeders

La migración principal añade campos necesarios a la tabla de usuarios:
- `usuarioEmail` - Correo electrónico del usuario
- `usuarioPassword` - Contraseña del usuario
- `profile_photo_path` - Ruta a la imagen de perfil

El seeder (`UserSeeder.php`) crea usuarios de prueba para facilitar la evaluación.

## Credenciales de Prueba

| Usuario | Correo | Contraseña |
|---------|--------|------------|
| admin   | admin@mail.com | admin123 |
| user   | user@mail.com | admin123 |

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

3. Configurar variables de entorno en el archivo `.env`:
   ```
   DB_CONNECTION=mysql
   DB_HOST=db
   DB_PORT=3306
   DB_DATABASE=fsg_prueba
   DB_USERNAME=root
   DB_PASSWORD=
   
   MAIL_MAILER=smtp
   MAIL_HOST=mailhog
   MAIL_PORT=1025
   MAIL_USERNAME=null
   MAIL_PASSWORD=null
   MAIL_ENCRYPTION=null
   MAIL_FROM_ADDRESS=no-reply@example.com
   ```

4. Iniciar los contenedores:
   ```bash
   docker-compose up -d
   ```

5. Acceder al shell del contenedor:
   ```bash
   docker-compose exec app bash
   ```

6. Dentro del contenedor, instalar dependencias y configurar la aplicación:
   ```bash
   composer install
   php artisan key:generate
   php artisan migrate --seed
   php artisan storage:link
   ```

7. Acceder a la aplicación en http://localhost:8000

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