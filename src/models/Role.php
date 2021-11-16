<?php
/**
* This is comment model
*/
declare(strict_types=1);

namespace Monsapp\Myblog\Models;

class Role {

    private $dbManager;

    function __construct() {
        $this->dbManager = new \Monsapp\Myblog\Utils\DatabaseManager();
    }

    /**
     * Return availables roles
     * @return array
     */

    function getRoles() {
        $query = $this->dbManager->query("SELECT * FROM `role`;");
        $results = $query->fetchAll();
        return $results;
    }

}
