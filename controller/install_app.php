<?php
include_once 'HubspotController.php';

$env = parse_ini_file('../.env');

$hs_controller = new HubspotController($env["ACCESS_TOKEN"]);

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
$app_redirect = "https://colaborador.grows.pro/deal-divider/controller/install_app.php";//URL de redireccionamiento del App de Hubspot

$resp_token = first_token();
createPropGroup($hs_controller);

function createPipe($hs_c) {
    $url = 'https://api.hubapi.com/crm/v3/pipelines/deals';
    $body = array (
        "displayOrder" => 0,
        "label" => "Grows - Embudo de Facturación",
        "stages" => [
            array (
                "metadata" => array (
                    "probability" => "1.0"
                ),
                "displayOrder" => 1,
                "label" => "Factura por emitir"
            ),
            array (
                "metadata" => array (
                    "probability" => "1.0"
                ),
                "displayOrder" => 2,
                "label" => "Factura emitida"
            ),
            array (
                "metadata" => array (
                    "probability" => "1.0"
                ),
                "displayOrder" => 3,
                "label" => "Factura pendiente de pago"
            ),
            array (
                "metadata" => array (
                    "probability" => "1.0"
                ),
                "displayOrder" => 4,
                "label" => "Factura atrasada"
            ),
            array (
                "metadata" => array (
                    "probability" => "1.0"
                ),
                "displayOrder" => 5,
                "label" => "Factura Pagada"
            ),
            array (
                "metadata" => array (
                    "probability" => "0.0"
                ),
                "displayOrder" => 6,
                "label" => "Factura Anulada"
            ),
        ]
    );
    $res = $hs_c->api_v3($url, $method = "POST", $data = $body);
    if (!$res['success'] || $res['status'] != 201) {
        print_r($res);
        echo "<br> Ocurrio un error al crear el pipeline";
        die();
    }
}

function createProps($hs_c) {
    $url = 'https://api.hubapi.com/crm/v3/properties/deals/batch/create';
    $body = array (
        "inputs" => [
            array (
                "label" => "Cuotas",
                "type" => "number",
                "fieldType" => "number",
                "groupName" => "info_fact",
                "name" => "cuotas"
            ),
            array (
                "label" => "Dividido",
                "type" => "enumeration",
                "fieldType" => "select",
                "groupName" => "info_fact",
                "name" => "dividido",
                "options" => [
                    array (
                        "label" => "Si",
                        "value" => "true"
                    ),
                    array (
                        "label" => "No",
                        "value" => "false"
                    )
                ]
            )
        ]
    );
    $res = $hs_c->api_v3($url, $method = "POST", $data = $body);
    if (!$res['success'] || $res['status'] != 201) {
        print_r($res);
        echo "<br> Ocurrio un error al crear las propiedades";
        die();
    }
    createPipe($hs_c);
}

function createPropGroup($hs_c) {
    $url = 'https://api.hubapi.com/crm/v3/properties/deals/groups';
    $body = array (
        "name" => "info_fact",
        "label" => "Grows - Información de Facturación",
        "displayOrder" => -1
    );
    $res = $hs_c->api_v3($url, $method = "POST", $data = $body);
    if (!$res['success'] || $res['status'] != 201) {
        print_r($res);
        echo "<br> Ocurrio un error el crear el grupo de propiedades";
        die();
    }
    createProps($hs_c);
}

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
            return $response;
        } else {
            echo 'Ocurrió un error durante la instalación.<br>';
            echo $response;
        }
    }
}
