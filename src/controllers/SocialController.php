<?php
/**
 * Social controller, CRUD social for users & admin
 */
declare(strict_types=1);

namespace Monsapp\Myblog\Controllers;

class SocialController extends Controller {

    function getAddUserSocialsPage(array $postArray) {
        // only confirmed user and user himself can add socials
        if((isset($postArray["token"]) && $postArray["token"] == $this->superGlobal->getSessionValue("token")) && ($this->role != -1 && $this->userInfos["id"] == $postArray["user_id"])) {
            $user = new \Monsapp\Myblog\Models\User();
            //todo get userid
            for($i = 0; $i < count($postArray["social_id"]); $i++) {
                $user->addSocial((int)$postArray["user_id"], (int)$postArray["social_id"][$i], $postArray["meta"][$i]);
            }
            $this->redirectTo("./index.php?page=panel");
        }

        $this->redirectTo("./index.php");
    }

    function getUpdateUserSocialsPage(array $postArray) {
        // only confirmed user and user himself can update socials
        if((isset($postArray["token"]) && $postArray["token"] == $this->superGlobal->getSessionValue("token")) && ($this->role != -1 && $this->userInfos["id"] == $postArray["user_id"])) {

            $user = new \Monsapp\Myblog\Models\User();

            for($i = 0; $i < count($postArray["social_id"]); $i++) {
                $user->updateSocial((int)$postArray["social_id"][$i], $postArray["meta"][$i]);
            }
            $this->redirectTo("./index.php?page=panel");
        }
        
        $this->redirectTo("./index.php");
    }

    function getDeleteUserSocialPage(int $userId, int $socialId) {
        // only confirmed user and user himself can delete socials
        if((!empty($this->superGlobal->getGetValue("token")) && $this->superGlobal->getGetValue("token") == $this->superGlobal->getSessionValue("token")) && ($this->role != -1 && $this->userInfos["id"] == $userId)) {
            $user = new \Monsapp\Myblog\Models\User();
            $user->deleteSocial($socialId);
            $this->redirectTo("./index.php?page=panel");
        }
        $this->redirectTo("./index.php");
    }

    /**
     * This is admin section Crud Social element
     */

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
        }
        $this->redirectTo("./index.php");
    }

    function getDeleteSocialPage(int $idSocial) {
        if((!empty($this->superGlobal->getGetValue("token")) && $this->superGlobal->getGetValue("token") == $this->superGlobal->getSessionValue("token")) && $this->role == 1) {
            $social = new \Monsapp\Myblog\Models\Social();
            $social->deleteSocial($idSocial);
            $this->redirectTo("./index.php?page=settingsmanager");
        }
        $this->redirectTo("./index.php");
    }
}
