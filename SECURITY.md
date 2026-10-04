# Seguridad

## Versiones con soporte

Solo `master`: Symfony 7.4 LTS (parches de seguridad hasta noviembre de 2029),
PHP 8.4 o superior y Twig 3. Hasta octubre de 2026 el proyecto seguía en
Symfony 3.4 y Twig 2, sin parches desde 2021. Este documento explicaba por qué
sus alertas no podían cerrarse sin migrar; la migración las cerró.

## Cómo se vigilan las dependencias

- `composer audit` contra `composer.lock` en cada push y pull request (CI) y
  cada lunes (`.github/workflows/audit.yml`). Un aviso publicado contra una
  versión que el lock ya fija llega sin ningún commit, y solo la pasada semanal
  se pone en rojo por él.
- Dependabot abre cada semana una PR agrupada con las actualizaciones menores y
  de parche de Composer, y otra con las de las acciones de GitHub, que van
  ancladas por SHA. Symfony se queda en la LTS 7.4: los saltos a una versión
  mayor se ignoran porque son una migración, no una actualización.

## Informar de una vulnerabilidad

No publiques los detalles en un issue. Abre uno que diga solo que quieres
informar de un problema de seguridad, y se acordará un canal privado.
