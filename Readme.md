
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



