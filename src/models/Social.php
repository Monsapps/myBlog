<?php
/**
* This is social model
*/
declare(strict_types=1);

namespace Monsapp\Myblog\Models;

class Social {

    private $db;

    function __construct() {
      $this->db = new \Monsapp\Myblog\Utils\DatabaseManager();
    }

    function getAllSocials() {
        $query = $this->db->query("SELECT * FROM `social` ORDER BY `name`ASC;");
        $results = $query->fetchAll();
        return $results;
    }

    function deleteSocial(int $id) {
      $sql = "DELETE FROM `social` WHERE `id` = :id;";

      $query = $this->db->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));

      $query->execute(array(":id" => $id));
      $query = null;
    }

    function addSocial(string $name, string $filename) {

      $sql = "INSERT INTO `social` (`name`, `social_image`) VALUES (:name, :filename);";

      $query = $this->db->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
      $query->execute(array(":name" => $name, ":filename" => $filename));
      $query = null;
    }

    function updateSocialImage(int $id, string $name, string $filename) {

      $sql = "UPDATE `social` 
                SET `name` = :name, `social_image` = :filename
                WHERE `id` = :id;";

      $query = $this->db->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
      $query->execute(array(":name" => $name, 
                            ":filename" => $filename,
                            ":id" => $id));
      $query = null;
    }

    function updateSocial(int $id, string $name) {

      $sql = "UPDATE `social` SET `name` = :name WHERE `id` = :id;";

      $query = $this->db->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
      $query->execute(array(":name" => $name, ":id" => $id));
      $query = null;
    }
    
}
