<?php
/**
 * This is the main controller
 */
declare(strict_types=1);

namespace Monsapp\Myblog\Controllers;

class Controller {

    private $title;
    private $keywords;
    private $descriptions;
    private $twig;
    private $role;

    function __construct() {

        $config = new \Monsapp\Myblog\Utils\ConfigManager();

        $this->title = $config->getConfig("site_title");
        $this->keywords = $config->getConfig("site_keywords");
        $this->descriptions = $config->getConfig("site_descriptions");

        $loader = new \Twig\Loader\FilesystemLoader("src/views/");
        $this->twig = new \Twig\Environment($loader);

        if((isset($_COOKIE["email"])) && (isset($_COOKIE["sessionid"])) ) {
            $permission = new AutorisationController($_COOKIE["email"], $_COOKIE["sessionid"]);
            $this->role = $permission->getUserRole();
        }

    }

    function getHomepage() {

        echo $this->twig->render("index.html", array(
                "title" => $this->title, 
                "navtitle" => $this->title, 
                "desciption" => $this->descriptions,
                "keywords" => $this->keywords,
                "role" => $this->role
            ));
    }

    function getConnectPage() {

        echo $this->twig->render("connect.html", array(
            "title" => "Connexion" . $this->title,
            "navtitle" => $this->title,
            "desciption" => $this->descriptions,
            "keywords" => $this->keywords,
            "role" => $this->role
        ));
    }

    function getRegistrationPage(array $post) {

        require "./src/models/User.php";

        $name = $post["name"];
        $surname = $post["surname"];
        $email = $post["email"];
        $password = $post["password"];
        $retryPassword = $post["retrypassword"];

        if($password == $retryPassword) {

            if(userExist($email) != 0) {
                // if email already present in DB return to the registration page
                Header("Location: ./index.php?page=connect&error=2");
                exit;
            } else {
                $date = date('Y-m-d');
                $encryptedPassword = password_hash($password, PASSWORD_DEFAULT);
                addUser($name, $surname, $email, $encryptedPassword, $date, "");
                
                Header("Location: ./index.php");
                exit;
            }

        } else {
            Header("Location: ./index.php?page=connect&error=1");
            exit;
        }
    }

    function getLogInPage(array $post) {

        require "./src/models/User.php";

        $email = $post["email"];
        $password = $post["password"];

        $userInfos = getUserInfos($email);
        $hashedPassword = $userInfos["password"];

        if(password_verify($password, $hashedPassword)) {

            // Set cookies to store email and hashed pass
            setcookie("email", $email, time()+3600);
            // We store combinaise of userId & email hashed password to compare inside Autorisation Controller
            $hashedInfos = password_hash($userInfos["id"] . $userInfos["email"], PASSWORD_DEFAULT);
            setcookie("sessionid", $hashedInfos, time()+3600);
            Header("Location: ./index.php");
            exit;
        } else {
            Header("Location: ./index.php?page=connect&error=3");
            exit;
        }
    }

    function getDisconnectPage() {
        // Set cookies to store empty
        setcookie("email", "", time());
        setcookie("sessionid", "", time());
        Header("Location: ./index.php");
        exit;
    }

}