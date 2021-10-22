<?php

/**
 * Controller to send mail
 */

 function sendMail(string $to, string $message, string $from, string $senderName, string $senderSurname) {

    $subject = $senderName . " " . $senderSurname . "vous à contacter";
    $headers = array(
        "From" => $from,
        "Reply-To"=> $from,
        "X-Mailer" => "PHP/" . phpversion()
    );

    mail($to, $subject, $message, $headers);
 }
 