<?php
/**
 * This is image model
 */
declare(strict_types=1);

namespace Monsapp\Myblog\Models;

class Image {

    private $db;

    function __construct() {
        $this->db = new \Monsapp\Myblog\Utils\DatabaseManager();
    }

    function setImage(int $userId, string $imageName) {
        $sql = "INSERT INTO `image` (`user_id`, `path_name`)
        VALUES(:user_id, :path_name);";

        $query = $this->db->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
        $query->execute(array(
            ":user_id" => $userId,
            ":path_name" => $imageName
            ));
        $query = null;
    }

    function updateImage(int $userId, string $imageName) {
        $sql = "UPDATE `image` SET `path_name` = :path_name WHERE `user_id` = :id;";
        $query = $this->db->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
        $query->execute(array(
            ":path_name" => $imageName,
            ":id" => $userId
        ));
        $query = null;
    }

}
