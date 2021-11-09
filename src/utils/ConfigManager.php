<?php
/**
*    This is class to read configurations from database
*/
declare(strict_types=1);

namespace Monsapp\Myblog\Utils;

class ConfigManager {

    private $dbManager;
    private $dbQuery;

    function __construct() {

        $this->dbManager = new DatabaseManager();
        // Load settings from database
        $query = $this->dbManager->query("SELECT * FROM `config`");
        // Add $query to local $dbQuery
        $this->dbQuery = $query;
    }

    function getConfig(string $name = null) {
        if(!empty($name)) {
            // Find value for $name
            while($getValue = $this->dbQuery->fetch()) {

                if ($getValue["name"] == $name) {

                    // Return the value for $name
                    return $getValue["value"];
                }
            }
        }
        // Create new array & return it
        $siteInfo = [];
        while($getValue = $this->dbQuery->fetch()) {
            $siteInfo[$getValue["name"]] = $getValue["value"];
        }
        return $siteInfo;
    }

    function editConfig(string $setting, string $values) {
        $sql = "UPDATE `config`
            SET `value` = :values
            WHERE `name` = :setting;";

        $query = $this->dbManager->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
        $query->execute(array(
            ":setting" => $setting,
            ":values" => $values
        ));
        $query = null;
    }
}
