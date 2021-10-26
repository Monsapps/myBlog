<?php
/**
*    This is class to read configurations from database
*/
declare(strict_types=1);

namespace Monsapp\Myblog\Utils;

class ConfigManager {

    private $db;
    private $dbQuery;

    function __construct() {

        $this->db = new DatabaseManager();
        // Load settings from database
        $query = $this->db->query("SELECT * FROM `config`");
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

    function editConfig(string $setting, string $values) {
        $sql = "UPDATE `config`
            SET `value` = :values
            WHERE `name` = :setting;";

        $query = $this->db->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
        $query->execute(array(
            ":setting" => $setting,
            ":values" => $values
        ));
        $query = null;
    }
}
