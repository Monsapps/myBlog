<?php
/**
 * Contact Controller, generate page and send email
 */
declare(strict_types=1);

namespace Monsapp\Myblog\Controllers;

class ContactController extends Controller {

    function getContactPage(array $postArray) {
        
        if(!empty($postArray["name"]) && !empty($postArray["surname"]) && !empty($postArray["email"]) && !empty($postArray["message"])) {
            $contact = new \Monsapp\Myblog\Controllers\ContactController();
            $user = new \Monsapp\Myblog\Models\User();

            $mainUser = $user->getUserInfos((int)$this->siteInfo["site_main_user_id"]);

            if(!$contact->sendMail($mainUser["email"], $postArray["message"], $postArray["email"], $postArray["name"], $postArray["surname"])) {

                $contact->sendMessage($postArray["message"], $postArray["email"], $postArray["name"], $postArray["surname"]);
                $this->redirectTo("./index.php?status=1");
                return;
            }
        }
        $this->redirectTo("./index.php?error=1");
    }

    function sendMail(string $toAdmin, string $message, string $from, string $senderName, string $senderSurname): Bool {

        $subject = $senderName . " " . $senderSurname . "vous à contacter";
        $headers = array(
            "From" => $from,
            "Reply-To"=> $from,
            "X-Mailer" => "PHP/" . phpversion()
        );

        if(!mail($toAdmin, $subject, $message, $headers)) {
            return false;
        }
    }

    function sendMessage(string $message, string $email, string $senderName, string $senderSurname) {
        $contact = new \Monsapp\Myblog\Models\Contact();
        $contact->addContactMessage($message, $email, $senderName, $senderSurname);
    }

    /**
     * Panel Controller for Contact in DB
     */

    function getContactManagerPage() {
        $contact = new \Monsapp\Myblog\Models\Contact();
        $messages = $contact->getAllContactMessage();
        if($this->role >= 1) {
            $this->twig->display("panel/contact.html.twig", array(
                "title" => "Gestion des contacts - " . $this->siteInfo["site_title"], 
                "navtitle" => $this->siteInfo["site_title"], 
                "descriptions" => $this->siteInfo["site_descriptions"],
                "keywords" => $this->siteInfo["site_keywords"],
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
}
 