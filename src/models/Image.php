<?php
/**
 * This is image model
 */
declare(strict_types=1);

namespace Monsapp\Myblog\Models;

class Image {

    private $dbManager;

    function __construct() {
        $this->dbManager = new \Monsapp\Myblog\Utils\DatabaseManager();
    }

    /**
     * Add avatar info to database
     * @param int $userId
     *  User id
     * @param string $imageName
     *  Image name
     * @return void
     */

    function setImage(int $userId, string $imageName) {
        $sql = "INSERT INTO `image` (`user_id`, `path_name`)
        VALUES(:user_id, :path_name);";

        $query = $this->dbManager->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
        $query->execute(array(
            ":user_id" => $userId,
            ":path_name" => $imageName
            ));
        $query = null;
    }

    /**
     * Update avatar info of the database
     * @param int $userId
     *  User id
     * @param string $imageName
     *  Image name
     * @return void
     */

    function updateImage(int $userId, string $imageName) {
        $sql = "UPDATE `image` SET `path_name` = :path_name WHERE `user_id` = :id;";
        $query = $this->dbManager->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
        $query->execute(array(
            ":path_name" => $imageName,
            ":id" => $userId
        ));
        $query = null;
    }

}
