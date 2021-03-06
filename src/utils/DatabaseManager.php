<?php
/**
*   Class to manage data from MySQL DB
*/
namespace Monsapp\Myblog\Utils;

class DatabaseManager extends \PDO {

    private $iniConfig;

    /**
     * Overwrite PDO connection
     * @return void
     */

    function __construct() {

        $this->parseIniFile();

        $dns = "mysql:host=" . $this->iniConfig["database"]["db_hostname"] .
        ((!empty($this->iniConfig["db_port"])) ? (';port=' . $this->iniConfig["database"]["db_port"]) : "") .
        ";dbname=" . $this->iniConfig["database"]["db_name"];

        try {
            parent::__construct($dns, $this->iniConfig["database"]["db_user"], $this->iniConfig["database"]["db_password"]);
        } catch(PDOException $e) {
            $this->errorException($e->getMessage());
        }
    }

    /**
     * Open config.ini file for the database connection
     * @return void
     */

    private function parseIniFile() {
        if(!$iniConfig = parse_ini_file("./config/config.ini", TRUE))
            throw new Exception("Impossible d'ouvrir le fichier de configuration de la base de données");

        $this->iniConfig = $iniConfig;
    }

    /**
     * Error exception page
     * @return void
     */

    function errorException($error) {
        ?>
        <html>
        <head>
            <title>Erreur</title>
        </head>
        <body>
            <?= filter_var($error, FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?>
        </body>
        </html>
    <?php
    }
}
