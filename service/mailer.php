<?php

function send_mail($mail, $subject, $message)
{
    $headers = "From: info@mcdonalds-korting.nl" . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
    return mail($mail, $subject, $message, $headers);
}
