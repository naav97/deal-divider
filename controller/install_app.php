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

first_token();
//createPropGroup($hs_controller);

function saveDetailsToDB($pipeRes, $token, $portalId, $refTok) {

    global $env;

    $data = json_decode($pipeRes, true);
    $pipeid = "PIPE_ID=".$data['id'];
    $stageid = "STAGE_ID=";
    foreach($data['stages'] as &$st) {
        if($st['label'] == "Factura emitida") {
            $stageid = $stageid.$st['id'];
            break;
        }
    }
    $server = "138.197.104.102";
    $user = "usuario_db";
    $pass = $env['DB_PASS'];
    $db = "clientes";
    $conn = new mysqli($server, $user, $pass, $db);
    if($conn->connect_error) {
        die("ERROR al conectarse a la base de datos: ".$conn->connect_error);
    }
    $sql = "INSERT INTO det_client (PortalID, OAToken, RefToken, PipelineID, StageID) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $portalId, $token, $refTok, $pipeid, $stageid);
    if($stmt->execute()) {
        echo "<br>Instalacion completada correctamente";
    }
    else {
        echo "Error instalado la aplicacion: ".$conn->error;
    }
    $stmt->close();
    $conn->close();
}

function createPipe($hs_c, $token, $portalId, $refTok) {
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
    saveDetailsToDB($res['data'], $token, $portalId, $refTok);
}

function createProps($hs_c, $token, $portalId, $refTok) {
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
    createPipe($hs_c, $token, $portalId, $refTok);
}

function createPropGroup($hs_c, $token, $portalId, $refTok) {
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
    createProps($hs_c, $token, $portalId, $refTok);
}

function obtener_portal_id ($tok_res) {
    $url = "https://api.hubapi.com/integrations/v1/me";
    $data_token = json_decode($tok_res, true);
    $hs_c = new HubspotController("Bearer ".$data_token['access_token']);
    $res = $hs_c->api_v1($url, $method = "GET");
    if ($res['success'] && $res['status'] == 200) {
        $data_cliente = json_decode($res['data'], true);
        print_r($data_cliente['portalId']);
        createPropGroup($hs_c, $data_token['access_token'], $data_cliente['portalId'], $data_token['refresh_token']);
    }
    else {
        print_r($res);
        echo "<br> Ocurrio un error al obtener la información del cliente";
        die();
    }
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
            obtener_portal_id($response);
        } else {
            echo 'Ocurrió un error durante la instalación.<br>';
            echo $response;
        }
    }
}
