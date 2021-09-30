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
./src/view/ (templates php files)
./src/view/panel/ (templates php files)
./index.php (main php file)
./README.md (this)
```

## Installation

### Installation requirements
- PHP (>7.3) with PDO MySQL
- MySQL (>5.7)
- Apache (>2.4)

### How to
- Edit ./config/config.ini file with your database infos
```text
[database]
db_hostname = "yourDatabaseHostname"
db_name = "yourDatabaseName"
db_user = "yourDatabaseUsername"
db_password = "yourDatabasePassword"
```
- Upload all files and folder inside your server
- Launch your blog & follow instructions
