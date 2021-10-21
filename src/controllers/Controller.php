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
    private $mainUser;
    private $twig;
    private $role;
    private $isAllowedToCRUD;
    private $userInfos;

    function __construct() {

        $config = new \Monsapp\Myblog\Utils\ConfigManager();

        $this->title = $config->getConfig("site_title");
        $this->keywords = $config->getConfig("site_keywords");
        $this->descriptions = $config->getConfig("site_descriptions");
        $this->mainUser = $config->getConfig("site_main_user_id");

        $loader = new \Twig\Loader\FilesystemLoader("src/views/");
        $this->twig = new \Twig\Environment($loader);

        // add variable currentPage for navigation menu
        $currentPage = (empty($_GET["page"])) ? "" : $_GET["page"];
        $this->twig->addGlobal("currentPage", $currentPage);

        // add variable id_error if present (default: 0)
        $idError = (empty($_GET["error"])) ? "0" : $_GET["error"];
        $this->twig->addGlobal("error", $idError);

        // add variable id_status if present (default: 0)
        $idStatus = (empty($_GET["status"])) ? "0" : $_GET["status"];
        $this->twig->addGlobal("status", $idStatus);

        $this->isAllowedToCRUD = false;
        $this->role = -1;
        $this->userInfos = null;
        if(((isset($_COOKIE["email"])) && !empty($_COOKIE["email"])) && (isset($_COOKIE["sessionid"])) ) {
            $userController = new UserController($_COOKIE["email"], $_COOKIE["sessionid"]);
            $this->role = $userController->getUserRole();
            $this->isAllowedToCRUD = $userController->isAllowedToCrud();
            // securised userinfo with hashed string
            $this->userInfos = $userController->getUserInfos();
        }
    }

    /**
     * Controller for homepage
     */

    function getHomepage() {
        $user = new \Monsapp\Myblog\Models\User();
        $mainUser = $user->getUserInfos((int)$this->mainUser);
        $userSocials = $user->getUserSocials((int)$this->mainUser);
        echo $this->twig->render("index.html.twig", array(
                "title" => $this->title, 
                "navtitle" => $this->title, 
                "desciption" => $this->descriptions,
                "keywords" => $this->keywords,
                "role" => $this->role,
                "user" => $this->userInfos,
                "main_user" => $mainUser,
                "userSocials" => $userSocials
            ));
    }

    /**
     * Controllers for Posts
     */

    function getPostsPage() {
        $post = new \Monsapp\Myblog\Models\Post();
        $posts = $post->getAllPosts();

        echo $this->twig->render("posts.html.twig", array(
            "title" => "Articles - " . $this->title, 
            "navtitle" => $this->title, 
            "desciption" => $this->descriptions,
            "keywords" => $this->keywords,
            "role" => $this->role,
            "user" => $this->userInfos,
            "posts" => $posts
        ));
    }

    function getPostPage(int $id) {
        $post = new \Monsapp\Myblog\Models\Post();
        $postInfos = $post->getPostInfos($id);

        $comment = new \Monsapp\Myblog\Models\Comment();
        $comments = $comment->getComments($id);

        echo $this->twig->render("post.html.twig", array(
            "title" => $postInfos["title"] . " - " . $this->title, 
            "navtitle" => $this->title, 
            "desciption" => $this->descriptions,
            "keywords" => $postInfos["keywords"],
            "role" => $this->role,
            "is_allowed_to_crud" => $this->isAllowedToCRUD, 
            "user" => $this->userInfos,
            "post" => $postInfos,
            "comments" => $comments
        ));
    }

    function getAddPostPage() {
        if($this->isAllowedToCRUD) {
            echo $this->twig->render("addpost.html.twig", array(
                "title" => $this->title, 
                "navtitle" => $this->title, 
                "desciption" => $this->descriptions,
                "keywords" => $this->keywords,
                "role" => $this->role,
                "user" => $this->userInfos
            ));
        } else {
            Header("Location: ./index.php");
            exit;
        }
    }

    function getPublishPage(array $postArray) {
        if($this->isAllowedToCRUD) {
            // We need to attach user id to a post
            $userInfos = $this->userInfos;

            $post = new \Monsapp\Myblog\Models\Post();
            $post->addPost((int)$userInfos["id"], $postArray["title"], $postArray["hat"], $postArray["content"], $postArray["keywords"]);

            Header("Location: ./index.php?page=post");
            exit;
        } else {
            Header("Location: ./index.php");
            exit;
        }
    }

    function getEditPostPage(int $id) {
        // We need to verify user id to edit post
        $userInfos = $this->userInfos;

        // We store all users for edit author's post
        $user = new \Monsapp\Myblog\Models\User();
        $allUsers = $user->getAllUsers();

        $post = new \Monsapp\Myblog\Models\Post();
        $postInfos = $post->getPostInfos($id);

        if($this->isAllowedToCRUD && ($userInfos["id"] == $postInfos["user_id"])) {
            echo $this->twig->render("editpost.html.twig", array(
                "title" => $this->title, 
                "navtitle" => $this->title, 
                "desciption" => $this->descriptions,
                "keywords" => $this->keywords,
                "role" => $this->role,
                "user" => $this->userInfos,
                "post" => $postInfos,
                "authors" => $allUsers
            ));
        } else {
            Header("Location: ./index.php?page=post&id". $id);
            exit;
        }
    }

    function getEditPostPublishPage(array $postArray) {
        if($this->isAllowedToCRUD) {
            $post = new \Monsapp\Myblog\Models\Post();
            // IMPORTANT mettre le controlleur isAllowedToEditPost()
            $post->updatePost((int)$postArray["id"], (int)$postArray["author"], $postArray["title"], $postArray["hat"], $postArray["content"], $postArray["keywords"]);
            Header("Location: ./index.php?page=post&id=". $postArray["id"]);
            exit;
        } else {
            Header("Location: ./index.php?page=post&id=". $postArray["id"]);
        exit;
        }
    }

    /**
     * Controllers for login/logout/register
     */

    function getConnectPage() {

        echo $this->twig->render("connect.html.twig", array(
            "title" => "Connexion - " . $this->title,
            "navtitle" => $this->title,
            "desciption" => $this->descriptions,
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
                Header("Location: ./index.php?page=connect&error=2");
                exit;
            } else {
                $date = date("Y-m-d H:i:s");
                $encryptedPassword = password_hash($password, PASSWORD_DEFAULT);
                $user->addUser($name, $surname, $email, $encryptedPassword, $date, "");
                Header("Location: ./index.php");
                exit;
            }

        } else {
            Header("Location: ./index.php?page=connect&error=1");
            exit;
        }
    }

    function getLoginPage(array $post) {

        $user = new \Monsapp\Myblog\Models\User();

        $email = $post["email"];
        $password = $post["password"];

        $userInfos = $user->getUserInfos($email);
        $hashedPassword = $userInfos["password"];

        if(password_verify($password, $hashedPassword)) {
            // Set cookies to store email and hashed pass
            setcookie("email", $email, time()+3600);
            // We store combination of userId & email hashed password to compare inside Autorisation Controller
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

    /**
     * Controller for comments
     */

    function getAddCommentPage(array $postArray) {
        if($this->role != -1) {
            $comment = new \Monsapp\Myblog\Models\Comment();
            $comment->addComment((int)$postArray["post_id"], (int)$postArray["user_id"], $postArray["comment"]);
            Header("Location: ./index.php?page=post&id=". $postArray["post_id"] ."&status=1");
            exit;
        } else {
            Header("Location: ./index.php?page=post&id=". $postArray["post_id"]);
            exit;
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
                //todo get info from getUserFullInfos
                $userId = (int)$userInfos["id"];
                $social = new \Monsapp\Myblog\Models\Social();
                $socials = $social->getAllSocials();
                $userSocials = $user->getUserSocials($userId);
                echo $this->twig->render("panel/index.html.twig", array(
                    "title" => "Panneau de configation - " . $this->title,
                    "navtitle" => $this->title,
                    "description" => $this->descriptions,
                    "keywords" => $this->keywords,
                    "role" => $this->role,
                    "user" => $userInfos,
                    "socials" => $socials,
                    "userSocials" => $userSocials
                ));
            } else {
                Header("Location: ./index.php");
                exit;
            }

        } else {
            Header("Location: ./index.php");
            exit;
        }
    }

    function getEditProfilePage(array $postArray) {
        $userInfos = $this->userInfos();
        // only confirmed users and user himself can update infos
        if($this->role != -1 && $userInfos["id"] == $postArray["id"]) {
            $user = new \Monsapp\Myblog\Models\User();
            $user->updateUser((int)$postArray["id"], $postArray["name"], $postArray["surname"], $postArray["hat"]);

            Header("Location: ./index.php?page=panel");
            exit;
        } else {
            Header("Location: ./index.php");
            exit;
        }
    }

    function getUploadAvatarPage(array $files, array $postArray) {
        $userInfos = $this->userInfos;
        // only confirmed users and user himself can upload avatar
        if($this->role != -1 && $userInfos["id"] == $postArray["user_id"]) {
            $image = new \Monsapp\Myblog\Models\Image();
            $uploadDir = "./public/uploads/";
            $mimeType = mime_content_type($files['avatar']['tmp_name']);

            // checking the mime_type if <<image/...>>
            if(strpos($mimeType, "image") !== false) {

                // get the file extension for the mimetype
                preg_match("#/([a-z]{3,4})#", $mimeType, $fileExtension);

                // unique file name for id reference
                $encodedFileName = md5($postArray["user_id"]) .".". $fileExtension[1];

                $uploadFile = $uploadDir . basename($encodedFileName);
                if (move_uploaded_file($files['avatar']['tmp_name'], $uploadFile)) {
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
                    Header("Location: ./index.php?page=panel");
                    exit;
                } else {
                    Header("Location: ./index.php?page=panel&error=2");
                    exit;
                }
            } else {
                Header("Location: ./index.php?page=panel&error=1");
                exit;
            }
        } else {
            Header("Location: ./index.php");
            exit;
        }
    }

    function getAddUserSocialsPage(array $postArray) {

        $userInfos = $this->userInfos;

        // only confirmed user and user himself can add socials
        if($this->role != -1 && $userInfos["id"] == $postArray["user_id"]) {
            $user = new \Monsapp\Myblog\Models\User();
            //todo get userid
            for($i = 0; $i < count($postArray["social_id"]); $i++) {
                $user->addSocial((int)$postArray["user_id"], (int)$postArray["social_id"][$i], $postArray["meta"][$i]);
            }
            Header("Location: ./index.php?page=panel");
            exit;
        } else {
            Header("Location: ./index.php");
            exit;
        }
    }

    function getUpdateUserSocialsPage(array $postArray) {

        $userInfos = $this->userInfos;
        // only confirmed user and user himself can update socials
        if($this->role != -1 && $userInfos["id"] == $postArray["user_id"]) {

            $user = new \Monsapp\Myblog\Models\User();

            for($i = 0; $i < count($postArray["social_id"]); $i++) {
                $user->updateSocial((int)$postArray["social_id"][$i], $postArray["meta"][$i]);
            }
            Header("Location: ./index.php?page=panel");
            exit;
        } else {
            Header("Location: ./index.php");
            exit;
        }
    }

    function getDeleteUserSocialPage(int $userId, int $socialId) {
        $userInfos = $this->userInfos;
        // only confirmed user and user himself can delete socials
        if($this->role != -1 && $userInfos["id"] == $userId) {
            $user = new \Monsapp\Myblog\Models\User();
            $user->deleteSocial($socialId);
            Header("Location: ./index.php?page=panel");
            exit;
        } else {
            Header("Location: ./index.php");
            exit;
        }
    }

    function getUploadCvPage(array $files, array $postArray) {
        // only admin can upload cv
        if($this->role == 1) {
            $image = new \Monsapp\Myblog\Models\CurriculumVitae();
            $uploadDir = "./public/uploads/";
    
            $mimeType = mime_content_type($files['cv']['tmp_name']);
            if(strpos($mimeType, "application/pdf") !== false) {
                // encode pdf file to store on server
                $encodedFileName = md5($postArray["user_id"]) .".pdf";
                $uploadFile = $uploadDir . basename($encodedFileName);
                if (move_uploaded_file($files['cv']['tmp_name'], $uploadFile)) {
                    if(empty($postArray["user_cv_name"])) {
                        $image->setCv((int)$postArray["user_id"], $encodedFileName);
                    }
                } else {
                    Header("Location: ./index.php?page=panel&error=2");
                    exit;
                }
                Header("Location: ./index.php?page=panel");
                exit;
            } else {
                Header("Location: ./index.php?page=panel&error=1");
                exit;
            }
        } else {
            Header("Location: ./index.php?page=panel");
            exit;
        }
    }

    function getSettingsManagerPage() {

        // only admin can edit main settings
        if($this->role == 1) {
            $user = new \Monsapp\Myblog\Models\User();
            $allUsers = $user->getAllUsers();
            $social = new \Monsapp\Myblog\Models\Social();
            $socials = $social->getAllSocials();
            echo $this->twig->render("panel/settings.html.twig", array(
                "title" => "Préférences générales - " . $this->title,
                "navtitle" => $this->title,
                "description" => $this->descriptions,
                "keywords" => $this->keywords,
                "role" => $this->role,
                "authors" => $allUsers,
                "main_user_id" => $this->mainUser,
                "socials" => $socials
            ));
        } else {
            Header("Location: ./index.php");
            exit;
        }
    }

    function getMainSettingsPage(array $post) {
        // only admin can edit main settings
        if($this->role == 1) {
            if(!empty($post["site_title"]) && $this->title != $post["site_title"]) {
                $this->config->editConfig("site_title", $post["site_title"]);
            }

            if(!empty($post["site_descriptions"]) && $this->descriptions != $post["site_descriptions"]) {
                $this->config->editConfig("site_descriptions", $post["site_description"]);
            }

            if(!empty($post["site_keywords"]) && $this->keywords != $post["site_keywords"]) {
                $this->config->editConfig("site_keywords", $post["site_keywords"]);
            }

            if(!empty($post["main_user"]) && $this->mainUser != $post["main_user"]) {
                $this->config->editConfig("site_main_user_id", $post["main_user"]);
            }

            Header("Location: ./index.php?page=settingsmanager");
            exit;
        } else {
            Header("Location: ./index.php");
            exit;
        }
    }

    function getCommentManagerPage() {
        // only admin can crud comment
        if($this->role == 1) {
            $comment = new \Monsapp\Myblog\Models\Comment();
            $comments = $comment->getPendingComments();
            echo $this->twig->render("panel/comment.html.twig", array(
                "title" => "Gestion des commentaires - " . $this->title,
                "navtitle" => $this->title,
                "description" => $this->descriptions,
                "keywords" => $this->keywords,
                "role" => $this->role,
                "comments" => $comments
            ));
        } else {
            Header("Location: ./index.php");
            exit;
        }
    }

    function getActivateCommentPage(int $commentId) {
        if($this->role == 1) {
            $comment = new \Monsapp\Myblog\Models\Comment();
            $comment->activateComment($commentId);
            Header("Location: ./index.php?page=commentmanager");
            exit;
        } else {
            Header("Location: ./index.php?page=panel");
            exit;
        }
    }

    function getRejectCommentPage(int $commentId) {
        if($this->role == 1) {
            $comment = new \Monsapp\Myblog\Models\Comment();
            $comment->rejectComment($commentId);
            Header("Location: ./index.php?page=commentmanager");
            exit;
        } else {
            Header("Location: ./index.php?page=panel");
            exit;
        }
    }

    function getPermissionsManagerPage() {
        if($this->role == 1) {
            $user = new \Monsapp\Myblog\Models\User();
            $allUsers = $user->getAllUsers();

            $role = new \Monsapp\Myblog\Models\Role();
            $allRoles = $role->getRoles();
            echo $this->twig->render("panel/users.html.twig", array(
                "title" => "Gestions des permissions - " . $this->title,
                "navtitle" => $this->title,
                "description" => $this->descriptions,
                "keywords" => $this->keywords,
                "role" => $this->role,
                "users" => $allUsers,
                "main_user_id" => $this->mainUser,
                "roles" => $allRoles
            ));
        } else {
            Header("Location: ./index.php");
            exit;
        }
    }

    function getSetPermissionPage(array $postArray) {
        if($this->role == 1) {
            $user = new \Monsapp\Myblog\Models\User();
            $user->setPermission((int)$postArray["user_id"], (int)$postArray["role_id"]);
            Header("Location: ./index.php?page=permissionmanager");
            exit;
        } else {
            Header("Location: ./index.php");
            exit;
        }
    }

    function getDeleteSocialPage(int $id) {
        if($this->role == 1) {
            $social = new \Monsapp\Myblog\Models\Social();
            $social->deleteSocial($id);
            Header("Location: ./index.php?page=settingsmanager");
            exit;
        } else {
            Header("Location: ./index.php");
            exit;
        }
    }

    function getUpdateSocialPage(array $files, array $postArray) {
        if($this->role == 1) {
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
            
                            $uploadFile = $uploadDir . basename($filename);
                            if (move_uploaded_file($imageTmpName, $uploadFile)) {
                                $social->updateSocialImage((int)$postArray["id"][$i], $name, $filename);
                            } else {
                                Header("Location: ./index.php?page=settingsmanager&error=2");
                                exit;
                            }
                        } else {
                            Header("Location: ./index.php?page=settingsmanager&error=1");
                            exit;
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
        
                        $uploadFile = $uploadDir . basename($filename);
                        if (move_uploaded_file($imageTmpName, $uploadFile)) {
                            $social->addSocial($name, $filename);
                        } else {
                            Header("Location: ./index.php?page=settingsmanager&error=2");
                            exit;
                        }
                    } else {
                        Header("Location: ./index.php?page=settingsmanager&error=1");
                        exit;
                    }

                }
            }

            Header("Location: ./index.php?page=settingsmanager");
            exit;
        } else {
            Header("Location: ./index.php");
            exit;
        }
    }

    function getPostManagerPage() {
        $userInfos = $this->userInfos;

        $post = new \Monsapp\Myblog\Models\Post();
        $posts = $post->getAllPosts();
        if($this->role >= 1) {
            echo $this->twig->render("panel/posts.html.twig", array(
                "title" => "Gestions des articles - " . $this->title,
                "navtitle" => $this->title,
                "description" => $this->descriptions,
                "keywords" => $this->keywords,
                "role" => $this->role,
                "user_id" => $userInfos["id"],
                "posts" => $posts
            ));
        } else {
            Header("Location: ./index.php");
            exit;
        }
    }

    function getDeletePostPage(int $id) {
        $post = new \Monsapp\Myblog\Models\Post();
        $postInfo = $post->getPostInfos($id);
        if($this->role == 1) {
            $post->deletePost((int)$id);
            Header("Location: ./index.php?page=postmanager");
            exit;
        } elseif($this->role == 2 && $this->userInfos == $postInfo["used_id"]) {
            $post->deletePost((int)$id);
            Header("Location: ./index.php?page=postmanager");
            exit;
         }else {
            Header("Location: ./index.php");
            exit;
        }
    }
}
