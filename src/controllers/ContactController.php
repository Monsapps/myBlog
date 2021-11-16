<?php
/**
 * Contact Controller, generate page and send email
 */
declare(strict_types=1);

namespace Monsapp\Myblog\Controllers;

class ContactController extends Controller {

    /**
     * Try sending mail with smtp if not add info to database
     * @param array $postArray
     *  Array from contact form
     * @return void
     */

    function getContactPage(array $postArray) {
        
        if(!empty($postArray["name"]) && !empty($postArray["surname"]) && !empty($postArray["email"]) && !empty($postArray["message"])) {
            $contact = new \Monsapp\Myblog\Controllers\ContactController();
            $user = new \Monsapp\Myblog\Models\User();

            $mainUser = $user->getUserInfos((int)$this->siteInfo["site_main_user_id"]);

            $message = utf8_decode($postArray["message"]);
            $name = utf8_decode($postArray["name"]);
            $surname = utf8_decode($postArray["surname"]);

            if(!$contact->sendMail($mainUser["email"], $message, $postArray["email"], $name, $surname)) {

                $contact->sendMessage($postArray["message"], $postArray["email"], $postArray["name"], $postArray["surname"]);
            }

            $this->redirectTo("./index.php?status=1");
        }
        $this->redirectTo("./index.php?error=1");
    }

    /**
     * Send mail to the main user
     * @param string $toAdmin
     *  Admin mail
     * @param string $message
     *  Content of message
     * @param string $from
     *  Sender mail
     * @param string $senderName
     *  Sender name
     * @param string $senderSurname
     *  Sender surname
     * @return bool
     */

    function sendMail(string $toAdmin, string $message, string $from, string $senderName, string $senderSurname): Bool {

        $subject = $senderName . " " . $senderSurname . utf8_decode(" vous à contacter");
        $headers = array(
            "MIME-Version" => "1.0",
            "Content-type" => "text/html; charset=utf-8",
            "From" => $from,
            "Reply-To"=> $from,
            "X-Mailer" => "PHP/" . phpversion()
        );

        return mail($toAdmin, $subject, $message, $headers);
    }

    /**
     * Add contact message to the database
     * @param string $message
     *  Content of message
     * @param string $from
     *  Sender mail
     * @param string $senderName
     *  Sender name
     * @param string $senderSurname
     *  Sender surname
     * @return void
     */

    function sendMessage(string $message, string $email, string $senderName, string $senderSurname) {
        $contact = new \Monsapp\Myblog\Models\Contact();
        $contact->addContactMessage($message, $email, $senderName, $senderSurname);
    }

    /**
     * Get contact messages in admin section
     * @return void
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
            return;
        }
        $this->redirectTo("./index.php");
    }

    /**
     * Update message status for contact message
     * @param int $idMessage
     *  Message id
     * @return void
     */

    function getReadMessagePage(int $idMessage) {
        if((!empty($this->superGlobal->getGetValue("token")) && $this->superGlobal->getGetValue("token") == $this->superGlobal->getSessionValue("token")) && ($this->role == 1)) {
            $contact = new \Monsapp\Myblog\Models\Contact();
            $contact->updateStatus((int) $idMessage);
            $this->redirectTo("./index.php?page=contactmanager");
        }
        $this->redirectTo("./index.php");
    }
}
 