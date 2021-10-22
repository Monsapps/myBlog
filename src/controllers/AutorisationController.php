<?php
/**
 * This is a controller to check the role for users
 * Guest role id 0
 * Administrator role id 1
 * Member role id 2
 * 
 * Todo update members roles
 */

declare(strict_types=1);

namespace Monsapp\Myblog\Controllers;

class AutorisationController {

    private $userRole;

    function __construct(string $email, string $hashed) {

        $db = new \Monsapp\Myblog\Utils\DatabaseManager();

        $sql = "SELECT *
            FROM `user`
            WHERE `email` = :email
            LIMIT 1;";
        $query = $db->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
        $query->execute(array(":email" => $email));

        if($query->rowCount() == 0) {
            $this->roleId = 0;
        } else {
            $userInfos = $query->fetch();
            $query->closeCursor();
            // We compare hashed string with database infos
            if(password_verify($userInfos["id"] . $userInfos["email"], $hashed)) {
                switch($userInfos['role_id']) {
                    case "1":
                        $this->roleId = 1;
                    break;
                    case "2":
                        $this->roleId = 2;
                    break;
                    default:
                    $this->roleId = 0;
                }
            } else {
                $this->roleId = 0;
            }
        }
    }

    function getUserRole() {
        return $this->roleId;
    }
}