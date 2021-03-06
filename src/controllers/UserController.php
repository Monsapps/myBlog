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

class UserController {

    private $userRole;
    private $userInfos;

    function __construct(string $email, string $hashed) {
        $user = new \Monsapp\Myblog\Models\User();
        $userInfos = $user->getUserInfos($email);
        
        $this->userRole = -1;
        $this->userInfos = null;
        if(password_verify($userInfos["id"] . $userInfos["email"], $hashed)) {
            $this->userInfos = $userInfos;
            $this->userRole = (int)$userInfos['role_id'];
        }
    }

    /**
     * Return user role
     * @return int
     */

    function getUserRole() {
        return $this->userRole;
    }

    /**
     * Return user info
     * @return array
     */

    function getUserInfos() {
        return $this->userInfos;
    }

    /**
     * Return if role can Add/Update/Delete content
     * @return bool
     */

    function isAllowedToCrud() {
        if($this->userRole > 0) {
            return true;
        }
    }
}
