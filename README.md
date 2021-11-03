# myBlog

## Project structure

```text
./config/ (folder for config file)
./public/css/ (cascading style sheets file)
./public/images/ (images files)
./public/javascript/ (javascript file)
./public/uploads/ (uploaded files)
./src/controller/ (controllers php files)
./src/model/ (models php files)
./src/utils/ (utils php files)
./src/view/ (templates php files)
./src/view/panel/ (templates php files)
./index.php (main php file)
./install.php (php script to install myBlog)
./README.md (this)
```

## Installation

### Installation requirements
-  PHP (>7.3) with PDO MySQL
-  MySQL (>5.7)
-  Apache (>2.4)
-  Autoload (Composer)
-  Twig (>1.42)

### How to
-  Edit ./config/config.ini file with your database infos
```text
[database]
db_hostname = "yourDatabaseHostname"
db_name = "yourDatabaseName"
db_user = "yourDatabaseUsername"
db_password = "yourDatabasePassword"
```
-  Add require library with composer (vendor folder in root)
-  Upload all files and folder inside your server
-  Launch http://example.com/install.php and follow instructions
-  Enjoy
