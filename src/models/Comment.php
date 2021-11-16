<?php
/**
* This is comment model
*/
declare(strict_types=1);

namespace Monsapp\Myblog\Models;

class Comment {

    private $dbManager;

    function __construct() {
        $this->dbManager = new \Monsapp\Myblog\Utils\DatabaseManager();
    }

    /**
     * Return all comments for a post
     * @param int $postId
     *  Post id
     * @return array
     */

    function getComments(int $postId) {
        
        $sql = "SELECT c.content, c.date, u.name, u.surname, i.path_name
                FROM `comment` AS c
                LEFT JOIN `user` AS u ON u.`id` = c.`user_id`
                LEFT JOIN `image` AS i ON u.`id` = i.`user_id`
                WHERE `post_id` = :id AND `status` = 'Confirmed'
                ORDER BY c.`date` ASC;";
    
        $query = $this->dbManager->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
        $query->execute(array(":id" => $postId));
    
        $results = $query->fetchAll();
        $query->closeCursor();
        return $results;
    }

    /**
     * Return all pending comments
     * @return array
     */

    function getPendingComments() {

        $query = $this->dbManager->query("SELECT c.id, c.content, p.title, u.name, u.surname
            FROM `comment` AS c
            LEFT JOIN `post` AS p ON p.`id` = c.`post_id`
            LEFT JOIN `user`AS u ON u.`id` = c.`user_id`
            WHERE `status` = 'Pending'
            ORDER BY c.id DESC;");

        $results = $query->fetchAll();
        $query->closeCursor();
        return $results;
    }

    /**
     * Add comment to database
     * @param int $postId
     *  Post id
     * @param int $userId
     *  User id
     * @param string $content
     *  Content of comment
     * @return void
     */

    function addComment(int $postId, int $userId, string $content) {

        $date = date("Y-m-d H:i:s");

        $sql = "INSERT INTO `comment` (`user_id`, `post_id`, `content`, `date`, `status`)
        VALUES(:user_id, :post_id, :content, :date, 'Pending');";

        $query = $this->dbManager->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));

        $query->execute(array(
            ":user_id" => $userId,
            ":post_id" => $postId,
            ":content" => $content,
            ":date" => $date
        ));
        $query = null;
    }

    /**
     * Activate comment visibility
     * @param int $commentId
     *  Comment id
     * @return void
     */

    function activateComment(int $commentId) {

        $sql = "UPDATE `comment` SET `status` = 'Confirmed' WHERE `id` = :id;";
        $query = $this->dbManager->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
        $query->execute(array(":id" => $commentId));
        $query = null;
    }

    /**
     * Reject comment 
     * @param int $commentId
     *  Comment id
     * @return void
     */

    function rejectComment(int $commentId) {

        $sql = "UPDATE `comment` SET `status` = 'Rejected' WHERE `id` = :id;";
        $query = $this->dbManager->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
        $query->execute(array(":id" => $commentId));
        $query = null;
    }

}
