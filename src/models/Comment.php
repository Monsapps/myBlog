<?php
/**
* This is comment model
*/
declare(strict_types=1);

namespace Monsapp\Myblog\Models;

class Comment {

    function getComments(int $postId) {
        $db = new \Monsapp\Myblog\Utils\DatabaseManager();
        
        $sql = "SELECT c.content, c.date, u.name, u.surname, i.path_name
                FROM `comment` AS c
                LEFT JOIN `user` AS u ON u.`id` = c.`user_id`
                LEFT JOIN `image` AS i ON u.`id` = i.`user_id`
                WHERE `post_id` = :id AND `status` = 'Confirmed'
                ORDER BY c.`date` ASC;";
    
        $query = $db->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
        $query->execute(array(":id" => $postId));
    
        $results = $query->fetchAll();
        $query->closeCursor();
        return $results;
    }

    function getPendingComments() {
        $db = new \Monsapp\Myblog\Utils\DatabaseManager();

        $query = $db->query("SELECT c.id, c.content, p.title, u.name, u.surname
            FROM `comment` AS c
            LEFT JOIN `post` AS p ON p.`id` = c.`post_id`
            LEFT JOIN `user`AS u ON u.`id` = c.`user_id`
            WHERE `status` = 'Pending'
            ORDER BY c.id DESC;");

        $results = $query->fetchAll();
        $query->closeCursor();
        return $results;
    }

    function addComment(int $postId, int $userId, string $content) {
        $db = new \Monsapp\Myblog\Utils\DatabaseManager();

        $date = date("Y-m-d H:i:s");

        $sql = "INSERT INTO `comment` (`user_id`, `post_id`, `content`, `date`)
        VALUES(:user_id, :post_id, :content, :date);";

        $query = $db->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));

        $query->execute(array(
            ":user_id" => $userId,
            ":post_id" => $postId,
            ":content" => $content,
            ":date" => $date
        ));
        $query = null;
    }

    function activateComment(int $commentId) {
        $db = new \Monsapp\Myblog\Utils\DatabaseManager();

        $sql = "UPDATE `comment` SET `status` = 'Confirmed' WHERE `id` = :id;";
        $query = $db->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
        $query->execute(array(":id" => $commentId));
        $query = null;
    }

    function rejectComment(int $commentId) {
        $db = new \Monsapp\Myblog\Utils\DatabaseManager();

        $sql = "UPDATE `comment` SET `status` = 'Rejected' WHERE `id` = :id;";
        $query = $db->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
        $query->execute(array(":id" => $commentId));
        $query = null;
    }

}
