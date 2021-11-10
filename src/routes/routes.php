<?php
/**
*  This is the main router for browsing page
*/

$controller = new Monsapp\Myblog\Controllers\Controller();
$contactController = new Monsapp\Myblog\Controllers\ContactController();
$postController = new Monsapp\Myblog\Controllers\PostController();
$commentController = new Monsapp\Myblog\Controllers\CommentController();
$socialController = new Monsapp\Myblog\Controllers\SocialController();
$superGlobal = new Monsapp\Myblog\Utils\SuperGlobal();

if(!empty($superGlobal->getGetValue("page"))) {
    switch($superGlobal->getGetValue("page")) {
        case "post":
            // If an id get the post
            if(!empty($superGlobal->getGetValue("id"))) {
                // We must have an numeric id 
                if(is_numeric($superGlobal->getGetValue("id"))) {
                    $postController->getPostPage($superGlobal->getGetValue("id"));
                } else {
                    $postController->getPostsPage();
                }
            } else {
                $postController->getPostsPage();
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
            $contactController->getContactPage($superGlobal->getPostValue());
        break;
        case "addpost":
            $postController->getAddPostPage();
        break;
        case "publish":
            $postController->getPublishPage($superGlobal->getPostValue());
        break;
        case "editpost":
            // If an id get the post
            if(!empty($superGlobal->getGetValue("id"))) {
                // We must have an numeric id 
                if(is_numeric($superGlobal->getGetValue("id"))) {
                    $postController->getEditPostPage($superGlobal->getGetValue("id"));
                } else {
                    $postController->getPostsPage();
                }
            } else {
                $postController->getPostsPage();
            }
        break;
        case "editpostpublish":
            $postController->getEditPostPublishPage($superGlobal->getPostValue());
        break;
        case "addcomment":
            $commentController->getAddCommentPage($superGlobal->getPostValue());
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
            $socialController->getAddUserSocialsPage($superGlobal->getPostValue());
        break;
        case "updateusersocials":
            $socialController->getUpdateUserSocialsPage($superGlobal->getPostValue());
        break;
        case "deleteusersocial":
            $socialController->getDeleteUserSocialPage($superGlobal->getGetValue("user_id"), $superGlobal->getGetValue("social_id"));
        break;
        case "uploadcv":
            $controller->getUploadCvPage($superGlobal->getFilesValue(), $superGlobal->getPostValue());
        break;
        case "commentmanager":
            $commentController->getCommentManagerPage();
        break;
        case "confirmcomment":
            if(is_numeric($superGlobal->getGetValue("id"))) {
                $commentController->getActivateCommentPage($superGlobal->getGetValue("id"));
            } else {
                $controller->getHomepage();
            }
        break;
        case "rejectcomment":
            if(is_numeric($superGlobal->getGetValue("id"))) {
                $commentController->getRejectCommentPage($superGlobal->getGetValue("id"));
            } else {
                $controller->getHomepage();
            }
        break;
        case "settingsmanager":
            $controller->getSettingsManagerPage();
        break;
        case "contactmanager":
            $contactController->getContactManagerPage();
        break;
        case "readmessage":
            $contactController->getReadMessagePage($superGlobal->getGetValue("id"));
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
            $socialController->getDeleteSocialPage($superGlobal->getGetValue("id"));
        break;
        case "updatesocial":
            $socialController->getUpdateSocialPage($superGlobal->getFilesValue(), $superGlobal->getPostValue());
        break;
        case "postmanager":
            $postController->getPostManagerPage();
        break;
        case "deletepost":
            $postController->getDeletePostPage($superGlobal->getGetValue("id"));
        break;
        default:
            $controller->getHomepage();
        }
    } else {
        $controller->getHomepage();
    }
