<?php
/**
 * This is the main controller
 */
declare(strict_types=1);

namespace Monsapp\Myblog\Controllers;

class Controller {

    protected $config;
    protected $siteInfo;
    protected $twig;
    protected $role;
    protected $isAllowedToCRUD;
    protected $userInfos;
    protected $superGlobal;

    function __construct() {

        $this->config = new \Monsapp\Myblog\Utils\ConfigManager();

        $this->superGlobal = new \Monsapp\Myblog\Utils\SuperGlobal();

        $this->siteInfo = $this->config->getConfig();

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
        $mainUser = $user->getUserInfos((int)$this->siteInfo["site_main_user_id"]);
        $userSocials = $user->getUserSocials((int)$this->siteInfo["site_main_user_id"]);
        $this->twig->display("index.html.twig", array(
                "title" => $this->siteInfo["site_title"], 
                "navtitle" => $this->siteInfo["site_title"], 
                "descriptions" => $this->siteInfo["site_descriptions"],
                "keywords" => $this->siteInfo["site_keywords"],
                "role" => $this->role,
                "user" => $this->userInfos,
                "main_user" => $mainUser,
                "userSocials" => $userSocials
            ));
    }

    /**
     * Controllers for login/logout/register
     */

    function getConnectPage() {

        $this->twig->display("connect.html.twig", array(
            "title" => "Connexion - " . $this->siteInfo["site_title"], 
            "navtitle" => $this->siteInfo["site_title"], 
            "descriptions" => $this->siteInfo["site_descriptions"],
            "keywords" => $this->siteInfo["site_keywords"],
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
                    "title" => "Panneau de configuration - " . $this->siteInfo["site_title"], 
                    "navtitle" => $this->siteInfo["site_title"], 
                    "descriptions" => $this->siteInfo["site_descriptions"],
                    "keywords" => $this->siteInfo["site_keywords"],
                    "role" => $this->role,
                    "user" => $this->userInfos,
                    "socials" => $socials,
                    "userSocials" => $userSocials,
                    "token" => $this->superGlobal->getSessionValue("token")
                ));
                return;
            }
            $this->redirectTo("./index.php");

        }
        $this->redirectTo("./index.php");
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
                $this->redirectTo("./index.php?page=panel&error=1");// todo
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
                "title" => "Préférences générales - " . $this->siteInfo["site_title"], 
                "navtitle" => $this->siteInfo["site_title"], 
                "descriptions" => $this->siteInfo["site_descriptions"],
                "keywords" => $this->siteInfo["site_keywords"],
                "role" => $this->role,
                "users" => $allUsers,
                "user" => $this->userInfos,
                "main_user_id" => $this->siteInfo["site_main_user_id"],
                "socials" => $socials,
                "token" => $this->superGlobal->getSessionValue("token")
            ));
            return;
        }
        $this->redirectTo("./index.php");
    }

    function getMainSettingsPage(array $post) {
        // only admin can edit main settings
        if((isset($post["token"]) && $post["token"] == $this->superGlobal->getSessionValue("token")) && $this->role == 1) {
            if(!empty($post["site_title"]) && $this->siteInfo["site_title"] != $post["site_title"]) {
                $this->config->editConfig("site_title", $post["site_title"]);
            }

            if(!empty($post["site_description"]) && $this->siteInfo["site_descriptions"] != $post["site_description"]) {
                $this->config->editConfig("site_descriptions", $post["site_description"]);
            }

            if(!empty($post["site_keywords"]) && $this->siteInfo["site_keywords"] != $post["site_keywords"]) {
                $this->config->editConfig("site_keywords", $post["site_keywords"]);
            }

            if(!empty($post["main_user"]) && $this->siteInfo["site_main_user_id"] != $post["main_user"]) {
                $this->config->editConfig("site_main_user_id", $post["main_user"]);
            }

            $this->redirectTo("./index.php?page=settingsmanager");
        } else {
            $this->redirectTo("./index.php");
        }
    }

    function getPermissionsManagerPage() {
        if($this->role == 1) {
            $user = new \Monsapp\Myblog\Models\User();
            $allUsers = $user->getAllUsers();

            $role = new \Monsapp\Myblog\Models\Role();
            $allRoles = $role->getRoles();
            $this->twig->display("panel/users.html.twig", array(
                "title" => "Gestion des permissions - " . $this->siteInfo["site_title"], 
                "navtitle" => $this->siteInfo["site_title"], 
                "descriptions" => $this->siteInfo["site_descriptions"],
                "keywords" => $this->siteInfo["site_keywords"],
                "role" => $this->role,
                "user" => $this->userInfos,
                "users" => $allUsers,
                "main_user_id" => (int)$this->siteInfo["site_main_user_id"],
                "roles" => $allRoles,
                "token" => $this->superGlobal->getSessionValue("token")
            ));
            return;
        }

        $this->redirectTo("./index.php");
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
        exit;
    }

    private function moveUploadedFile(string $filename, string $destination) {
        return move_uploaded_file($filename, $destination);
    }

    private function baseFilename(string $path) {
        return basename($path);
    }
}
