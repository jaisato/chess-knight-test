# Estado de seguridad de las dependencias

## Qué se ha corregido

`composer audit` informaba de **59 advisories en 4 paquetes**. Actualizando
dentro de las ramas compatibles quedan **41 en 6 paquetes**:

| Paquete | Antes | Ahora | Efecto |
|---------|-------|-------|--------|
| `symfony/symfony` | v3.3.13 | v3.4.49 | 39 → 21 advisories |
| `twig/twig` | v2.4.4 | v2.16.1 | 18 → 15 advisories |
| `symfony/swiftmailer-bundle` | ^2.3.10 | ^2.6.7 | — |

3.4.49 es la **última** versión publicada de la rama 3.4, y 2.16.1 la última de
Twig 2.x. No hay nada más que actualizar sin cambiar de versión mayor.

## Por qué quedan 41

Los avisos restantes afectan a **toda** la rama 3.x de Symfony y a **toda** la
2.x de Twig. Por ejemplo, CVE-2026-48489 (severidad alta) se corrige en 5.4.53,
6.4.41 y 7.4.13: no existe versión 3.x corregida. Symfony 3.4 dejó de recibir
soporte de seguridad en **noviembre de 2021** y Twig 2.x está igualmente
descontinuada.

Dicho de otro modo: **estas 41 no se pueden cerrar actualizando**. La única
corrección real es migrar a una rama con soporte (Symfony 6.4 LTS o 7.x, Twig
3.x), que en este proyecto implica reescribir la estructura de la aplicación
-`AppKernel`, `app/config/*.yml`, los bundles `sensio/*` que están abandonados-
y no es un cambio de dependencias.

Mientras tanto conviene tratar este repositorio como lo que es: **código de
2017, de una prueba técnica, no apto para exponer en producción.**

## Versión de PHP

`composer.json` declara ahora:

- `require.php = ">=7.1.3 <8.0"`
- `config.platform.php = "7.1.3"`

Los dos coinciden **a propósito**. `platform` hace que Composer resuelva como si
el intérprete fuese esa versión, así que si `require.php` admitiera menos que
`platform` (declaraba `>=5.5.9`), una instalación sobre PHP 5.5–7.1.2 pasaría la
verificación de plataforma y luego fallaría en ejecución: el conjunto bloqueado
incluye Twig 2.16.1, que exige PHP >= 7.1.3.

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
