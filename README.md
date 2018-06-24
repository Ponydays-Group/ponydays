# ponydays
Для сборки фронтенда перейти в ./frontend и запустить `npm run webpack:trunk`. 

Для сборки фронтендв в watch режиме (при обновлении файлов фронтенда будет автоматическая пересборка) запустить `npm run webpack:watch`.

Перед первой сборкой необходимо выполнить `npm i` для установки зависимостей.

Если необходимо установить вебпак: `npm i -g webpack`

== Overall installation ==
1. copy config/local.config.json.example to config/local.config.json and update the database credentials
2. load database structure from database directory (4Frum: don't forget to create a migration files for all your changes to a db structure)
3. create a directory templates/compiled and give it write access for a web-server account

== Fun facts ==
Templates are in templates/skin/redis
