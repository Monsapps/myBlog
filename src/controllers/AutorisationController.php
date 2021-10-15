<?php
/**
 * This is a controller to check the role for users
 * Member role id 0
 * Administrator role id 1
 * Editor role id 2
 * 
 */

declare(strict_types=1);

namespace Monsapp\Myblog\Controllers;

class AutorisationController {

    private $db;
    private $userRole;

    function __construct() {
        $this->db = new \Monsapp\Myblog\Utils\DatabaseManager();
    }

    function getUserRole(string $email, string $hashed) {
        $sql = "SELECT *
            FROM `user`
            WHERE `email` = :email
            LIMIT 1;";
        $query = $this->db->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
        $query->execute(array(":email" => $email));

        if($query->rowCount() == 0) {
            return -1;
        } else {
            $userInfos = $query->fetch();
            $query->closeCursor();
            // We compare hashed string with database infos
            if(password_verify($userInfos["id"] . $userInfos["email"], $hashed)) {
                return (int)$userInfos['role_id'];
            } else {
                return -1;
            }
        }
    }
}
