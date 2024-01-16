<?php
include_once 'controller/HubspotController.php';
include_once 'controller/DBController.php';

$env = parse_ini_file('.env');

$db_cont = new DBController($env['DB_PASS'], $env['CLIENT_ID'], $env['CLIENT_SECRET']);

$filterData = array_filter($_POST, function ($val) {
	return !empty($val);
});

$portal_id = $filterData['portal_id'];
$contactos = [];
$line_items = [];

$hs_controller = new HubspotController("Bearer ".$db_cont->getProperty($portal_id, "OAToken"));

function firstTry($filterData, $hs_controller) {
    global $contactos, $line_items;
    $url = 'https://api.hubapi.com/crm/v3/objects/deals/'.$filterData['deal_id'].'?associations=contact%2Cline_items';

    $res = $hs_controller->api_v3($url, $method = "GET");

    print_r($res);

    if ($res['success'] && $res['status'] == 200) {
        $contactos = json_decode($res['data'], true)['associations']['contacts']['results'];
        $line_items = json_decode($res['data'], true)['associations']['line items']['results'];
        return "OK";
    }
    elseif ($res['success'] && $res['status'] == 401) {
        return "OT";
    }
}

function mainp($filterData, $dbc, $portal_id, $hs_c) {
    $i = 0;
    $ret = "";
    while($i < 3) {
        $ret = firstTry($filterData, $hs_c);
        if($ret == "OT") {
            $dbc->updateToken($portal_id);
            $i = $i + 1;
        }
        else {
            return $ret;
        }
    }
    echo "No se pudo completar la operacion";
    print_r($ret);
    die();
}

mainp($filterData, $db_cont, $portal_id, $hs_controller);

$line_items_ids = [];

foreach ($line_items as &$li) {
    array_push($line_items_ids, array("id" => $li['id']));
}

$line_items_details = [];

$url_li_d = 'https://api.hubapi.com/crm/v3/objects/line_items/batch/read?archived=false';

$body_li_d = array (
    "inputs" => $line_items_ids,
    "properties" => [
        "name",
        "quantity",
        "price"
    ]
);

$res_li_d = $hs_controller->api_v3($url_li_d, $method = "POST", $data = $body_li_d);

if ($res_li_d['success'] && $res_li_d['status'] == 200) {
    $line_items_details = json_decode($res_li_d['data'], true)['results'];
}
else {
    print_r($res_li_d);
    echo "<br>Ocurrio un error creando los articulos de linea.";
}

function createLineItems($li_d, $hs_c) {
    $new_li = [];
    $url = 'https://api.hubapi.com/crm/v3/objects/line_items/batch/create';
    $props = [];
    foreach($li_d as &$li) {
        array_push($props, array("properties" => $li['properties']));
    }
    $body = array("inputs" => $props);
    $resp = $hs_c->api_v3($url, $method = "POST", $data = $body);
    if($resp['success'] && $resp['status'] == 201) {
        $new_li = json_decode($resp['data'], true)['results'];
    }
    return $new_li;
}

function createAsos($conts, $liit, $dealId) {
    $jsonres = array();

    $padre = array (
        'to' => [
            'id' => $dealId,
        ],
        'types' => [
            [
                'associationCategory' => 'HUBSPOT_DEFINED',
                'associationTypeId' => 451,
            ],
        ],
    );

    array_push($jsonres, $padre);

    foreach ($conts as &$cont) {
        $struct = array (
            "to" => [
                "id" => $cont['id'],
            ],
            "types" => [
                [

                    "associationCategory" => "HUBSPOT_DEFINED",
                    "associationTypeId" => 3,
                ],
            ],
        );

        array_push($jsonres, $struct);
    }

    foreach ($liit as &$li) {
        $struct = array (
            "to" => [
                "id" => $li['id'],
            ],
            "types" => [
                [

                    "associationCategory" => "HUBSPOT_DEFINED",
                    "associationTypeId" => 19,
                ],
            ],
        );

        array_push($jsonres, $struct);
    }

    return $jsonres;
}

$url_cre_deal = 'https://api.hubapi.com/crm/v3/objects/deals';
for ($i = 1; $i <= $filterData['deal_num_cuo']; $i++) {
    $new_li = createLineItems($line_items_details, $hs_controller);
    $deal = array (
        "properties" => [
            "amount" => $filterData['valor_cuota_'.$i],
            "dealname" => $filterData['deal_name']." cuota # ".$i,
            "closedate" => $filterData['fecha_pago_cuota_'.$i],
            "pipeline" => "74755618",
            "dealstage" => "143884940",
        ],
        "associations" => createAsos($contactos, $new_li, $filterData['deal_id'])
    );
    $res_cre_deal = $hs_controller->api_v3($url_cre_deal, $method = "POST", $data = $deal);
    if ($res_cre_deal['success'] && $res_cre_deal['status'] == 201) {

    }else{
        print_r($res_cre_deal);
        echo '<br>Ocurrió un error al crear el negocio';
        die();
    }
}

$body = array("properties" => array("dividido" => "true"));
$url = "https://api.hubapi.com/crm/v3/objects/deals/".$filterData['deal_id'];
$res = $hs_controller->api_v3($url, $method = "PATCH", $data = $body);

if ($res['success'] && $res['status'] == 200) {
    echo '<h2 style="font-family: Helvetica; text-align: center;margin-top: 25px;">Cuotas creadas con exito!</h2>';
}else{
    print_r($res);
    echo '<br>Ocurrió un error al actualizar el negocio';
    die();
}
