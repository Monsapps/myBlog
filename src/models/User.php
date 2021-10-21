<?php
/**
 * This is user model
 */
declare(strict_types=1);

namespace Monsapp\Myblog\Models;

class User {

    private $db;

    function __construct() {
        $this->db = new \Monsapp\Myblog\Utils\DatabaseManager();
    }

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
            $query = $this->db->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
            $query->execute(array(":email" => $emailOrInt));
        
            $userInfos = $query->fetch();
        
            $query = null;
        }

        // if userId is present return infos by id
        if(is_int($emailOrInt)) {
            $sql = "SELECT u.name, u.surname, u.user_hat, i.path_name, c.file_name
                FROM `user` AS u
                LEFT JOIN `image` AS i ON i.`user_id` = u.`id`
                LEFT JOIN `curriculum_vitae` AS c ON c.`user_id` = u.`id`
                WHERE u.`id` = :id
                LIMIT 1;";

            $query = $this->db->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
            $query->execute(array(":id" => $emailOrInt));
            $userInfos = $query->fetch();
            $query = null;
        }
        return $userInfos;
     }
    
    function getAllUsers() {
    $query = $this->db->query("SELECT * FROM `user`;");
    $results = $query->fetchAll();
    return $results;
    }

    function userExist(string $email) {

    $sql = "SELECT *
        FROM `user`
        WHERE email = :email;";
    
    $query = $this->db->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
    $query->execute(array(":email" => $email));

    $resultCount = $query->rowCount();

    $query = null;
    // return true of false???
    return $resultCount;
    }

    function addUser(string $name, string $surname, string $email, string $password, string $date, string $userHat) {

    $sql = "INSERT INTO `user` (`name`, `surname`, `email`, `password`, `registration_date`, `user_hat`, `role_id`)
        VALUES(:name, :surname, :email, :password, :date, :user_hat, 0);";

    $query = $this->db->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
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

    function updateUser(int $userId, string $name, string $surname, string $hat) {
        $sql = "UPDATE `user` SET 
        `name` = :name,
        `surname` = :surname,
        `user_hat`= :hat 
        WHERE `id` = :id;";

        $query = $this->db->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
        $query->execute(array(
            ":id" => $userId,
            ":name" => $name,
            ":surname" => $surname,
            ":hat" => $hat
        ));
        $query = null;
    }

    function setPermission(int $userId, int $roleId) {
        $sql = "UPDATE `user` SET `role_id` = :role_id WHERE `id` = :user_id;";

        $query = $this->db->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
        $query->execute(array(
            ":role_id" => $roleId,
            ":user_id" => $userId
        ));
        $query = null;
    }

    //
    function getUserImage(int $userId) {
        $sql = "SELECT *
            FROM `image`
            WHERE `user_id` = :id
            LIMIT 1;";
        $query = $this->db->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
        $query->execute(array(":id" => $userId));
        $userImage = $query->fetch();
        $query = null;

        return $userImage;
    }

    function getUserCv(int $userId) {
        $sql = "SELECT *
            FROM `curriculum_vitae`
            WHERE `user_id` = :id
            LIMIT 1;";
        $query = $this->db->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
        $query->execute(array(":id" => $userId));
        $userCv = $query->fetch();
        $query = null;

        return $userCv;
    }

    function getUserSocials(int $userId) {
        $sql = "SELECT us.id, us.meta, s.name, s.social_image
            FROM `user_social` AS us
            LEFT JOIN `social` AS s ON s.`id` = us.`social_id`
            WHERE `user_id` = :id";
        $query = $this->db->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
        $query->execute(array(":id" => $userId));
        $socials = $query->fetchAll();
        $query = null;

        return $socials;
    }

    function addSocial(int $userId, int $socialId, string $meta) {

        $sql = "INSERT INTO `user_social` (`user_id`, `social_id`, `meta`) VALUES (:user_id, :social_id, :meta);";

        $query = $this->db->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
        $query->execute(array(":user_id" => $userId, ":social_id" => $socialId, ":meta" => $meta));
        $query = null;
    }

    function updateSocial(int $socialId, string $socialMeta) {
        $sql = "UPDATE `user_social` SET `meta` = :meta WHERE `id` = :id;";

        $query = $this->db->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
        $query->execute(array(":meta" => $socialMeta, ":id" => $socialId));
        $query = null;
    }

    function deleteSocial(int $socialId) {
        $sql = "DELETE FROM `user_social` WHERE `id` = :id;";
        $query = $this->db->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
        $query->execute(array(":id" =>$socialId));
        $query = null;
    }
}
