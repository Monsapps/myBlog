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

    /**
     * Return availables socials networks from the database
     * @return array
     */

    function getAllSocials() {
        $query = $this->dbManager->query("SELECT * FROM `social` ORDER BY `name` ASC;");
        $results = $query->fetchAll();
        return $results;
    }

    /**
     * Delete available social network from the database
     * @param int $idSocial
     *  Social network id
     * @return void
     */

    function deleteSocial(int $idSocial) {
      $sql = "DELETE FROM `social` WHERE `id` = :id;";

      $query = $this->dbManager->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));

      $query->execute(array(":id" => $idSocial));
      $query = null;
    }

    /**
     * Add available social network to the database
     * @param string $name
     *  Name of the social network
     * @param string $filename
     *  Filename of the logo
     * @return void
     */

    function addSocial(string $name, string $filename) {

      $sql = "INSERT INTO `social` (`name`, `social_image`) VALUES (:name, :filename);";

      $query = $this->dbManager->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
      $query->execute(array(":name" => $name, ":filename" => $filename));
      $query = null;
    }

    /**
     * Update social network image and name of the database
     * @param string $name
     *  Name of the social network
     * @param string $filename
     *  Filename of the logo
     * @return void
     */

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

    /**
     * Update social network name of the database
     * @param string $name
     *  Name of the social network
     * @return void
     */

    function updateSocial(int $idSocial, string $name) {

      $sql = "UPDATE `social` SET `name` = :name WHERE `id` = :id;";

      $query = $this->dbManager->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
      $query->execute(array(":name" => $name, ":id" => $idSocial));
      $query = null;
    }
    
}
