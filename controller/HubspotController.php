<?php
include_once 'HelperController.php';
class HubspotController {

    public $api_token;

    public function __construct($pApiToken = "no-auth") {
        $this->api_token = $pApiToken;
    }

    function api_v1($url, $method = "GET", $data = array(), $vid_offset = "", $time_offset = "")
    {
        global $api_token;
        $helper_obj = new HelperController($api_token);

        if ($vid_offset != "") {
            $url = $url . "&timeOffset=$time_offset&vidOffset=$vid_offset";
        }        

        $ch = curl_init();

        $body_arr = array(
            CURLOPT_URL => $url,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => array(
                "accept: application/json",
                'Content-Type:application/json',
                'Authorization: '.$this->api_token
            )
        );
        if ($method == "POST" || $method == "PATCH") {
            $payload = json_encode($data);

            $body_arr = array(
                CURLOPT_URL => $url,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => $method,
                CURLOPT_HTTPHEADER => array(
                    "accept: application/json",
                    'Content-Type:application/json',
                    'Authorization: '.$this->api_token
                ),
                CURLOPT_POSTFIELDS => $payload
            );
        }
        curl_setopt_array($ch, $body_arr);

        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);

        curl_close($ch);

        if ($err) {
            return array(
                "success" => false,
                "vid" => 0,
                "msg" => "Erro de conexión curl",
                "error" => $err,
                "data" => $response,
            );
        } else {
            return array(
                "success" => true,
                "data" => $response,
                "msg" => "Exito",
                "status" => $httpcode
            );
        }
    }
    function getNewToken($url, $data = "")
    {

        $ch = curl_init();

        $body_arr = array(
            CURLOPT_URL => $url,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => array(
                "accept: application/json",
                'Content-Type:application/x-www-form-urlencoded'
            ),
            CURLOPT_POSTFIELDS => $data
        );
        curl_setopt_array($ch, $body_arr);

        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);

        curl_close($ch);

        if ($err) {
            return array(
                "success" => false,
                "vid" => 0,
                "msg" => "Erro de conexión curl",
                "error" => $err,
                "data" => $response,
            );
        } else {
            return array(
                "success" => true,
                "data" => $response,
                "msg" => "Exito",
                "status" => $httpcode
            );
        }
    }
    function api_v3($url, $method = "GET", $data = array(), $after = "", $content_type = "application/json")
    {
        global $api_token;
        $helper_obj = new HelperController($api_token);

        if ($after != "") {
            $url = $url . "&after=$after";
        }

        $ch = curl_init();

        $body_arr = array(
            CURLOPT_URL => $url,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => array(
                "accept: application/json",
                'Content-Type:application/json',
                'Authorization: '.$this->api_token
            )
        );
        if ($method == "POST" || $method == "PATCH") {
            if($content_type != "multipart/form-data") {
                $payload = json_encode($data);
            }
            else {
                $payload = $data;
            }
            $body_arr = array(
                CURLOPT_URL => $url,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => $method,
                CURLOPT_HTTPHEADER => array(
                    "accept: application/json",
                    'Content-Type:'.$content_type,
                    'Authorization: '.$this->api_token
                ),
                CURLOPT_POSTFIELDS => $payload
            );
        }
        
        curl_setopt_array($ch, $body_arr);

        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);

        curl_close($ch);

        if ($err) {
            return array(
                "success" => false,
                "vid" => 0,
                "msg" => "Erro de conexión curl",
                "error" => $err,
                "data" => $response,
            );
        } else {
            return array(
                "success" => true,
                "data" => $response,
                "msg" => "Exito",
                "status" => $httpcode
            );
        }
    }
}
