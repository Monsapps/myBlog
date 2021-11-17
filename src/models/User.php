<?php
/**
 * This is user model
 */
declare(strict_types=1);

namespace Monsapp\Myblog\Models;

class User {

    private $dbManager;

    function __construct() {
        $this->dbManager = new \Monsapp\Myblog\Utils\DatabaseManager();
    }

    /**
     * Get user info from the database
     * @param string|int $emailOrInt
     *  User email or id
     * @return array
     */

    function getUserInfos($emailOrInt) {
        $userInfos = null;
        // if email is present return infos by email for login
        if(is_string($emailOrInt)) {
            $sql = "SELECT u.id, u.email, u.name, u.surname, u.password, u.user_hat, u.role_id, i.path_name, c.file_name
                FROM `user` AS u
                LEFT JOIN `image` AS i ON i.`user_id` = u.`id`
                LEFT JOIN `curriculum_vitae` AS c ON c.`user_id` = u.`id`
                WHERE u.`email` = :email
                LIMIT 1;";
            $query = $this->dbManager->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
            $query->execute(array(":email" => $emailOrInt));
        
            $userInfos = $query->fetch();
        
            $query = null;
        }

        // if userId is present return infos by id
        if(is_int($emailOrInt)) {
            $sql = "SELECT  u.id, u.email, u.name, u.surname, u.password, u.user_hat, u.role_id, i.path_name, c.file_name
                FROM `user` AS u
                LEFT JOIN `image` AS i ON i.`user_id` = u.`id`
                LEFT JOIN `curriculum_vitae` AS c ON c.`user_id` = u.`id`
                WHERE u.`id` = :id
                LIMIT 1;";

            $query = $this->dbManager->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
            $query->execute(array(":id" => $emailOrInt));
            $userInfos = $query->fetch();
            $query = null;
        }
        return $userInfos;
    }

    /**
     * Return all users from the database
     * @return array
     */
    
    function getAllUsers() {
    $query = $this->dbManager->query("SELECT * FROM `user`;");
    $results = $query->fetchAll();
    return $results;
    }

    /**
     * Return user count from the database
     * @param string $email
     *  Mail of the user
     * @return int
     */

    function userExist(string $email) {
    $sql = "SELECT *
        FROM `user`
        WHERE email = :email;";
    
    $query = $this->dbManager->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
    $query->execute(array(":email" => $email));

    $resultCount = $query->rowCount();

    $query = null;
    return $resultCount;
    }

    /**
     * Add user to the database
     * @param string $name
     *  Name
     * @param string $surname
     *  Surname
     * @param string $email
     *  Mail
     * @param string $password
     *  Hashed password
     * @param string $date
     *  Date of registration
     * @param string $userHat
     *  "Punch line" of the user
     * @return void
     */

    function addUser(string $name, string $surname, string $email, string $password, string $date, string $userHat) {

    $sql = "INSERT INTO `user` (`name`, `surname`, `email`, `password`, `registration_date`, `user_hat`, `role_id`)
        VALUES(:name, :surname, :email, :password, :date, :user_hat, 0);";

    $query = $this->dbManager->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
    $query->execute(array(
        ":name" => $name,
        ":surname" => $surname,
        ":email" => $email,
        ":password" => $password,
        ":date" => $date,
        ":user_hat" => $userHat
    ));
    $query = null;
    }

    /**
     * Update user to the database
     * @param string $name
     *  Name
     * @param string $surname
     *  Surname
     * @param string $hat
     *  "Punch line" of the user
     * @return void
     */

    function updateUser(int $userId, string $name, string $surname, string $hat) {
        $sql = "UPDATE `user` SET 
        `name` = :name,
        `surname` = :surname,
        `user_hat`= :hat 
        WHERE `id` = :id;";

        $query = $this->dbManager->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
        $query->execute(array(
            ":id" => $userId,
            ":name" => $name,
            ":surname" => $surname,
            ":hat" => $hat
        ));
        $query = null;
    }

    /**
     * Set user permission
     * @param int $userId
     *  User id
     * @param int $roleId
     *  Role id
     * @return void
     */

    function setPermission(int $userId, int $roleId) {
        $sql = "UPDATE `user` SET `role_id` = :role_id WHERE `id` = :user_id;";

        $query = $this->dbManager->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
        $query->execute(array(
            ":role_id" => $roleId,
            ":user_id" => $userId
        ));
        $query = null;
    }

    /**
     * Return user socials networks from the database
     * @param int $userId
     *  User id
     * @return array
     */

    function getUserSocials(int $userId) {
        $sql = "SELECT us.id, us.meta, s.name, s.social_image
            FROM `user_social` AS us
            LEFT JOIN `social` AS s ON s.`id` = us.`social_id`
            WHERE `user_id` = :id";
        $query = $this->dbManager->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
        $query->execute(array(":id" => $userId));
        $socials = $query->fetchAll();
        $query = null;

        return $socials;
    }

    /**
     * Add user social to the database
     * @param int $userId
     *  User id
     * @param int $socialId
     *  Social network id
     * @param string $meta
     *  Profile address
     * @return void
     */

    function addSocial(int $userId, int $socialId, string $meta) {

        $sql = "INSERT INTO `user_social` (`user_id`, `social_id`, `meta`) VALUES (:user_id, :social_id, :meta);";

        $query = $this->dbManager->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
        $query->execute(array(":user_id" => $userId, ":social_id" => $socialId, ":meta" => $meta));
        $query = null;
    }

    /**
     * Update user social to the database
     * @param int $socialId
     *  Social network id
     * @param string $meta
     *  Profile address
     * @return void
     */

    function updateSocial(int $socialId, string $socialMeta) {
        $sql = "UPDATE `user_social` SET `meta` = :meta WHERE `id` = :id;";

        $query = $this->dbManager->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
        $query->execute(array(":meta" => $socialMeta, ":id" => $socialId));
        $query = null;
    }

    /**
     * Delete user social to the database
     * @param int $socialId
     *  Social network id
     * @return void
     */

    function deleteSocial(int $socialId) {
        $sql = "DELETE FROM `user_social` WHERE `id` = :id;";
        $query = $this->dbManager->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
        $query->execute(array(":id" =>$socialId));
        $query = null;
    }
}
