<?php
// CSRF fix
session_start();

// Load helpers
require "vendor/autoload.php";

//include "./src/routes/routes.php";

$controller = new Monsapp\Myblog\Controllers\Controller();
$superGlobal = new Monsapp\Myblog\Utils\SuperGlobal();

if(!empty($superGlobal->getGetValue("page"))) {
    switch($superGlobal->getGetValue("page")) {
        case "post":
            // If an id get the post
            if(!empty($superGlobal->getGetValue("id"))) {
                // We must have an numeric id 
                if(is_numeric($superGlobal->getGetValue("id"))) {
                    $controller->getPostPage($superGlobal->getGetValue("id"));
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
            $controller->getLoginPage($superGlobal->getPostValue());
        break;
        case "register":
            if(!empty($superGlobal->getPostValue())) {
                $controller->getRegistrationPage($superGlobal->getPostValue());
            } else {
                $controller->getConnectPage();
            }
        break;
        case "contact":
            $controller->getContactPage($superGlobal->getPostValue());
        break;
        case "addpost":
            $controller->getAddPostPage();
        break;
        case "publish":
            $controller->getPublishPage($superGlobal->getPostValue());
        break;
        case "editpost":
            // If an id get the post
            if(!empty($superGlobal->getGetValue("id"))) {
                // We must have an numeric id 
                if(is_numeric($superGlobal->getGetValue("id"))) {
                    $controller->getEditPostPage($superGlobal->getGetValue("id"));
                } else {
                    $controller->getPostsPage();
                }
            } else {
                $controller->getPostsPage();
            }
        break;
        case "editpostpublish":
            $controller->getEditPostPublishPage($superGlobal->getPostValue());
        break;
        case "addcomment":
            $controller->getAddCommentPage($superGlobal->getPostValue());
        break;
        case "panel":
            $controller->getPanelPage();
        break;
        case "editprofile":
            $controller->getEditProfilePage($superGlobal->getPostValue());
        case "uploadavatar":
            $controller->getUploadAvatarPage($superGlobal->getFilesValue(), $superGlobal->getPostValue());
        break;
        case "addusersocials":
            $controller->getAddUserSocialsPage($superGlobal->getPostValue());
        break;
        case "updateusersocials":
            $controller->getUpdateUserSocialsPage($superGlobal->getPostValue());
        break;
        case "deleteusersocial":
            $controller->getDeleteUserSocialPage($superGlobal->getGetValue("user_id"), $superGlobal->getGetValue("social_id"));
        break;
        case "uploadcv":
            $controller->getUploadCvPage($superGlobal->getFilesValue(), $superGlobal->getPostValue());
        break;
        case "commentmanager":
            $controller->getCommentManagerPage();
        break;
        case "confirmcomment":
            if(is_numeric($superGlobal->getGetValue("id"))) {
                $controller->getActivateCommentPage($superGlobal->getGetValue("id"));
            } else {
                $controller->getHomepage();
            }
        break;
        case "rejectcomment":
            if(is_numeric($superGlobal->getGetValue("id"))) {
                $controller->getRejectCommentPage($superGlobal->getGetValue("id"));
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
            $controller->getReadMessagePage($superGlobal->getGetValue("id"));
        break;
        case "mainsettings":
            $controller->getMainSettingsPage($superGlobal->getPostValue());
        break;
        case "permissionmanager":
            $controller->getPermissionsManagerPage();
        break;
        case "setpermission":
            $controller->getSetPermissionPage($superGlobal->getPostValue());
        break;
        case "deletesocial":
            $controller->getDeleteSocialPage($superGlobal->getGetValue("id"));
        break;
        case "updatesocial":
            $controller->getUpdateSocialPage($superGlobal->getFilesValue(), $superGlobal->getPostValue());
        break;
        case "postmanager":
            $controller->getPostManagerPage();
        break;
        case "deletepost":
            $controller->getDeletePostPage($superGlobal->getGetValue("id"));
        break;
        default:
            $controller->getHomepage();
        }
} else {
    $controller->getHomepage();
}
