<?php
/**
 * User: bartmachielsen
 * Date: 5-9-2017
 * Time: 19:53
 */

    header('Content-Type: application/json');
    require_once("database_connector.php");
    require("rest_connector.php");
    include("coupon_notification.php");


    function request_access($user, $mc, $conn){
        $access_token = $mc->register($user["USERNAME"], $user["PASSWORD"]);
        $user["TOKEN"] = $access_token["access_token"];
        $conn->access_token($user);
        return $user;
    }
    $try_amount = 50;
    $hist_amount = 200;
    $max_diff = 20;

    $uploaded_offers = array();
    $uploaded_redeems = array();

    $tries = array();
    $conn = new MySQLConnector();
    $mc = new McDonaldsConnector();
    
    function redeem($id, $user, $mc, $conn){
        $redeem = $mc->redeem($user["TOKEN"], $user["UID"], intval($id), "". $id);
        if(array_key_exists("error", $redeem)){
            return $redeem["error"] . " ID: " . $id;
        }else{ 
            if($conn->upload_offer($redeem)){
                // array_push($uploaded_offers, $redeem);
                send_to_all($redeem, $conn); // todo set back
            }
            if($conn->upload_redeem($redeem, $user)){
                return json_encode($redeem);
            }
        }
        return " || ";
    }

    $registered = $conn->get_all_coupons($hist_amount);
    $last_id = $registered[0]["CODE"];


    foreach ($conn->get_users() as $user){
        if($user["VALID"]){
            // CHECK IF ACCESS_TOKEN IS PRESENT
            $access_token = $user["TOKEN"];
            if($access_token == NULL){ 
                $user = request_access($user, $mc, $conn);
                $access_token = $user["TOKEN"];
            }  

            // GET ALL THE MISSING REDEEMS
            
            $count = $last_id;
            foreach($registered as $register){
                $diff = $count - $register["CODE"];
                for($i = 1; $i < $diff && $i < $max_diff; $i++){
                    array_push($tries, "MISSING: " . redeem(intval($count) - $i, $user, $mc, $conn));
                }
                $count = $register["CODE"];
            }
 
            // TRY ALL THE FOLLOWING REDEEMS AND ADD TO DATABASE!
            for($i = 1; $i <= $try_amount; $i++){
                array_push($tries, "NEW: " . redeem(intval($last_id) + $i, $user, $mc, $conn));
                
            }
        }
    }
    $conn->close();
    echo json_encode($tries);