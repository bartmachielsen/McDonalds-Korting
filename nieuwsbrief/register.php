<?php
    header("Access-Control-Allow-Origin: *");
    require(dirname(dirname(__FILE__)) ."/service/database_connector.php");
    $database = new MySQLConnector();
    if(array_key_exists("action", $_POST) && strcmp($_POST["action"],"unsubscribe") === 0){
        $database->remove_newsletter($_POST["mail"]);
        echo "Removed email from newsletter list!";
    }else{
        $database->add_newsletter($_POST["mail"]);
        echo "Added email to newsletter list!";
    }
