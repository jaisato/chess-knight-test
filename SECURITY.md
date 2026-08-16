# Estado de seguridad de las dependencias

## Qué se ha corregido

`composer audit` informaba de **59 advisories en 4 paquetes**. Actualizando
dentro de las ramas compatibles quedan **38 en 3 paquetes**:

| Paquete | Antes | Ahora | Efecto |
|---------|-------|-------|--------|
| `symfony/symfony` | v3.3.13 | v3.4.49 | 39 → 21 advisories |
| `twig/twig` | v2.4.4 | v2.16.1 | 18 → 15 advisories |
| `swiftmailer/swiftmailer` | v5.4.12 | v6.3.0 | 1 → **0** |
| `symfony/swiftmailer-bundle` | v2.6.7 | v3.3.1 | — (necesario para lo anterior) |
| `symfony/http-client` | v4.4.51 | v5.4.53 | 1 → **0** |
| `symfony/polyfill-intl-idn` | v1.30.0 | v1.38.1 | 1 → **0** |
| `symfony/mime` | v4.4.47 | v5.4.13 | 2 → 2 (ver abajo) |

3.4.49 es la **última** versión publicada de la rama 3.4, y 2.16.1 la última de
Twig 2.x.

## Los 38 que quedan, paquete por paquete

### `symfony/symfony` (21) y `twig/twig` (15) — sin corrección posible

Afectan a **toda** la rama 3.x de Symfony y a **toda** la 2.x de Twig. Por
ejemplo, CVE-2026-48489 (severidad alta) se corrige en 5.4.53, 6.4.41 y 7.4.13:
no existe versión 3.x corregida. Symfony 3.4 dejó de recibir soporte de
seguridad en **noviembre de 2021** y Twig 2.x está igualmente descontinuada.

**No se pueden cerrar actualizando.** La única corrección real es migrar a una
rama con soporte (Symfony 6.4 LTS o 7.x, Twig 3.x), lo que en este proyecto
implica reescribir la estructura de la aplicación -`AppKernel`,
`app/config/*.yml`, los bundles `sensio/*` abandonados- y no es un cambio de
dependencias.

### `symfony/mime` (2) — bloqueado por el monolito

CVE-2026-45067 (alta) y CVE-2026-45070 (media) se corrigen en 5.4.52. La
actualización **no** es posible aquí:

```
symfony/mime v5.4.52 conflicts with symfony/serializer <5.4.35
(symfony/symfony v3.4.49 replaces symfony/serializer self.version)
```

Es decir: el paquete monolítico `symfony/symfony` sustituye a `symfony/serializer`
por su propia versión 3.4.49, y mime 5.4.52 declara conflicto con cualquier
serializer anterior a 5.4.35. Se ha llegado hasta 5.4.13, que es lo máximo antes
del conflicto. Sale con la misma migración que los dos anteriores.

`symfony/mime` no lo usa el código de la aplicación: entra a través de
`sensiolabs/security-checker` (ver abajo).

### De dónde venían los otros tres

`sensio/distribution-bundle` → `sensiolabs/security-checker` → `symfony/http-client`
+ `symfony/mime` → `symfony/polyfill-intl-idn`.

Vale la pena señalarlo: **cuatro de los cinco advisories ajenos al framework
entraban por `sensiolabs/security-checker`**, una herramienta abandonada cuya
función -avisar de dependencias vulnerables- la hace hoy `composer audit`, que
viene incluido en Composer. Eliminar `sensio/distribution-bundle` se llevaría por
delante toda esa rama del árbol, pero es parte del esqueleto de la aplicación
Symfony 3 y su retirada pertenece también a la migración.

Mientras tanto conviene tratar este repositorio como lo que es: **código de
2017, de una prueba técnica, no apto para exponer en producción.**

## Versión de PHP

`composer.json` declara ahora:

- `require.php = ">=7.2.5 <8.0"`
- `config.platform.php = "7.2.5"`

Los dos coinciden **a propósito**. `platform` hace que Composer resuelva como si
el intérprete fuese esa versión, así que si `require.php` admitiera menos que
`platform` (declaraba `>=5.5.9`), una instalación sobre PHP 5.5–7.1.2 pasaría la
verificación de plataforma y luego fallaría en ejecución: el conjunto bloqueado
incluye Twig 2.16.1 (>= 7.1.3) y, tras esta ronda, `symfony/http-client` 5.4.53
y `symfony/mime` 5.4.13 (>= 7.2.5).

El suelo subió de 7.1.3 a **7.2.5** a propósito: era 7.1.3 lo que impedía
actualizar `symfony/polyfill-intl-idn` (1.38.1 exige >= 7.2) y `symfony/http-client`
(5.4.x exige >= 7.2.5), es decir, dos de los advisories que ahora están cerrados.
Estrechar el rango declarado a cambio de cerrarlos es un intercambio razonable
en un proyecto que de todas formas no es desplegable: PHP 7.1 dejó de recibir
soporte en diciembre de 2019.

7.1.3 es el suelo real del `composer.lock`, calculado como el mayor de los
mínimos que declaran los paquetes bloqueados.

El **techo** hace falta por el mismo motivo, en el otro sentido. Sin `<8.0` un
PHP 8.x quedaba declarado como soportado y, como `platform` finge un 7.1.3, la
instalación se completaba sin avisar — pero el conjunto bloqueado no funciona
ahí. `composer check-platform-reqs --lock` sobre PHP 8.4 lo confirma:

```
php   8.4.19   doctrine/doctrine-cache-bundle requires php (^7.1)   failed
```

El rango declarado coincide ahora con lo que el lock puede ejecutar realmente
por los dos extremos.

Fijar `platform` sigue haciendo falta para que el lock sea reproducible en
cualquier máquina: sin ello la resolución depende del PHP de quien ejecute
`composer`, y en un PHP 8.4 moderno `composer update` falla directamente.

## Paquetes abandonados

`sensio/distribution-bundle`, `sensio/framework-extra-bundle`,
`sensio/generator-bundle`, `sensiolabs/security-checker`,
`swiftmailer/swiftmailer`, `symfony/swiftmailer-bundle`,
`doctrine/doctrine-cache-bundle`, `doctrine/reflection`, `doctrine/annotations`,
`doctrine/cache` y `composer/package-versions-deprecated` están todos
abandonados. Sustituirlos forma parte de la misma migración.
