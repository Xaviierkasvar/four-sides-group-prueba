#!/bin/bash

# Ejecutar migraciones y seeders
php artisan migrate:refresh --seed

# Iniciar el servidor
php artisan serve --host=0.0.0.0 --port=8000