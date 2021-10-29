<?php
/**
* This is message model, 
*/
declare(strict_types=1);

namespace Monsapp\Myblog\Models;

class Contact {

    private $db;

    function __construct() {
        $this->db = new \Monsapp\Myblog\Utils\DatabaseManager();
    }

    function addContactMessage(string $message, string $email, string $name, string $surname) {
        $sql = "INSERT INTO `contact` (`name`, `surname`, `email`, `message`, `status`)
            VALUES(:name, :surname, :email, :message, 0);";

        $query = $this->db->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
        $query->execute(array(
            ":name" => $name,
            ":surname" => $surname,
            ":email" => $email,
            ":message" => $message,
            ));
        $query = null;
    }

    function getAllContactMessage() {
        $query = $this->db->query("SELECT * FROM `contact` WHERE `status` = 0;");
        $results = $query->fetchAll();
        return $results;
    }

    function updateStatus(int $id) {
        $sql = "UPDATE `contact` SET `status` = 1 WHERE `id` = :id;";

        $query = $this->db->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
        $query->execute(array(":id" => $id));
        $query = null;
    }
}