# Sistema de Pacientes — Laravel 11 (proyecto independiente)

Aplicación completa para captura y seguimiento de pacientes. Esta versión **sí incluye la estructura base necesaria para instalar Laravel desde cero**.

## Requisitos
- PHP 8.2+
- Composer 2+
- MySQL 8+ o MariaDB compatible
- Extensiones PHP: PDO, pdo_mysql, Mbstring, OpenSSL, Tokenizer, XML, Ctype, JSON, BCMath, Fileinfo

## Instalación en Windows/XAMPP
1. Descomprimir el ZIP en `D:\Xampp_old\htdocs\salud` o en otra carpeta.
2. Abrir CMD en esa carpeta.
3. Ejecutar `composer install`.
4. Ejecutar `copy .env.example .env`.
5. Ejecutar `php artisan key:generate`.
6. Crear una base MySQL llamada `pacientes`.
7. Verificar `.env`:
   `DB_CONNECTION=mysql`, `DB_HOST=127.0.0.1`, `DB_PORT=3306`, `DB_DATABASE=pacientes`, `DB_USERNAME=root`, `DB_PASSWORD=`.
8. Ejecutar `php artisan migrate --seed`.
9. Ejecutar `php artisan serve`.
10. Abrir `http://127.0.0.1:8000`.

## Usuarios
- Capturista: `capturista@example.com` / `password`
- Visor: `visor@example.com` / `password`

## Roles
- Capturista: captura, modifica expedientes, registra seguimientos y genera reportes.
- Visor: consulta y genera reportes.

La interfaz de captura es responsive y mobile-first.
