<?php
/**
*  This is the main router for browsing page
*/

$controller = new Monsapp\Myblog\Controllers\Controller();

if(isset($_GET["page"])) {
    switch($_GET["page"]) {
        case "users":
            if(isset($_GET["id"])) {
                $sprintf = sprintf("avec id %d", $_GET["id"]);
                    echo $sprintf;
                }
                echo "User demande";
            break;
            case "post":
                if(isset($_GET["show"])) {
                    echo "Articles demande";
                } else {
                    $controller->getPostsPage();
                }
            break;
            case "connect":
                $controller->getConnectPage();
            break;
            case "disconnect":
                $controller->getDisconnectPage();
            break;
            case "login":
                $controller->getLogInPage($_POST);
            break;
            case "register":
                if(isset($_POST)) {
                    $controller->getRegistrationPage($_POST);
                }
            break;
            case "addpost":
                $controller->getAddPostPage();
            break;
            case "publish":
                $controller->getPublishPage($_POST);
            break;
            default:
            include "./src/views/404.php";
        }
    } else {
        $controller->getHomepage();
    }