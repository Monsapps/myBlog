<?php
/**
* This is comment model
*/
declare(strict_types=1);

namespace Monsapp\Myblog\Models;

class Role {

    private $db;

    function __construct() {
        $this->db = new \Monsapp\Myblog\Utils\DatabaseManager();
    }

    function getRoles() {
        $query = $this->db->query("SELECT * FROM `role`;");
        $results = $query->fetchAll();
        return $results;
    }

}
