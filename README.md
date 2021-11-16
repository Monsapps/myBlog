# myBlog
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/5d43642e85564289a15c2d9884055cdf)](https://www.codacy.com/gh/Monsapps/myBlog/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=Monsapps/myBlog&amp;utm_campaign=Badge_Grade)

## Project structure

*   config (folder for config file)

*   public

    *   css (cascading style sheets file)

    *   images (images files)

    *   javascript (javascript file)

    *   uploads (uploaded files)

*   src

    *   controllers (Controllers files)

    *   models (Models files)

    *   routes (router file)

    *   utils (utils files)

    *   views (Views files)

        *   panel (Views for panel)

*   index.php (main php files)

*   install.php (php script to install myBlog)

*   README.md (this)

## Installation

### Installation requirements
*   PHP (>7.3) with PDO MySQL
*   MySQL (>5.7)
*   Apache (>2.4)
*   Autoload (Composer)
*   Twig (>1.42)

### How to
*   Edit ./config/config.ini file with your database infos
```text
[database]
db_hostname = "yourDatabaseHostname"
db_name = "yourDatabaseName"
db_user = "yourDatabaseUsername"
db_password = "yourDatabasePassword"
```
*   Add require library with composer (vendor folder in root)
*   Upload all files and folder inside your server
*   Launch http://example.com/install.php and follow instructions
*   Enjoy
