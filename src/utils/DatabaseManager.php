<?php
/**
*   Class to manage data from MySQL DB
*/
namespace Monsapp\Myblog\Utils;

class DatabaseManager extends \PDO {

    private $iniConfig;

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

    private function parseIniFile() {
        if(!$iniConfig = parse_ini_file("./config/config.ini", TRUE))
            throw new Exception("Impossible d'ouvrir le fichier de configuration de la base de données");

        $this->iniConfig = $iniConfig;
    }

    function errorException($error) {
        die("Erreur". $error);
    }
}
