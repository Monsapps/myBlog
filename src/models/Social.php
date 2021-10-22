<?php
/**
* This is social model
*/
declare(strict_types=1);

namespace Monsapp\Myblog\Models;

class Social {

    private $db;

    function __construc() {
        $this->db = new \Monsapp\Myblog\Utils\DatabaseManager();
    }

    function getAllSocials() {
        $query = $this->db->query("SELECT * FROM `social`;");
        $results = $query->fetchAll();
        return $results;
    }

    
}
