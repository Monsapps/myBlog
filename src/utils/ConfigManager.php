<?php
/**
*    This is class to read configurations from database
*/
declare(strict_types=1);

namespace Monsapp\Myblog\Utils;

class ConfigManager {

    //private $db;
    private $dbQuery;

    function __construct() {

        $db = new DatabaseManager();

        // Load settings from database
        $query = $db->query("SELECT * FROM `config`");

        // Add $query to local $dbQuery
        $this->dbQuery = $query;
    }

    function getConfig(string $name) {

        if(!empty($name)) {

            // Find value for $name
            while($getValue = $this->dbQuery->fetch()) {

                if ($getValue["name"] == $name) {

                    // Return the value for $name
                    return $getValue["value"];
                }

            }

            // We close the DB
            $this->dbQuery->closeCursor();

        }

    }

    // Create config table for first launch
    function createConfig() {
        //TODO
        /* private $db
        
        */
        $createTable = $db->query("CREATE...");

    }

    // Set setting
    function setConfig(string $setting, string $value) {
        //TODO
        
    }

}