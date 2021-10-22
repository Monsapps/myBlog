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
    private $userInfos;

    function __construct(string $email, string $hashed) {
        $this->db = new \Monsapp\Myblog\Utils\DatabaseManager();
        $sql = "SELECT *
        FROM `user`
        WHERE `email` = :email
        LIMIT 1;";
        $query = $this->db->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
        $query->execute(array(":email" => $email));

        if($query->rowCount() == 0) {
            $this->userRole = -1;
            $this->userInfos = null;
        } else {
            $userInfos = $query->fetch();
            $query->closeCursor();
            // We compare hashed string with database infos
            if(password_verify($userInfos["id"] . $userInfos["email"], $hashed)) {
                $this->userInfos = $userInfos;
                $this->userRole = (int)$userInfos['role_id'];
            } else {
                $this->userRole = -1;
            }
        }
    }

    function getUserRole() {
        return $this->userRole;
    }

    function getUserInfos() {
        return $this->userInfos;
    }

    function isAllowedToCrud() {
        if($this->userRole > 0) {
            return true;
        } else {
            return false;
        }

    }
}
