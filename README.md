<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Sistema de videovigilancia

El panel permite registrar, editar y eliminar cámaras autenticadas. La vista reproduce transmisiones HLS; las direcciones RTSP se convierten con MediaMTX. El estado de la cámara se selecciona manualmente y no es una comprobación automática de conexión.

### Conectar una cámara RTSP para una demostración local

Requisitos: Docker Desktop (con Compose), una cámara IP y una URL RTSP accesible desde la computadora que ejecuta el proyecto.

1. Ejecuta las migraciones:

   ```powershell
   php artisan migrate
   ```

2. En el archivo `.env` local del proyecto, configura la fuente RTSP de MediaMTX. No compartas ni subas ese archivo al repositorio:

   ```dotenv
   CAMERA_RTSP_URL=rtsp://USUARIO:CONTRASENA@IP_DE_LA_CAMARA:554/RUTA_DEL_STREAM
   HLS_PORT=8888
   ```

   Sustituye los valores de ejemplo por los datos de la cámara. Si usuario o contraseña contienen caracteres especiales, codifícalos para URL (percent-encoding).

3. Inicia la pasarela desde la raíz del proyecto:

   ```powershell
   docker compose up -d mediamtx
   ```

   MediaMTX publica el stream como `http://localhost:8888/camara/index.m3u8`. Para detenerla:

   ```powershell
   docker compose down
   ```

4. Inicia Laravel (`php artisan serve`), inicia sesión y abre **Videovigilancia > Agregar cámara**. Registra el nombre y la URL HLS `http://localhost:8888/camara/index.m3u8`. Guarda y pulsa reproducir.

Si la transmisión no aparece, confirma primero que la cámara es accesible desde esa computadora, revisa `docker compose logs mediamtx` y prueba la URL HLS en el navegador. HLS puede tardar unos segundos en empezar. Esta configuración de MediaMTX usa una fuente RTSP a la vez; para varias cámaras RTSP se deben definir paths adicionales en `mediamtx.yml` y asignar a cada cámara su URL HLS correspondiente.

Para una cámara que ya ofrece HLS, no necesitas MediaMTX: registra directamente su URL `https://...m3u8`. Si se accede desde otra computadora en la red local, inicia Laravel con `php artisan serve --host=0.0.0.0`, sustituye `localhost` por el nombre/IP del servidor en la URL HLS y asegúrate de que la red y el firewall permitan los puertos de Laravel y HLS. Esta configuración de demostración no habilita autenticación en la pasarela; úsala en una red de confianza, no expongas el puerto a Internet y no incluyas credenciales reales en archivos versionados.

### Guía paso a paso para usar y presentar el módulo

#### 1. Preparar el proyecto

En la computadora donde se vaya a probar, instala PHP compatible con Laravel 12, Composer y configura una base de datos en el archivo `.env` local. Para convertir una cámara RTSP también se requiere Docker Desktop con Docker Compose.

Desde PowerShell, abre la carpeta del proyecto y ejecuta:

```powershell
composer install
php artisan key:generate
php artisan migrate
```

Para crear el usuario de demostración que define `DatabaseSeeder`, ejecuta:

```powershell
php artisan db:seed
```

En una instalación limpia, ese seeder crea `test@example.com` con la contraseña `password`. Úsala solo para la demostración y cámbiala antes de cualquier despliegue. La cuenta `admin@admin.com` creada manualmente en la base de datos de otra computadora no se incluye al descargar el código. Las cámaras ficticias se pueden cargar opcionalmente con `php artisan db:seed --class=CameraSeeder`; sus direcciones son de ejemplo y no conectan dispositivos reales.

#### 2. Iniciar sesión y recorrer el panel

Inicia Laravel:

```powershell
php artisan serve
```

Abre `http://127.0.0.1:8000/login`, inicia sesión y selecciona **Videovigilancia** en el menú lateral, o abre `http://127.0.0.1:8000/surveillance`. Pulsa **Agregar cámara** para crear un registro; puedes editarlo o eliminarlo desde su tarjeta. El estado se selecciona manualmente: el sistema no comprueba automáticamente si la cámara está conectada.

#### 3. Conectar una cámara RTSP

Se necesita una cámara IP encendida y accesible por la red desde la computadora que ejecuta MediaMTX. Consulta el manual/configuración de la cámara para obtener su URL RTSP. Suele seguir este formato:

```text
rtsp://USUARIO:CONTRASENA@IP_DE_LA_CAMARA:554/RUTA_DEL_STREAM
```

Reemplaza cada componente por los valores reales del dispositivo; el puerto y la ruta dependen del fabricante. No pegues la URL RTSP en el formulario web ni la compartas, porque puede contener la contraseña.

En el archivo `.env` local, agrega la URL RTSP y el puerto HLS:

```dotenv
CAMERA_RTSP_URL=rtsp://USUARIO:CONTRASENA@IP_DE_LA_CAMARA:554/RUTA_DEL_STREAM
HLS_PORT=8888
```

Si usuario o contraseña contienen caracteres especiales, codifícalos para URL (percent-encoding). No incluyas credenciales reales en archivos versionados ni en materiales de presentación.

Inicia la pasarela desde la raíz del proyecto:

```powershell
docker compose up -d mediamtx
```

MediaMTX transforma la transmisión para que el navegador la pueda reproducir en:

```text
http://localhost:8888/camara/index.m3u8
```

En **Videovigilancia > Agregar cámara**, completa el nombre y demás datos; en **URL de transmisión HLS**, registra la URL anterior (no la URL RTSP). Guarda y pulsa reproducir. El video puede tardar unos segundos en comenzar.

Para diagnosticar problemas, revisa los registros de MediaMTX:

```powershell
docker compose logs mediamtx
```

Comprueba también que Docker está iniciado, la dirección RTSP y las credenciales son correctas, y que la computadora puede alcanzar la cámara. Para detener la pasarela:

```powershell
docker compose down
```

La configuración proporcionada tiene un path `camara` y acepta **una fuente RTSP a la vez**. Para varias cámaras RTSP, hay que definir paths adicionales en `mediamtx.yml` y guardar la URL HLS de cada path en su registro. Si una cámara ya ofrece HLS, no hace falta MediaMTX: registra directamente su URL `.m3u8`.

#### 4. Acceso desde otro dispositivo de la misma red

`localhost` se refiere al dispositivo donde se abre el navegador. Si otra computadora de la red va a ver el video, inicia Laravel con `php artisan serve --host=0.0.0.0` y guarda en el registro HLS una URL que use la IP o el nombre del servidor, por ejemplo `http://IP_DEL_SERVIDOR:8888/camara/index.m3u8`. Configura la red y el firewall para permitir los puertos necesarios; el servidor debe poder acceder a la cámara.

#### Alcance y seguridad

Esta integración sirve para una demostración local o una red de confianza. MediaMTX no tiene autenticación HLS configurada en esta plantilla; no expongas directamente sus puertos ni los de la cámara a Internet. Un uso real requiere HTTPS, control de roles, autorización de usuarios y protección/gestión segura de credenciales. La pantalla de estado no detecta automáticamente fallos de conexión.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
