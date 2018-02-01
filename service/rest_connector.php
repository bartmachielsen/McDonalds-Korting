<?php
/**
 * Created by PhpStorm.
 * User: bartmachielsen
 * Date: 3-9-2017
 * Time: 11:15
 */

class McDonaldsConnector{
    var $base_headers = array('Content-Type:application/json',
        'x-vmob-device_network_type:wifi',
        'x-vmob-location_latitude:',
        'x-vmob-device_utc_offset:+2:00',
        'x-vmob-application_version:1080',
        'x-vmob-location_longitude:',
        'x-vmob-location_accuracy:',
        'x-vmob-mobile_operator:none',
        'x-vmob-device_timezone_id:Europe/Amsterdam',
        'x-vmob-device_type:a',
        'x-vmob-device:samsung GT-I9505',
        'x-vmob-sdk_version:4.26.0',
        // 'x-vmob-uid:a6Gb3zVRMcNtqbDSZ_J_pkbopt_lfNjWRryMU3oVciv7dt2YbkN8Sg==_',
        'Accept:application/json',
        'x-vmob-device_os_version:6.0.1',
        'x-vmob-authorization:c527e167-2481-43bd-805d-56eceea3ba2c',
        'x-vmob-beacons:',
        'Accept-Language:nl-NL',
        'x-vmob-device_screen_resolution:1080x1920',
        'User-Agent:Dalvik/2.1.0 (Linux; U; Android 6.0.1; GT-I9505 Build/MOB30J)',
        'Connection:Keep-Alive',);

    function request_headers($url, $addition_headers, $method="GET"){
        $curl_h = curl_init($url);
        curl_setopt($curl_h, CURLOPT_HTTPHEADER, array_merge($this->base_headers, $addition_headers));
        curl_setopt($curl_h, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl_h, CURLOPT_RETURNTRANSFER, true);
        return $curl_h;
    }
    function get($url, $headers){
        $curl_h = $this->request_headers($url, $headers);
        return json_decode(curl_exec($curl_h), true);
    }
    function put($url, $headers, $content){
        $curl_h = $this->request_headers($url, array_merge($headers, array("Content-Type"=>"application/json")), "PUT");
        curl_setopt($curl_h, CURLOPT_POST, 1);
        curl_setopt($curl_h, CURLOPT_POSTFIELDS,
            json_encode($content));
        return json_decode(curl_exec($curl_h), true);
    }
    function post($url, $headers, $content){
        $curl_h = $this->request_headers($url, array_merge($headers, array("Content-Type"=>"application/json")), "POST");
        curl_setopt($curl_h, CURLOPT_POST, 1);
        curl_setopt($curl_h, CURLOPT_POSTFIELDS,
            json_encode($content));
        return json_decode(curl_exec($curl_h), true);
    }
    function register($username, $password){
        return $this->post(
            "http://con-tcb823.vmobapps.com/v3/DeviceRegistration",
            array(),
            array(
                'username'=>$username, 
                'password'=>$password,
                'grant_type'=>'password'
                )
            );
    }

    function get_offers($access_token){
        return $this->get(
            'http://off-tcb823.vmobapps.com/v3/offers',
            array('Authorization:bearer ' . $access_token,)
        );
    }

    function get_tags($access_token){
        $curl_h = curl_init('http://con-tcb823.vmobapps.com/v3/consumers/me/tagvalues');
        return $this->get(
            'http://con-tcb823.vmobapps.com/v3/consumers/me/tagvalues',
            array('Authorization:bearer ' . $access_token)
        );
    }

    function redeemed($access_token){
        return $this->get("http://con-tcb823.vmobapps.com/v3/redeemedOffers",
                        array('Authorization:bearer ' . $access_token));
    }

    function redeem($access_token, $uid, $offer_instance, $offer_id){
        $curl_h = curl_init('http://con-tcb823.vmobapps.com/v3/redeemedOffers');
        curl_setopt($curl_h, CURLOPT_HTTPHEADER, array_merge(
            $this->base_headers, array('Authorization:bearer ' . $access_token, "Content-Type"=>"application/json",
                    "x-vmob-uid:".$uid)));
        curl_setopt($curl_h, CURLOPT_POST, 1);
        curl_setopt($curl_h, CURLOPT_POSTFIELDS,
            json_encode(array('offerInstanceUniqueId'=>$offer_instance, 'offerId'=>$offer_id)));
        curl_setopt($curl_h, CURLOPT_RETURNTRANSFER, true);
        $response = json_decode(curl_exec($curl_h), true);
        return $response;
    }

    function get_image($width, $height, $path){
        $curl_h = curl_init('http://az798212.vo.msecnd.net/xy/'.$width.'/'.$height.'/?path='.$path);
        curl_setopt($curl_h, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($curl_h);
        return $result;
    }


    function create_user($access_token, $dateOfBirth, $email, $password,$firstname, $lastname, $gender, $postcode){
        return $this->post('http://con-tcb823.vmobapps.com/v3/emailRegistrations',
                        array('Authorization:bearer ' . $access_token),
                        array(
                            "emailRegistration"=>array(
                                "dateOfBirth"=>$dateOfBirth,
                                "emailAddress"=>$email,
                                "extendedData"=>"zipcode\u003d".$postcode."\u0026advertisementid\u003df21cfe42-5122-4626-b22c-d655d09ef867",
                                "firstname"=>$firstname,
                                "gender"=>$gender,
                                "lastName"=>$lastname,
                                "password"=>$password
                            ),
                            "grant_type"=>"password",
                            "password"=>$password,
                            "username"=>$email
                        ));
    }

    function put_tags($access_token, $remove_tags, $add_tags){
        return $this->put('http://con-tcb823.vmobapps.com/v3/consumers/me/tagvalues', array('Authorization:bearer ' . $access_token),
                        array("tagValueRemoveReferenceCodes"=>$remove_tags,
                        "tagValueAddReferenceCodes"=>$add_tags));
    }

//    function add_cross_reference($access_token, $reference_token){
//
//    }

    function get_offer_details($access_token, $gift_id, $offer_id){

        $curl_h = curl_init('http://con-tcb823.vmobapps.com/v3/consumers/me/gifts/'.$offer_id.'?giftId='.$gift_id);
        curl_setopt($curl_h, CURLOPT_HTTPHEADER, array_merge(
            $this->base_headers, array('Authorization:bearer ' . $access_token)));
        curl_setopt($curl_h, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($curl_h);
        $response = json_decode($result, true);
        return $response;
    }

    function image_url($width, $height, $path){
        return 'http://az798212.vo.msecnd.net/xy/'.$width.'/'.$height.'/?path='.$path;
    }

}