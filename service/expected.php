<?php
    header("Access-Control-Allow-Origin: *");
    header('Content-Type: application/json');
    require_once("database_connector.php");



    function sort_existing($db, $coupons){
        $sorted = array();
        foreach($coupons as $coupon){
            $id = $coupon["CODE"];
            if(!array_key_exists($id, $sorted)){
                $offer_details = $db->get_offer_redeemed($coupon);
                $sorted[$id] = array();
                $sorted[$id]["REDEEMED"] = array();
                $sorted[$id]["DETAILS"] = $offer_details;
            }
            array_push($sorted[$id]["REDEEMED"], $coupon);
        }
        return $sorted;
    }

    
    $db = new MySQLConnector();
    if(array_key_exists("code", $_GET)){
        echo json_encode($db->get_redeemed($_GET["code"]));
        // echo json_encode(sort_existing($db, $db->get_redeemed($_GET["code"])));
        exit();
    }
    echo json_encode(sort_existing($db, $db->get_all_redeemed()));
