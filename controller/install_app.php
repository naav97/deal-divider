<?php
//validación de código
if (!isset($_GET['code'])) {
    echo 'Código no encontrado.';
}
//código desde Hubspot
$code = $_GET['code'];
//app ID - 1412834
//datos de configuración de la aplicación
$app_client_id = "0c7293a4-9fcc-45c4-897d-8c040a16ed28"; //client_id
$app_client_secret = "8789b03f-1bd2-4921-8e31-ebb11fa79534"; //client_secret
$app_redirect = "https://colaborador.grows.pro/install_app.php";//URL de redireccionamiento del App de Hubspot

first_token();

function first_token()
{
    global $code, $app_client_id, $app_client_secret,$app_redirect;

    $hurl = "https://api.hubapi.com/oauth/v1/token";

    $ch = curl_init();
    
    $fields = array(
        "grant_type=authorization_code",
        "client_id=$app_client_id",
        "client_secret=$app_client_secret",
        "redirect_uri=$app_redirect",
        "code=$code"
    );
    $payload = implode("&", $fields);

    curl_setopt_array($ch, array(
        CURLOPT_URL => $hurl,
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
            'Content-Type: application/x-www-form-urlencoded'
        ),
        CURLOPT_POSTFIELDS => $payload
    ));

    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err = curl_error($ch);

    curl_close($ch);
    if ($err) {
        return array(
            "success" => false,           
            "msg" => "Erro de conexión curl",
            "error" => $err,
            "data" => $response,
        );
    } else {
        if ($httpcode == 200) {
            echo 'Instalación completa.<br>';
            echo $response;
        } else {
            echo 'Ocurrió un error durante la instalación.<br>';
            echo $response;
        }
    }
}
