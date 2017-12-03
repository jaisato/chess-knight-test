Chess knight's shortest path (Another Symfony project).
=====

It calculates the Knight's shortest path from source to destination box on chessboard (using backtracking algorithm).

Instructions:
- Requirements: Git, PHP 7.1, composer
- Clone repository: **git clone git@github.com:jaisato/chess-knight-test.git**
- If you have composer globally installed execute *"composer install"*. Otherwise execute *"php composer.phar install"* to install project dependencies.
- Run PHP server: **php bin/console server:run (or server:start)**
- Open browser and go to home page http://localhost:8000/. Optional querystring parameters: **source** (int), **destination** (int). Example: http://localhost:8000/?source=0&destination=1
- Run unit tests: **php vendor/bin/simple-phpunit --configuration phpunit.xml.dist**
