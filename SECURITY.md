# Seguridad

## Versiones con soporte

Solo `master`: Symfony 7.4 LTS (parches de seguridad hasta noviembre de 2029),
PHP 8.4 o superior y Twig 3. Hasta octubre de 2026 el proyecto seguía en
Symfony 3.4, sin parches desde noviembre de 2021, y en Twig 2, sin parches
desde septiembre de 2024 (la última versión, la 2.16.1, es del 9 de
septiembre). Este documento explicaba por qué sus alertas no podían cerrarse
sin migrar; la migración las cerró.

## Cómo se vigilan las dependencias

- `composer audit` contra `composer.lock` en cada push y pull request (CI) y
  cada lunes (`.github/workflows/audit.yml`). Un aviso publicado contra una
  versión que el lock ya fija llega sin ningún commit, y solo la pasada semanal
  se pone en rojo por él.
- Dependabot abre cada semana una PR agrupada con las actualizaciones menores y
  de parche de Composer, y otra con las de las acciones de GitHub. Las acciones
  de terceros van ancladas por SHA; el workflow reutilizable de
  `jaisato/.github` se llama con `@main` a propósito, porque es un repositorio
  propio y esa es la convención de la casa. Symfony se queda en la LTS 7.4: los
  saltos a una versión mayor se ignoran porque son una migración, no una
  actualización.

## Informar de una vulnerabilidad

No abras un issue público. Informa en privado con el formulario de avisos de
seguridad de GitHub:
<https://github.com/jaisato/chess-knight-test/security/advisories/new>.
