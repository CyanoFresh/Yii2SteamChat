Yii 2 Steam Chat
============================

Chat using WebSocket, Yii2 Framework and Steam login.


DIRECTORY STRUCTURE
-------------------

      assets/             contains assets definition
      commands/           contains console commands (controllers)
      config/             contains application configurations
      controllers/        contains Web controller classes
      mail/               contains view files for e-mails
      models/             contains model classes
      runtime/            contains files generated during runtime
      tests/              contains various tests for the basic application
      vendor/             contains dependent 3rd-party packages
      views/              contains view files for the Web application
      web/                contains the entry script and Web resources



REQUIREMENTS
------------

The minimum requirement by this project template that your Web server supports PHP 5.4.0.


INSTALLATION
------------

```
git clone https://github.com/CyanoFresh/Yii2SteamChat chat
cd chat
composer update
```

Configure app in the `config/web.php` and `config/db.php`
Add Steam API Key on `config/params.php`

```
php yii migrate
```

To run WebSocket server use (required for chat:

```
php yii server
```

Set document root for domain to `web/`
