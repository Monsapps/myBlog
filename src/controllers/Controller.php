<?php
/**
 * This is the main controller
 */
declare(strict_types=1);

namespace Monsapp\Myblog\Controllers;

class Controller {

    private $config;
    private $title;
    private $keywords;
    private $descriptions;
    private $mainUser;
    private $twig;
    private $role;
    private $isAllowedToCRUD;
    private $userInfos;
    private $superGlobal;

    function __construct() {

        $this->config = new \Monsapp\Myblog\Utils\ConfigManager();

        $this->superGlobal = new \Monsapp\Myblog\Utils\SuperGlobal();

        $this->title = $this->config->getConfig("site_title");
        $this->keywords = $this->config->getConfig("site_keywords");
        $this->descriptions = $this->config->getConfig("site_descriptions");
        $this->mainUser = $this->config->getConfig("site_main_user_id");

        $loader = new \Twig\Loader\FilesystemLoader("src/views/");
        $this->twig = new \Twig\Environment($loader);

        // add variable currentPage for navigation menu
        $currentPage = (empty($this->superGlobal->getGetValue("page"))) ? "" : $this->superGlobal->getGetValue("page");
        $this->twig->addGlobal("currentPage", $currentPage);

        // add variable id_error if present (default: 0)
        $idError = (empty($this->superGlobal->getGetValue("error"))) ? "0" : $this->superGlobal->getGetValue("error");
        $this->twig->addGlobal("error", $idError);

        // add variable id_status if present (default: 0)
        $idStatus = (empty($this->superGlobal->getGetValue("status"))) ? "0" : $this->superGlobal->getGetValue("status");
        $this->twig->addGlobal("status", $idStatus);

        $this->isAllowedToCRUD = false;
        $this->role = -1;
        $this->userInfos = null;
        if((!empty($this->superGlobal->getCookieValue("email"))) && (!empty($this->superGlobal->getCookieValue("sessionid"))) ) {
            $userController = new UserController($this->superGlobal->getCookieValue("email"), $this->superGlobal->getCookieValue("sessionid"));
            $this->role = $userController->getUserRole();
            $this->isAllowedToCRUD = $userController->isAllowedToCrud();
            // securised userinfo with hashed string
            $this->userInfos = $userController->getUserInfos();
        }

        if(empty($this->superGlobal->getSessionValue("token"))) {
            $this->superGlobal->setSessionValue("token", bin2hex(openssl_random_pseudo_bytes(6)));
        }
    }

    /**
     * Controller for homepage
     */

    function getHomepage() {
        $user = new \Monsapp\Myblog\Models\User();
        $mainUser = $user->getUserInfos((int)$this->mainUser);
        $userSocials = $user->getUserSocials((int)$this->mainUser);
        $this->twig->display("index.html.twig", array(
                "title" => $this->title, 
                "navtitle" => $this->title, 
                "descriptions" => $this->descriptions,
                "keywords" => $this->keywords,
                "role" => $this->role,
                "user" => $this->userInfos,
                "main_user" => $mainUser,
                "userSocials" => $userSocials
            ));
    }

    /**
     * Controller for contact form
     */

    function getContactPage(array $postArray) {
        
        if(!empty($postArray["name"]) && !empty($postArray["surname"]) && !empty($postArray["email"]) && !empty($postArray["message"])) {
            $contact = new \Monsapp\Myblog\Controllers\ContactController();
            $user = new \Monsapp\Myblog\Models\User();

            $mainUser = $user->getUserInfos((int)$this->mainUser);

            if(!$contact->sendMail($mainUser["email"], $postArray["message"], $postArray["email"], $postArray["name"], $postArray["surname"])) {

                $contact->sendMessage($postArray["message"], $postArray["email"], $postArray["name"], $postArray["surname"]);
                $this->redirectTo("./index.php?status=1");

            }
        } else {
            $this->redirectTo("./index.php?error=1");
        }
    }

    /**
     * Controllers for Posts
     */

    function getPostsPage() {
        $post = new \Monsapp\Myblog\Models\Post();
        $posts = $post->getAllPosts();

        $this->twig->display("posts.html.twig", array(
            "title" => "Articles - " . $this->title, 
            "navtitle" => $this->title, 
            "descriptions" => $this->descriptions,
            "keywords" => $this->keywords,
            "role" => $this->role,
            "user" => $this->userInfos,
            "posts" => $posts,
            "token" => $this->superGlobal->getSessionValue("token")
        ));
    }

    function getPostPage(int $idPost) {
        $post = new \Monsapp\Myblog\Models\Post();
        $postInfos = $post->getPostInfos($idPost);

        $comment = new \Monsapp\Myblog\Models\Comment();
        $comments = $comment->getComments($idPost);

        $this->twig->display("post.html.twig", array(
            "title" => $postInfos["title"] . " - " . $this->title, 
            "navtitle" => $this->title, 
            "descriptions" => $this->descriptions,
            "keywords" => $postInfos["keywords"],
            "role" => $this->role,
            "is_allowed_to_crud" => $this->isAllowedToCRUD, 
            "user" => $this->userInfos,
            "post" => $postInfos,
            "comments" => $comments,
            "token" => $this->superGlobal->getSessionValue("token")
        ));
    }

    function getAddPostPage() {
        if((!empty($this->superGlobal->getGetValue("token")) && $this->superGlobal->getGetValue("token") == $this->superGlobal->getSessionValue("token")) && $this->isAllowedToCRUD) {
            $this->twig->display("addpost.html.twig", array(
                "title" => $this->title, 
                "navtitle" => $this->title, 
                "descriptions" => $this->descriptions,
                "keywords" => $this->keywords,
                "role" => $this->role,
                "user" => $this->userInfos,
                "token" => $this->superGlobal->getSessionValue("token")
            ));
        } else {
            $this->redirectTo("./index.php");
        }
    }

    function getPublishPage(array $postArray) {
        if($this->isAllowedToCRUD && ($postArray["token"] == $this->superGlobal->getSessionValue("token"))) {
            // We need to attach user id to a post
            $userInfos = $this->userInfos;

            $post = new \Monsapp\Myblog\Models\Post();
            $post->addPost((int)$userInfos["id"], $postArray["title"], $postArray["hat"], $postArray["content"], $postArray["keywords"]);

            $this->redirectTo("./index.php?page=post");
        } else {
            $this->redirectTo("./index.php");
        }
    }

    function getEditPostPage(int $idPost) {

        // We store all users to edit author's post
        $user = new \Monsapp\Myblog\Models\User();
        $allUsers = $user->getAllUsers();

        $post = new \Monsapp\Myblog\Models\Post();
        $postInfos = $post->getPostInfos($idPost);

        if((!empty($this->superGlobal->getGetValue("token")) && $this->superGlobal->getGetValue("token") == $this->superGlobal->getSessionValue("token")) && ($this->isAllowedToCRUD && (($this->userInfos["id"] == $postInfos["user_id"])) || ($this->role == 1))) {
            $this->twig->display("editpost.html.twig", array(
                "title" => $this->title, 
                "navtitle" => $this->title, 
                "descriptions" => $this->descriptions,
                "keywords" => $this->keywords,
                "role" => $this->role,
                "user" => $this->userInfos,
                "post" => $postInfos,
                "authors" => $allUsers,
                "token" => $this->superGlobal->getSessionValue("token")
            ));
        } else {
            $this->redirectTo("./index.php?page=post&id". $idPost);
        }
    }

    function getEditPostPublishPage(array $postArray) {
        if($this->isAllowedToCRUD && ($postArray["token"] == $this->superGlobal->getSessionValue("token"))) {
            $post = new \Monsapp\Myblog\Models\Post();
            $post->updatePost((int)$postArray["id"], (int)$postArray["author"], $postArray["title"], $postArray["hat"], $postArray["content"], $postArray["keywords"]);
            $this->redirectTo("./index.php?page=post&id=". $postArray["id"]);
        }

        $this->redirectTo("./index.php?page=post&id=". $postArray["id"]);
    }

    /**
     * Controllers for login/logout/register
     */

    function getConnectPage() {

        $this->twig->display("connect.html.twig", array(
            "title" => "Connexion - " . $this->title,
            "navtitle" => $this->title,
            "descriptions" => $this->descriptions,
            "keywords" => $this->keywords,
            "role" => $this->role
        ));
    }

    function getRegistrationPage(array $post) {

        $user = new \Monsapp\Myblog\Models\User();

        $name = $post["name"];
        $surname = $post["surname"];
        $email = $post["email"];
        $password = $post["password"];
        $retryPassword = $post["retrypassword"];

        if($password == $retryPassword) {
            if($user->userExist($email) != 0) {
                // if email already present in DB return to the registration page
                $this->redirectTo("./index.php?page=connect&error=2");
            } else {
                $date = date("Y-m-d H:i:s");
                $encryptedPassword = password_hash($password, PASSWORD_DEFAULT);
                $user->addUser($name, $surname, $email, $encryptedPassword, $date, "");
                $this->redirectTo("./index.php");
            }

        } else {
            $this->redirectTo("./index.php?page=connect&error=1");
        }
    }

    function getLoginPage(array $post) {

        $user = new \Monsapp\Myblog\Models\User();

        $email = $post["email"];
        $password = $post["password"];

        $userInfos = $user->getUserInfos($email);
        $hashedPassword = $userInfos["password"];

        if(password_verify($password, $hashedPassword)) {
            // clear token
            $this->superGlobal->unsetSession("token");
            // Set cookies to store email and hashed pass
            $this->superGlobal->setCookieValue("email", $email, time()+3600);
            // We store combination of userId & email hashed password to compare inside Autorisation Controller
            $hashedInfos = password_hash($userInfos["id"] . $userInfos["email"], PASSWORD_DEFAULT);
            $this->superGlobal->setCookieValue("sessionid", $hashedInfos, time()+3600);
            $this->redirectTo("./index.php");
        } else {
            $this->redirectTo("./index.php?page=connect&error=3");
        }
    }

    function getDisconnectPage() {
        // clear token
        session_destroy();
        // Set cookies to store empty
        $this->superGlobal->setCookieValue("email", "", time());
        $this->superGlobal->setCookieValue("sessionid", "", time());
        $this->redirectTo("./index.php");
    }

    /**
     * Controller for comments
     */

    function getAddCommentPage(array $postArray) {
        if(($this->role != -1) && ($postArray["token"] == $this->superGlobal->getSessionValue("token"))) {
            $comment = new \Monsapp\Myblog\Models\Comment();
            $comment->addComment((int)$postArray["post_id"], (int)$postArray["user_id"], $postArray["comment"]);
            $this->redirectTo("./index.php?page=post&id=". $postArray["post_id"] ."&status=1");
        } else {
            $this->redirectTo("./index.php?page=post&id=". $postArray["post_id"]);
        }
    }

    /**
     * Controller for panel
     */

    function getPanelPage() {
        if($this->role != -1) {
            $user = new \Monsapp\Myblog\Models\User();

            $userInfos = $this->userInfos;

            if($userInfos != null) {
                $social = new \Monsapp\Myblog\Models\Social();
                $socials = $social->getAllSocials();
                $userSocials = $user->getUserSocials((int)$this->userInfos["id"]);
                $this->twig->display("panel/index.html.twig", array(
                    "title" => "Panneau de configation - " . $this->title,
                    "navtitle" => $this->title,
                    "descriptions" => $this->descriptions,
                    "keywords" => $this->keywords,
                    "role" => $this->role,
                    "user" => $this->userInfos,
                    "socials" => $socials,
                    "userSocials" => $userSocials,
                    "token" => $this->superGlobal->getSessionValue("token")
                ));
            } else {
                $this->redirectTo("./index.php");
            }

        } else {
            $this->redirectTo("./index.php");
        }
    }

    function getEditProfilePage(array $postArray) {
        // only confirmed users and user himself can update infos
        if((isset($postArray["token"]) && $postArray["token"] == $this->superGlobal->getSessionValue("token")) && ($this->role != -1 && $this->userInfos["id"] == $postArray["id"])) {
            $user = new \Monsapp\Myblog\Models\User();
            $user->updateUser((int)$postArray["id"], $postArray["name"], $postArray["surname"], $postArray["hat"]);

            $this->redirectTo("./index.php?page=panel");
        } else {
            $this->redirectTo("./index.php");
        }
    }

    function getUploadAvatarPage(array $files, array $postArray) {
        // only confirmed users and user himself can upload avatar
        if((isset($postArray["token"]) && $postArray["token"] == $this->superGlobal->getSessionValue("token")) && ($this->role != -1 && $this->userInfos["id"] == $postArray["user_id"])) {
            $image = new \Monsapp\Myblog\Models\Image();
            $uploadDir = "./public/uploads/";
            $mimeType = mime_content_type($files['avatar']['tmp_name']);

            // checking the mime_type if <<image/...>>
            if(strpos($mimeType, "image") !== false) {

                // get the file extension for the mimetype
                preg_match("#/([a-z]{3,4})#", $mimeType, $fileExtension);

                // unique file name for id reference
                $encodedFileName = md5($postArray["user_id"]) .".". $fileExtension[1];

                $uploadFile = $uploadDir . $this->baseFilename($encodedFileName);
                if ($this->moveUploadedFile($files['avatar']['tmp_name'], $uploadFile)) {
                    // update or insert data image if user_image_id exist
                    if(!empty($postArray["user_image_file"])) {
                        $image->updateImage((int)$postArray["user_id"], $encodedFileName);

                        // if the file name is not the same name = delete
                        if(!$postArray["user_image_file"] != $encodedFileName) {
                            unlink($uploadDir . $postArray["user_image_file"]);
                        }
                    } else {
                        $image->setImage((int)$postArray["user_id"], $encodedFileName);
                    }
                    $this->redirectTo("./index.php?page=panel");
                } else {
                    $this->redirectTo("./index.php?page=panel&error=2");
                }
            } else {
                $this->redirectTo("./index.php?page=panel&error=1");
            }
        } else {
            $this->redirectTo("./index.php");
        }
    }

    function getAddUserSocialsPage(array $postArray) {
        // only confirmed user and user himself can add socials
        if((isset($postArray["token"]) && $postArray["token"] == $this->superGlobal->getSessionValue("token")) && ($this->role != -1 && $this->userInfos["id"] == $postArray["user_id"])) {
            $user = new \Monsapp\Myblog\Models\User();
            //todo get userid
            for($i = 0; $i < count($postArray["social_id"]); $i++) {
                $user->addSocial((int)$postArray["user_id"], (int)$postArray["social_id"][$i], $postArray["meta"][$i]);
            }
            $this->redirectTo("./index.php?page=panel");
        } else {
            $this->redirectTo("./index.php");
        }
    }

    function getUpdateUserSocialsPage(array $postArray) {
        // only confirmed user and user himself can update socials
        if((isset($postArray["token"]) && $postArray["token"] == $this->superGlobal->getSessionValue("token")) && ($this->role != -1 && $this->userInfos["id"] == $postArray["user_id"])) {

            $user = new \Monsapp\Myblog\Models\User();

            for($i = 0; $i < count($postArray["social_id"]); $i++) {
                $user->updateSocial((int)$postArray["social_id"][$i], $postArray["meta"][$i]);
            }
            $this->redirectTo("./index.php?page=panel");
        } else {
            $this->redirectTo("./index.php");
        }
    }

    function getDeleteUserSocialPage(int $userId, int $socialId) {
        // only confirmed user and user himself can delete socials
        if((!empty($this->superGlobal->getGetValue("token")) && $this->superGlobal->getGetValue("token") == $this->superGlobal->getSessionValue("token")) && ($this->role != -1 && $this->userInfos["id"] == $userId)) {
            $user = new \Monsapp\Myblog\Models\User();
            $user->deleteSocial($socialId);
            $this->redirectTo("./index.php?page=panel");
        } else {
            $this->redirectTo("./index.php");
        }
    }

    function getUploadCvPage(array $files, array $postArray) {
        // only admin can upload cv
        if((isset($postArray["token"]) && $postArray["token"] == $this->superGlobal->getSessionValue("token")) && $this->role == 1) {
            $image = new \Monsapp\Myblog\Models\CurriculumVitae();
            $uploadDir = "./public/uploads/";
    
            $mimeType = mime_content_type($files['cv']['tmp_name']);
            if(strpos($mimeType, "application/pdf") !== false) {
                // encode pdf file to store on server
                $encodedFileName = md5($postArray["user_id"]) .".pdf";
                $uploadFile = $uploadDir . $this->baseFilename($encodedFileName);
                if ($this->moveUploadedFile($files['cv']['tmp_name'], $uploadFile)) {
                    if(empty($postArray["user_cv_name"])) {
                        $image->setCv((int)$postArray["user_id"], $encodedFileName);
                    }
                    $this->redirectTo("./index.php?page=panel");
                } else {
                    $this->redirectTo("./index.php?page=panel&error=2");
                }
            } else {
                $this->redirectTo("./index.php?page=panel&error=1");
            }
        } else {
            $this->redirectTo("./index.php?page=panel");
        }
    }

    function getSettingsManagerPage() {

        // only admin can edit main settings
        if($this->role == 1) {
            $user = new \Monsapp\Myblog\Models\User();
            $allUsers = $user->getAllUsers();
            $social = new \Monsapp\Myblog\Models\Social();
            $socials = $social->getAllSocials();
            $this->twig->display("panel/settings.html.twig", array(
                "title" => "Préférences générales - " . $this->title,
                "navtitle" => $this->title,
                "descriptions" => $this->descriptions,
                "keywords" => $this->keywords,
                "role" => $this->role,
                "users" => $allUsers,
                "user" => $this->userInfos,
                "main_user_id" => $this->mainUser,
                "socials" => $socials,
                "token" => $this->superGlobal->getSessionValue("token")
            ));
        } else {
            $this->redirectTo("./index.php");
        }
    }

    function getMainSettingsPage(array $post) {
        // only admin can edit main settings
        if((isset($post["token"]) && $post["token"] == $this->superGlobal->getSessionValue("token")) && $this->role == 1) {
            if(!empty($post["site_title"]) && $this->title != $post["site_title"]) {
                $this->config->editConfig("site_title", $post["site_title"]);
            }

            if(!empty($post["site_description"]) && $this->descriptions != $post["site_description"]) {
                $this->config->editConfig("site_descriptions", $post["site_description"]);
            }

            if(!empty($post["site_keywords"]) && $this->keywords != $post["site_keywords"]) {
                $this->config->editConfig("site_keywords", $post["site_keywords"]);
            }

            if(!empty($post["main_user"]) && $this->mainUser != $post["main_user"]) {
                $this->config->editConfig("site_main_user_id", $post["main_user"]);
            }

            $this->redirectTo("./index.php?page=settingsmanager");
        } else {
            $this->redirectTo("./index.php");
        }
    }

    function getCommentManagerPage() {
        // only admin can crud comment
        if($this->role == 1) {
            $comment = new \Monsapp\Myblog\Models\Comment();
            $comments = $comment->getPendingComments();
            $this->twig->display("panel/comment.html.twig", array(
                "title" => "Gestion des commentaires - " . $this->title,
                "navtitle" => $this->title,
                "descriptions" => $this->descriptions,
                "keywords" => $this->keywords,
                "role" => $this->role,
                "user" => $this->userInfos,
                "comments" => $comments,
                "token" => $this->superGlobal->getSessionValue("token")
            ));
        } else {
            $this->redirectTo("./index.php");
        }
    }

    function getActivateCommentPage(int $commentId) {
        if((!empty($this->superGlobal->getGetValue("token")) && $this->superGlobal->getGetValue("token") == $this->superGlobal->getSessionValue("token")) && $this->role == 1) {
            $comment = new \Monsapp\Myblog\Models\Comment();
            $comment->activateComment($commentId);
            $this->redirectTo("./index.php?page=commentmanager");
        } else {
            $this->redirectTo("./index.php?page=panel");
        }
    }

    function getRejectCommentPage(int $commentId) {
        if((!empty($this->superGlobal->getGetValue("token")) && $this->superGlobal->getGetValue("token") == $this->superGlobal->getSessionValue("token")) && $this->role == 1) {
            $comment = new \Monsapp\Myblog\Models\Comment();
            $comment->rejectComment($commentId);
            $this->redirectTo("./index.php?page=commentmanager");
        } else {
            $this->redirectTo("./index.php?page=panel");
        }
    }

    function getPermissionsManagerPage() {
        if($this->role == 1) {
            $user = new \Monsapp\Myblog\Models\User();
            $allUsers = $user->getAllUsers();

            $role = new \Monsapp\Myblog\Models\Role();
            $allRoles = $role->getRoles();
            $this->twig->display("panel/users.html.twig", array(
                "title" => "Gestions des permissions - " . $this->title,
                "navtitle" => $this->title,
                "descriptions" => $this->descriptions,
                "keywords" => $this->keywords,
                "role" => $this->role,
                "user" => $this->userInfos,
                "users" => $allUsers,
                "main_user_id" => $this->mainUser,
                "roles" => $allRoles,
                "token" => $this->superGlobal->getSessionValue("token")
            ));
        } else {
            $this->redirectTo("./index.php");
        }
    }

    function getSetPermissionPage(array $postArray) {
        if((isset($postArray["token"]) && $postArray["token"] == $this->superGlobal->getSessionValue("token")) && $this->role == 1) {
            $user = new \Monsapp\Myblog\Models\User();
            $user->setPermission((int)$postArray["user_id"], (int)$postArray["role_id"]);
            $this->redirectTo("./index.php?page=permissionmanager");
        } else {
            $this->redirectTo("./index.php");
        }
    }

    function getDeleteSocialPage(int $idSocial) {
        if((!empty($this->superGlobal->getGetValue("token")) && $this->superGlobal->getGetValue("token") == $this->superGlobal->getSessionValue("token")) && $this->role == 1) {
            $social = new \Monsapp\Myblog\Models\Social();
            $social->deleteSocial($idSocial);
            $this->redirectTo("./index.php?page=settingsmanager");
        } else {
            $this->redirectTo("./index.php");
        }
    }

    function getUpdateSocialPage(array $files, array $postArray) {
        if((isset($postArray["token"]) && $postArray["token"] == $this->superGlobal->getSessionValue("token")) && $this->role == 1) {
            $social = new \Monsapp\Myblog\Models\Social();

            $uploadDir = "./public/images/socials/";

            for($i = 0; $i < count($postArray["id"]); $i++) {

                $name = $postArray["name"][$i];

                // if id != 0 update table
                if(!empty($postArray["id"][$i])) {
                    // if an new image is present, upload new one
                    if(!empty($files["image"]["name"][$i])) {

                        $imageTmpName = $files["image"]['tmp_name'][$i];
                        $mimeType = mime_content_type($imageTmpName);

                        // checking the mime_type if <<image/...>>
                        if(strpos($mimeType, "image") !== false) {
                            // get the file extension for the mimetype
                            preg_match("#/([a-z]{3,4})#", $mimeType, $fileExtension);
            
                            $filename = $name .".". $fileExtension[1];
            
                            $uploadFile = $uploadDir . $this->baseFilename($filename);
                            if ($this->moveUploadedFile($imageTmpName, $uploadFile)) {
                                $social->updateSocialImage((int)$postArray["id"][$i], $name, $filename);
                            } else {
                                $this->redirectTo("./index.php?page=settingsmanager&error=2");
                            }
                        } else {
                            $this->redirectTo("./index.php?page=settingsmanager&error=1");
                        }
                    } else {
                        $social->updateSocial((int)$postArray["id"][$i], $name);
                    }

                } else {
                    $imageTmpName = $files["image"]["tmp_name"][$i];
                    $mimeType = mime_content_type($imageTmpName);

                    // checking the mime_type if <<image/...>>
                    if(strpos($mimeType, "image") !== false) {

                        // get the file extension for the mimetype
                        preg_match("#/([a-z]{3,4})#", $mimeType, $fileExtension);
        
                        $filename = $name .".". $fileExtension[1];
        
                        $uploadFile = $uploadDir . $this->baseFilename($filename);
                        if ($this->moveUploadedFile($imageTmpName, $uploadFile)) {
                            $social->addSocial($name, $filename);
                        } else {
                            $this->redirectTo("./index.php?page=settingsmanager&error=2");
                        }
                    } else {
                        $this->redirectTo("./index.php?page=settingsmanager&error=1");
                    }

                }
            }

            $this->redirectTo("./index.php?page=settingsmanager");
        } else {
            $this->redirectTo("./index.php");
        }
    }

    function getPostManagerPage() {
        $post = new \Monsapp\Myblog\Models\Post();
        $posts = $post->getAllPosts();
        if($this->role >= 1) {
            $this->twig->display("panel/posts.html.twig", array(
                "title" => "Gestions des articles - " . $this->title,
                "navtitle" => $this->title,
                "descriptions" => $this->descriptions,
                "keywords" => $this->keywords,
                "role" => $this->role,
                "user" => $this->userInfos,
                "posts" => $posts,
                "token" => $this->superGlobal->getSessionValue("token")
            ));
        } else {
            $this->redirectTo("./index.php");
        }
    }

    function getDeletePostPage(int $idPost) {
        $post = new \Monsapp\Myblog\Models\Post();
        $postInfo = $post->getPostInfos($idPost);
        if((!empty($this->superGlobal->getGetValue("token")) && $this->superGlobal->getGetValue("token") == $this->superGlobal->getSessionValue("token")) && (($this->role == 1) || (($this->role == 2) && ($this->userInfos == $postInfo["used_id"])))) {
            $post->deletePost((int)$idPost);
            $this->redirectTo("./index.php?page=postmanager");
        /*} elseif($this->role == 2 && $this->userInfos == $postInfo["used_id"]) {
            $post->deletePost((int)$id);
            Header("Location: ./index.php?page=postmanager");
            exit;*/
         }else {
            $this->redirectTo("./index.php");
        }
    }

    function getContactManagerPage() {
        $contact = new \Monsapp\Myblog\Models\Contact();
        $messages = $contact->getAllContactMessage();
        if($this->role >= 1) {
            $this->twig->display("panel/contact.html.twig", array(
                "title" => "Contact - " . $this->title,
                "navtitle" => $this->title,
                "descriptions" => $this->descriptions,
                "keywords" => $this->keywords,
                "role" => $this->role,
                "user" => $this->userInfos,
                "messages" => $messages,
                "token" => $this->superGlobal->getSessionValue("token")
            ));
        } else {
            $this->redirectTo("./index.php");
        }
    }

    function getReadMessagePage(int $idMessage) {
        if((!empty($this->superGlobal->getGetValue("token")) && $this->superGlobal->getGetValue("token") == $this->superGlobal->getSessionValue("token")) && ($this->role == 1)) {
            $contact = new \Monsapp\Myblog\Models\Contact();
            $contact->updateStatus((int) $idMessage);
            $this->redirectTo("./index.php?page=contactmanager");
        } else {
            $this->redirectTo("./index.php");
        }
    }

    function redirectTo(string $urlAddress) {
        // Javascript redirection or PHP ??
        /*?>
            <html>
            <head>
                <script>
                function Redirection(){
                    document.location.href="<?= filter_var($urlAddress, FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?>";
                }
                </script>
            </head>
            <body onLoad="Redirection()">
            </body>
            </html>
        <?php*/
        Header("Location: ". $urlAddress);
    }

    private function moveUploadedFile(string $filename, string $destination) {
        return move_uploaded_file($filename, $destination);
    }

    private function baseFilename(string $path) {
        return basename($path);
    }
}
