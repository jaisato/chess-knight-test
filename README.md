Chess knight's shortest path (Another Symfony project).
=====

It calculates the Knight's shortest path from source to destination box on chessboard (using backtracking algorithm).

Instructions:
- Clone repository: git clone git@github.com:jaisato/chess-knight-test.git
- Execute "composer install" to install dependencies: php composer.phar install
- Run PHP server: php bin/console server:start
- Open browser and go to home page http://localhost:8000/. Optional query parameters: source (int), destination (int). Example: http://localhost:8000/?source=0&destination=1
- Run unit tests: php vendor/bin/simple-phpunit --configuration phpunit.xml.dist
