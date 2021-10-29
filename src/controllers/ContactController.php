<?php
/**
 * Controller to send mail
 */
declare(strict_types=1);

namespace Monsapp\Myblog\Controllers;

class ContactController {

    function sendMail(string $to, string $message, string $from, string $senderName, string $senderSurname): Bool {

        $subject = $senderName . " " . $senderSurname . "vous à contacter";
        $headers = array(
            "From" => $from,
            "Reply-To"=> $from,
            "X-Mailer" => "PHP/" . phpversion()
        );

        if(mail($to, $subject, $message, $headers)) {
            return true;
        } else {
            return false;
        }
    }

    function sendMessage(string $message, string $email, string $senderName, string $senderSurname) {
        $contact = new \Monsapp\Myblog\Models\Contact();
        $contact->addContactMessage($message, $email, $senderName, $senderSurname);
    }
}
 