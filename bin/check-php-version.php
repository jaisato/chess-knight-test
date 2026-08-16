<?php

/**
 * Comprueba la versión de PHP **real**, no la que finge `config.platform`.
 *
 * `composer.json` fija `config.platform.php` para que la resolución sea
 * reproducible: sin ello, `composer update` depende del intérprete de quien lo
 * ejecute y falla directamente en un PHP moderno. El efecto secundario es que
 * Composer valida todas las restricciones -incluida `require.php`- contra ese
 * valor sintético, así que una instalación en un PHP fuera del rango soportado
 * se completa sin una sola queja y revienta después, al ejecutar.
 *
 * Este script cierra ese hueco: corre sobre el intérprete de verdad y aborta
 * antes de instalar nada.
 */

declare(strict_types=1);

const MINIMUM = 70205; // 7.2.5
const MAXIMUM = 80000; // < 8.0

if (PHP_VERSION_ID >= MINIMUM && PHP_VERSION_ID < MAXIMUM) {
    exit(0);
}

fwrite(STDERR, sprintf(
    "%s  PHP no soportado: el intérprete es %s.%s"
    . "  Este proyecto necesita >= 7.2.5 y < 8.0.%s%s"
    . "  El conjunto bloqueado incluye Symfony 3.4 y doctrine/doctrine-cache-bundle,%s"
    . "  que no funcionan en PHP 8. `config.platform.php` finge un 7.2.5 para que el%s"
    . "  composer.lock sea reproducible, de modo que Composer por sí solo no lo detecta.%s%s"
    . "  Ver SECURITY.md.%s",
    PHP_EOL,
    PHP_VERSION,
    PHP_EOL,
    PHP_EOL,
    PHP_EOL,
    PHP_EOL,
    PHP_EOL,
    PHP_EOL,
    PHP_EOL,
    PHP_EOL
));

exit(1);
