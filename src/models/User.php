<?php
/**
 * This is user model
 */

declare(strict_types=1);

 function getUserInfos(string $email) {
    $db = new Monsapp\Myblog\Utils\DatabaseManager();

    $sql = "SELECT *
        FROM `user`
        WHERE email = :email
        LIMIT 1;";
    $query = $db->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $query->execute(array(":email" => $email));

    $userInfos = $query->fetch();

    $query->closeCursor();

    return $userInfos;
 }

 function userExist(string $email) {

    $db = new Monsapp\Myblog\Utils\DatabaseManager();

    $sql = "SELECT *
        FROM `user`
        WHERE email = :email;";
    
    $query = $db->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $query->execute(array(":email" => $email));

    $resultCount = $query->rowCount();

    $query->closeCursor();
    // return true of false???
    return $resultCount;
 }

 function addUser(string $name, string $surname, string $email, string $password, string $date, string $userHat) {
    $db = new Monsapp\Myblog\Utils\DatabaseManager();

    $sql = "INSERT INTO `user` (`name`, `surname`, `email`, `password`, `registration_date`, `user_hat`, `role`)
        VALUES(:name, :surname, :email, :password, :date, :user_hat, 2);";

    $query = $db->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $query->execute(array(
        ":name" => $name,
        ":surname" => $surname,
        ":email" => $email,
        ":password" => $password,
        ":date" => $date,
        ":user_hat" => $userHat
    ));

    $query->cursorClose();
 }