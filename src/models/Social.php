<?php
/**
* This is social model
*/
declare(strict_types=1);

namespace Monsapp\Myblog\Models;

class Social {

    private $dbManager;

    function __construct() {
      $this->dbManager = new \Monsapp\Myblog\Utils\DatabaseManager();
    }

    function getAllSocials() {
        $query = $this->dbManager->query("SELECT * FROM `social` ORDER BY `name`ASC;");
        $results = $query->fetchAll();
        return $results;
    }

    function deleteSocial(int $idSocial) {
      $sql = "DELETE FROM `social` WHERE `id` = :id;";

      $query = $this->dbManager->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));

      $query->execute(array(":id" => $idSocial));
      $query = null;
    }

    function addSocial(string $name, string $filename) {

      $sql = "INSERT INTO `social` (`name`, `social_image`) VALUES (:name, :filename);";

      $query = $this->dbManager->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
      $query->execute(array(":name" => $name, ":filename" => $filename));
      $query = null;
    }

    function updateSocialImage(int $idSocial, string $name, string $filename) {

      $sql = "UPDATE `social` 
                SET `name` = :name, `social_image` = :filename
                WHERE `id` = :id;";

      $query = $this->dbManager->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
      $query->execute(array(":name" => $name, 
                            ":filename" => $filename,
                            ":id" => $idSocial));
      $query = null;
    }

    function updateSocial(int $idSocial, string $name) {

      $sql = "UPDATE `social` SET `name` = :name WHERE `id` = :id;";

      $query = $this->dbManager->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
      $query->execute(array(":name" => $name, ":id" => $idSocial));
      $query = null;
    }
    
}
