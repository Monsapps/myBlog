<?php
/**
*  This is the main router for browsing page
*/

$controller = new Monsapp\Myblog\Controllers\Controller();
$dollar = new Monsapp\Myblog\Utils\Dollar();

if(!empty($dollar->inputGet("page"))) {
    switch($dollar->inputGet("page")) {
        case "post":
            // If an id get the post
            if(!empty($dollar->inputGet("id"))) {
                // We must have an numeric id 
                if(is_numeric($dollar->inputGet("id"))) {
                    $controller->getPostPage($dollar->inputGet("id"));
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
            $controller->getLoginPage($dollar->inputPost());
        break;
        case "register":
            if(isset($_POST)) {
                $controller->getRegistrationPage($dollar->inputPost());
            } else {
                $controller->getConnectPage();
            }
        break;
        case "contact":
            $controller->getContactPage($dollar->inputPost());
        break;
        case "addpost":
            $controller->getAddPostPage();
        break;
        case "publish":
            $controller->getPublishPage($dollar->inputPost());
        break;
        case "editpost":
            // If an id get the post
            if(!empty($dollar->inputGet("id"))) {
                // We must have an numeric id 
                if(is_numeric($dollar->inputGet("id"))) {
                    $controller->getEditPostPage($dollar->inputGet("id"));
                } else {
                    $controller->getPostsPage();
                }
            } else {
                $controller->getPostsPage();
            }
        break;
        case "editpostpublish":
            $controller->getEditPostPublishPage($dollar->inputPost());
        break;
        case "addcomment":
            $controller->getAddCommentPage($dollar->inputPost());
        break;
        case "panel":
            $controller->getPanelPage();
        break;
        case "editprofile":
            $controller->getEditProfilePage($dollar->inputPost());
        case "uploadavatar":
            $controller->getUploadAvatarPage($_FILES, $dollar->inputPost());
        break;
        case "addusersocials":
            $controller->getAddUserSocialsPage($dollar->inputPost());
        break;
        case "updateusersocials":
            $controller->getUpdateUserSocialsPage($dollar->inputPost());
        break;
        case "deleteusersocial":
            $controller->getDeleteUserSocialPage($dollar->inputGet("user_id"), $dollar->inputGet("social_id"));
        break;
        case "uploadcv":
            $controller->getUploadCvPage($_FILES, $dollar->inputPost());
        break;
        case "commentmanager":
            $controller->getCommentManagerPage();
        break;
        case "confirmcomment":
            if(is_numeric($dollar->inputGet("id"))) {
                $controller->getActivateCommentPage($dollar->inputGet("id"));
            } else {
                $controller->getHomepage();
            }
        break;
        case "rejectcomment":
            if(is_numeric($dollar->inputGet("id"))) {
                $controller->getRejectCommentPage($dollar->inputGet("id"));
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
            $controller->getReadMessagePage($dollar->inputGet("id"));
        break;
        case "mainsettings":
            $controller->getMainSettingsPage($dollar->inputPost());
        break;
        case "permissionmanager":
            $controller->getPermissionsManagerPage();
        break;
        case "setpermission":
            $controller->getSetPermissionPage($dollar->inputPost());
        break;
        case "deletesocial":
            $controller->getDeleteSocialPage($dollar->inputGet("id"));
        break;
        case "updatesocial":
            $controller->getUpdateSocialPage($_FILES, $dollar->inputPost());
        break;
        case "postmanager":
            $controller->getPostManagerPage();
        break;
        case "deletepost":
            $controller->getDeletePostPage($dollar->inputGet("id"));
        break;
        default:
            $controller->getHomepage();
        }
    } else {
        $controller->getHomepage();
    }
