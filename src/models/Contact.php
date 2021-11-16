<?php
/**
* This is message model, 
*/
declare(strict_types=1);

namespace Monsapp\Myblog\Models;

class Contact {

    private $dbManager;

    function __construct() {
        $this->dbManager = new \Monsapp\Myblog\Utils\DatabaseManager();
    }

    /**
     * Add contact message to database
     * @param string $message
     *  Message
     * @param string $email
     *  Sender mail
     * @param string $name
     *  Sender name
     * @param string $surname
     *  Sender surname
     * @return void
     */

    function addContactMessage(string $message, string $email, string $name, string $surname) {
        $sql = "INSERT INTO `contact` (`name`, `surname`, `email`, `message`, `status`)
            VALUES(:name, :surname, :email, :message, 0);";

        $query = $this->dbManager->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
        $query->execute(array(
            ":name" => $name,
            ":surname" => $surname,
            ":email" => $email,
            ":message" => $message,
            ));
        $query = null;
    }

    /**
     * Return not read messages from database
     * @return array
     */

    function getAllContactMessage() {
        $query = $this->dbManager->query("SELECT * FROM `contact` WHERE `status` = 0;");
        $results = $query->fetchAll();
        return $results;
    }

    /**
     * Update contact message status
     * @param int $idMessage
     *  Message id
     * @return void
     */

    function updateStatus(int $idMessage) {
        $sql = "UPDATE `contact` SET `status` = 1 WHERE `id` = :id;";

        $query = $this->dbManager->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
        $query->execute(array(":id" => $idMessage));
        $query = null;
    }
}