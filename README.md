Chess knight's shortest path (Another Symfony project).
=====

It calculates the Knight's shortest path from source to destination box on chessboard (using a breadth-first search).

Instructions:
- Requirements: Git, **PHP >= 7.2.5 and < 8.0**, composer

  The locked dependency set (Symfony 3.4, `doctrine/doctrine-cache-bundle`) does
  not run on PHP 8. `composer.json` pins `config.platform.php` so the lock file
  resolves reproducibly, which also means Composer validates against that
  synthetic version rather than your interpreter - so a `composer install` on an
  unsupported PHP would otherwise succeed and fail later. `bin/check-php-version.php`
  runs on the real interpreter before install/update and stops that. See
  `SECURITY.md`.
- Clone repository: **git clone git@github.com:jaisato/chess-knight-test.git**
- Install a current Composer 2 (https://getcomposer.org/download/) and execute *"composer install"* to install project dependencies. The repository no longer ships a `composer.phar`: the one it carried was a 1.6-dev snapshot from 2017, affected by several published Composer advisories and unable to honour `config.allow-plugins`.
- Run PHP server: **php bin/console server:run (or server:start)**
- Open browser and go to home page http://localhost:8000/. Optional querystring parameters: **source** (int, 0-63), **destination** (int, 0-63); an off-board value answers 400. Example: http://localhost:8000/?source=0&destination=1
- Run unit tests: **php vendor/bin/simple-phpunit --configuration phpunit.xml.dist**
