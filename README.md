# Camino más corto del caballo

Calcula el camino más corto de un caballo de ajedrez entre dos casillas del
tablero con una búsqueda en anchura. Es una prueba técnica de 2017 con
arquitectura DDD (dominio, aplicación e infraestructura), migrada en 2026 a
**Symfony 7.4 LTS, PHP 8.4 y Twig 3**.

## Requisitos

- PHP 8.4 o superior, con las extensiones `ctype` e `iconv`.
- Composer 2.

## Instalación y uso

```bash
git clone https://github.com/jaisato/chess-knight-test.git
cd chess-knight-test
composer install
php -S 127.0.0.1:8000 -t public   # o `symfony serve`
```

Abre <http://127.0.0.1:8000/?source=0&destination=63>. La página muestra la
solución en JSON: las casillas por las que pasa el caballo y el número de
movimientos (`"totalMoves":6` en el ejemplo).

Parámetros del query string, todos opcionales:

| Parámetro | Por defecto | Qué es |
|---|---|---|
| `source` | `0` | Casilla de origen, de 0 a 63: `x = n % 8`, `y = n ÷ 8` (0 es a1 y 63 es h8). |
| `destination` | `63` | Casilla de destino, con la misma numeración. |
| `boardId`, `knightId` | — | Ids de un tablero y un caballo existentes. Sin ellos, cada petición crea los suyos. |

Respuestas:

- **200** con la solución.
- **400** si una casilla está fuera del tablero o no es un entero, o si un id
  llega como array (`?knightId[]=x`).
- **404** si `boardId` o `knightId` no existen. Los repositorios viven en
  memoria y duran lo que dura la petición, así que un id inventado siempre
  responde 404.

## Arquitectura

```
src/
├── Domain/          Tablero, casillas, caballo y sus movimientos; el servicio de dominio
│                    con la búsqueda en anchura y el evento NewShortestPathFound
├── Application/     El caso de uso GetMinimumNumberOfMovesService, su petición y el DTO
└── Infrastructure/  El controlador web, los repositorios en memoria y el listener que
                     registra cada solución en el log
templates/           Plantillas Twig
```

- `config/services.yaml` resuelve los repositorios que declara el dominio
  (`KnightRepository`, `BoardRepository`) con las implementaciones en memoria.
  El dominio no se registra en el contenedor: se construye con `new`.
- Cada solución dispara `NewShortestPathFound`. `LogNewShortestPathFoundListener`
  (`#[AsEventListener]`) la deja en el log (`var/log/dev.log` en desarrollo).
- La ruta se declara con `#[Route]` en `KnightController`, que recibe
  `Twig\Environment` por el constructor.

## Calidad

```bash
composer check          # todo lo que ejecuta el CI, en orden
composer test           # PHPUnit 12: suites unit y functional
composer stan           # PHPStan, nivel max
composer cs             # php-cs-fixer en modo comprobación (cs:fix corrige)
composer lint           # lint del contenedor, de la configuración YAML y de Twig
```

El CI (`.github/workflows/ci.yml`) usa el workflow reutilizable
[`symfony-ci`](https://github.com/jaisato/.github) de `jaisato/.github`:
`composer validate --strict`, `php -l` y la suite en PHP 8.4 y 8.5,
php-cs-fixer, PHPStan, los lints, una cobertura mínima del 80 % y
`composer audit`. `audit.yml` repite la auditoría cada lunes y Dependabot
propone cada semana las actualizaciones de Composer y de las acciones. Ver
[`SECURITY.md`](SECURITY.md).

## Historia

El código original es de 2017, sobre Symfony 3.3, Twig 2 y PHP 7. Sus
dependencias acumulaban 35 alertas de Dependabot que solo se cerraban saliendo
de Symfony 3.4 y Twig 2, que ya no reciben parches. La migración partió de un
esqueleto nuevo de Symfony 7.4 en lugar de ir saltando de versión en versión:
el bundle `ChessBundle` pasó a ser `src/`, y Doctrine, Swiftmailer, la
configuración de seguridad y los bundles `sensio/*`, que la aplicación no
usaba, se quedaron fuera.

La última versión sobre Symfony 3.4 es el commit `7ff5835`.
