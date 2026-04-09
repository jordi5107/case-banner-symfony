
# Módulo Banner

Este módulo desacopla la funcionalidad de banners propia de PrestaShop.

## Descripción

El proyecto consta de una entidad madre `Banner` de la cual cuelga `BannerTranslation`, que estas son entidades traducidas. Se ha optado por esta estructura ya que, a nivel de escalabilidad, era necesaria una entidad separada para las traducciones, dado que de cada banner, como se vio en los requisitos, debe poder existir más de una traducción.

Para gestionar los banners desde el backend se ha optado por implementar un panel de administración mediante **EasyAdmin** por su rapidez y robustez. El propio módulo también funciona como API, ya que dependiendo de las necesidades finales se podría utilizar un iframe directamente en PrestaShop, o un frontal en PrestaShop que consuma la API.

Para el frontal se han definido dos rutas:
- Una opción **API** que devuelve un JSON con los banners activos.
- Una opción que **renderiza el HTML** directamente con el banner.

Además, el módulo está **dockerizado** para facilitar su desarrollo y mantenimiento, agilizando el entorno local.

Por último, se han añadido **pruebas unitarias** con PHPUnit y seeders en las tablas para facilitar el testing.

## Como se integrara en prestashop?

El proyecto expone dos rutas que cubren estrategias de integración distintas. La elección depende del grado de acoplamiento tolerable con PrestaShop.

### Opción A — Consumo de la API JSON

La ruta `GET /api/banners?lang={locale}` devuelve un array de banners activos en JSON.

Un módulo PrestaShop mínimo implementaría el hook `displayHome` realizando una llamada HTTP interna a esta API  y renderizaría el resultado con su propia propia logica del frontal.

---

### Opción B — Embed via iframe

En lugar de devolver datos en bruto (JSON), esta ruta devuelve un fragmento de HTML ya procesado y listo para ser visualizado.

La ruta GET /banners/render?lang={locale} renderiza el fragmento HTML de los banners activos, filtrados por fecha e idioma y con sus estilos incluidos.

El hook displayHome de PrestaShop consume este HTML mediante una petición de servidor e inyecta el contenido directamente en el DOM.

### Gestión (backoffice)

El panel `/admin` de EasyAdmin (Banner Manager) funciona como herramienta **independiente**, con su propio formulario de login en `/login`. El acceso está protegido mediante `form_login` de Symfony con sesión y logout nativos.

**No es necesario integrarlo dentro del backoffice de PrestaShop.** El equipo de puede acceder directamente a la URL del panel.

Si se quisiera enlazar desde el backoffice de PrestaShop, la opción recomendada es un **enlace externo** al panel de Symfony, no un iframe. Los navegadores bloquean las cookies de sesión en iframes entre dominios distintos.

Tambien existe la possiblidad de crear laa gestion del modulo via api, lo unico que esta opcion requeriría construir endpoints CRUD completos que aún no existen, más una interfaz propia en el backoffice de PrestaShop que los consuma.


## Cómo ejecutar en local

### Requisitos previos

- Docker instalado.

### Pasos

**1. Clonar el repositorio**

```bash
git clone <url-del-repo>
cd <carpeta-creada>
```

**2. Levantar los contenedores**

```bash
docker compose up -d --build
```

Esto levanta tres servicios.  
El entrypoint instala automáticamente las dependencias de Composer si no existen.

> **⚠️ Importante:** El `composer install` se ejecuta dentro del contenedor al arrancar. Antes de continuar, espera a que aparezca el mensaje `ready to handle connections` en los logs:
> ```bash
> docker compose logs app -f
> ```

**3. Ejecutar las migraciones**

```bash
docker exec case-banner-symfony-app-1 php bin/console doctrine:migrations:migrate --no-interaction
```

**4. Cargar los datos de ejemplo**

```bash
docker exec case-banner-symfony-app-1 php bin/console doctrine:fixtures:load --no-interaction
```

### URLs disponibles

| URL | Descripción |
|---|---|
| `http://localhost:8080/admin` | Panel de administración (EasyAdmin) — requiere login |
| `http://localhost:8080/banners?lang=es` | Vista frontend con banners activos (pública) |
| `http://localhost:8080/api/banners?lang=es` | API JSON con banners activos (pública) |

El parámetro `lang` permite escoger el idioma de los banners.

#### Credenciales del panel de administración

| Campo | Valor |
|---|---|
| Usuario | `admin` |
| Contraseña | `admin` |

> **Nota:** La contraseña está almacenada con hash bcrypt en `config/packages/security.yaml`. Para cambiarla, genera un nuevo hash con:
> ```bash
> docker exec case-banner-symfony-app-1 php bin/console security:hash-password
> ```
> y sustituye el valor del campo `password` en `config/packages/security.yaml`. En producción se recomienda sustituir el proveedor en memoria por uno de base de datos.

### Ejecutar los tests

```bash
docker exec case-banner-symfony-app-1 php bin/phpunit
```

## Arquitectura

### Modelo de datos

```
Banner
  ├── id
  ├── internalName
  ├── backgroundColor
  ├── startDate
  ├── endDate
  ├── active
  └── translations (OneToMany) ──► BannerTranslation
                                        ├── id
                                        ├── content
                                        ├── locale (ManyToOne) ──► Locale
                                        └── banner

Locale
  ├── id
  ├── code  (ej: "es", "en", "fr")
  └── active
```

- Un `Banner` puede tener múltiples traducciones (`BannerTranslation`), una por idioma (`Locale`).
- La activación de un banner está controlada por tres condiciones: el flag `active`, `startDate` y `endDate`, lo que permite programar campañas con fecha de inicio y fin.
- Los `Locale` también tienen un flag `active`, permitiendo habilitar/deshabilitar idiomas de forma independiente.

### Capas de la aplicación

El proyecto sigue una arquitectura en capas estándar de Symfony, pero se podria restructurar para ser con otro tipo de arquitectura como hexagonal

### Flujo de una petición API

```
HTTP GET /api/banners?lang=es
        │
        ▼
  BannerController (Api)
        │  delega con locale
        ▼
  GetActiveBannerService
        │  consulta banners activos en fecha y locale
        ▼
  BannerRepository → Base de datos
        │  devuelve entidades Banner con translations
        ▼
  Mapeo a BannerResponseDTO[]
        │
        ▼
  JSON Response { status, banners[] }
```

### Contenedorización

La aplicación está dockerizada con los siguientes servicios:
- **PHP-FPM** — ejecuta la aplicación Symfony.
- **Nginx** — servidor web y proxy inverso.
- **Base de datos** (definida en `compose.yaml`).

Los entornos de desarrollo y producción se diferencian mediante `compose.override.yaml`.

### Testing

Las pruebas están organizadas bajo `tests/Unit/` con la misma estructura de carpetas que `src/`, separando tests de `Controller` y de `Service`. Se utilizan fixtures (`AppFixtures`) para poblar la base de datos en entornos de test y desarrollo.



