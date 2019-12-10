# Island Rush V2

[![Build status](https://dev.azure.com/spenceradolph/IslandRushK2/_apis/build/status/IslandRushK2-CI)](https://dev.azure.com/spenceradolph/IslandRushK2/_build/latest?definitionId=5)

![FullGameboard](https://github.com/island-rush/Images/blob/master/K2/FullGameboard.PNG)

Island Rush is a military strategy teaching tool/game for use by DFMI at The United States Air Force Academy. The game is deployed as a web-app, and conists of 2 teams of 4-5 students each playing aginst each other to dominate a domain of islands. Students use lessons of strategy they have learned and put them into practice to demonstrate their knowledge.

## Getting Started

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes. See deployment for notes on how to deploy the project on a live system.

### Prerequisites

The root directory of this repository should be hosted on a web server. These are widely available and configurable, and specific to your hosting situation (ex: local vs cloud). Note that the entire backend is written in PHP. MySQL is used for the database. This can be installed/used either locally or externally.

```
web server
mysql
```

### Database

There are many methods of running / hosting a MySQL server. Once the database exists, please create a user/password for the game to use. Set these values in the env variables, or hard-code them in the ./db.php

### Development

Hosting the repository should allow access to these pages.

[XAMPP](https://www.apachefriends.org/index.html) was used by our development team, but any [similar](https://en.wikipedia.org/wiki/List_of_Apache%E2%80%93MySQL%E2%80%93PHP_packages) software should do.

- /index.php
- /admin.php
- /courseDirector.php
- /credits.html
- /troubleshoot.html
- /game.php
  - You must authenticate via player login to see/use this page.

Note there are several env variables used by the backend, although these could also be changed with hard-coded default values. (Within ./db.php and ./loginVerify.php)

- CD_LASTNAME = Course Director Last Name (lowercase)
- CD_PASSWORD = Course Director MD5 Password Hash
- DB_NAME = name of database
- DB_HOSTNAME = database host (ex: remotemysql.com)
- DB_USERNAME = database user
- DB_PASSWORD = database password

Inserting the database tables must be done manually through either command line interface, or usually MySQL Workbench. Run the ./db_reset.sql script to accomplish this before all other tasks. Creating/deleting games can be accomplished from the ./courseDirector page. Login from the homepage with the creditionals used in the env variables. The password used when creating a game is the password used by teachers to login to their ./admin page. Teachers are able activate/deactivate their games, as well as reset the game to have initial pieces on the board.

## Deployment

Simply host this directory, create a mysql database, and correctly assign all env variables/hard-coded variables.

Current Official Deployments have been automated and setup with [Azure](https://azure.microsoft.com/en-us/).

Azure App Services may have issues with php session data. This may be due to auto-scaling, or other Azure services, and could perhaps be solved with an external session store. Previous deployments utilized Azure VMs with XAMPP for more control, and ensuring similarity between development and production environments.

## Built With

- [php](https://nodejs.org/en/docs/) - Frontend / Backend
  - HTML, Javascript, CSS
- [mysql](https://dev.mysql.com/doc/) - Database

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Version

Version 2.6.2

## TODO

- Fix piece dragging and popups timing to be more stable.
- Add wiki type website to explain the game / rules. (in progress)
- Improve Troubleshooting page
- Added url messages into body on CD page, similar to K3

Note: Although this version is no longer being developed, it is still supported by the Island Rush Dev Team. Please [report](https://gitreports.com/issue/island-rush/K2) any issues.
