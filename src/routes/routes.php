<?php
/**
*  This is the main router for browsing page
*/

$controller = new Monsapp\Myblog\Controllers\Controller();

if(isset($_GET["page"])) {
    switch($_GET["page"]) {
        case "post":
            // If an id get the post
            if(isset($_GET["id"])) {
                // We must have an numeric id 
                if(is_numeric($_GET["id"])) {
                    $controller->getPostPage($_GET["id"]);
                } else {
                    $controller->getPostsPage();
                }
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
            $controller->getLoginPage($_POST);
        break;
        case "register":
            if(isset($_POST)) {
                $controller->getRegistrationPage($_POST);
            } else {
                $controller->getConnectPage();
            }
        break;
        case "contact":
            $controller->getContactPage($_POST);
        break;
        case "addpost":
            $controller->getAddPostPage();
        break;
        case "publish":
            $controller->getPublishPage($_POST);
        break;
        case "editpost":
            // If an id get the post
            if(isset($_GET["id"])) {
                // We must have an numeric id 
                if(is_numeric($_GET["id"])) {
                    $controller->getEditPostPage($_GET["id"]);
                } else {
                    $controller->getPostsPage();
                }
            } else {
                $controller->getPostsPage();
            }
        break;
        case "editpostpublish":
            $controller->getEditPostPublishPage($_POST);
        break;
        case "addcomment":
            $controller->getAddCommentPage($_POST);
        break;
        case "panel":
            $controller->getPanelPage();
        break;
        case "editprofile":
            $controller->getEditProfilePage($_POST);
        case "uploadavatar":
            $controller->getUploadAvatarPage($_FILES, $_POST);
        break;
        case "addusersocials":
            $controller->getAddUserSocialsPage($_POST);
        break;
        case "updateusersocials":
            $controller->getUpdateUserSocialsPage($_POST);
        break;
        case "deleteusersocial":
            $controller->getDeleteUserSocialPage($_GET["user_id"], $_GET["social_id"]);
        break;
        case "uploadcv":
            $controller->getUploadCvPage($_FILES, $_POST);
        break;
        case "commentmanager":
            $controller->getCommentManagerPage();
        break;
        case "confirmcomment":
            if(is_numeric($_GET["id"])) {
                $controller->getActivateCommentPage($_GET["id"]);
            } else {
                $controller->getHomepage();
            }
        break;
        case "rejectcomment":
            if(is_numeric($_GET["id"])) {
                $controller->getRejectCommentPage($_GET["id"]);
            } else {
                $controller->getHomepage();
            }
        break;
        case "settingsmanager":
            $controller->getSettingsManagerPage();
        break;
        case "contactmanager":
            $controller->getContactManagerPage();
        break;
        case "readmessage":
            $controller->getReadMessagePage($_GET["id"]);
        break;
        case "mainsettings":
            $controller->getMainSettingsPage($_POST);
        break;
        case "permissionmanager":
            $controller->getPermissionsManagerPage();
        break;
        case "setpermission":
            $controller->getSetPermissionPage($_POST);
        break;
        case "deletesocial":
            $controller->getDeleteSocialPage($_GET["id"]);
        break;
        case "updatesocial":
            $controller->getUpdateSocialPage($_FILES, $_POST);
        break;
        case "postmanager":
            $controller->getPostManagerPage();
        break;
        case "deletepost":
            $controller->getDeletePostPage($_GET["id"]);
        break;
        default:
            $controller->getHomepage();
        }
    } else {
        $controller->getHomepage();
    }
