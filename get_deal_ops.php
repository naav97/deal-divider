<?php
include_once 'controller/HubspotController.php';

$env = parse_ini_file('.env');

$hubspot_obj = new HubspotController($env["ACCESS_TOKEN"]);

$noResults = array(
    'results' => array(
        array(
            "objectId" => 1,
            "title" => "Error al adquirir la información del negocio, pro favor recargue la página. Si el problema persiste por favor contacte al equipo Grows.",
            "properties" => array()
        )
    )
);

function created_date_format($date) {
    $date_obj = date_create($date);
    date_sub($date_obj, date_interval_create_from_date_string('5 hours'));
    $date_format = date_format($date_obj, 'd/m/Y H:i:s');
    return $date_format;
}

function format_results($item_2) {
    return array(
        "objectId" => $item_2['id'],
        "title" => $item_2['properties']['dealname'],
        "link" => "https://app.hubspot.com/contacts/21549431/record/0-3/".$item_2['properties']['hs_object_id'],
        "created" => created_date_format($item_2['properties']['createdate']),
        "priority" => "LOW",
        "properties" => array(
            array(
                "label" => "Valor",
                "dataType" => "STRING",
                "value" => $item_2['properties']['amount']
            ),
            array(
                "label" => "Fecha de pago",
                "dataType" => "STRING",
                "value" => $item_2['properties']['closedate']
            )         
        ),
    );
}

//obtener lista de documentos
function create_list($deal_id)
{
    global $hubspot_obj;   
    global $noResults;

    /*Listar documentos*/
    $test_ids = [];
    $respond = array();
    $respond['results'] = array();
    $dividido = null;
    //con el deal_id buscamos los test drives asociados al negocio
    $url = "https://api.hubapi.com/crm/v3/objects/deals/".$deal_id."?properties=dividido";

    $resp = $hubspot_obj->api_v3($url, "GET");

    if ($resp['success'] && $resp['status'] == 200) {
        $data = json_decode($resp['data'], true);
        $props = $data['properties'];
        if ($props['dividido'] != "true") {
            $dividido = false;
        }
        else {
            $dividido = true;

            $url = "https://api.hubapi.com/crm/v4/objects/deals/".$deal_id."/associations/deals";

            $resp = $hubspot_obj->api_v3($url, "GET");

            $results = json_decode($resp['data'], true)['results'];

            foreach ($results as $key => $item) {                
                array_push(
                    $test_ids,
                    array(
                        "id" => $item['toObjectId'] . ""
                    )
                );
            }

            $url = "https://api.hubapi.com/crm/v3/objects/deals/batch/read?archived=false";
            $body_arr_2 = [
                "properties" => [
                    "dealname",
                    "amount",
                    "closedate",
                    "hs_object_id"
                ],
                "inputs" => $test_ids
            ];

            $resp_2 = $hubspot_obj->api_v3($url, "POST", $body_arr_2);

            if ($resp_2['success'] && $resp_2['status'] == 200) {
                $data_2 = json_decode($resp_2['data'], true);
                $results_2 = $data_2['results'];
                foreach ($results_2 as $key_2 => $item_2) {
                    $respond['results'][] = format_results($item_2); 
                }
            } else {
                $respond = $noResults; 
            }
        }
    } else {
        $respond = $noResults; 
    }
    if (!$dividido) {
        $respond['primaryAction'] = array(
            "type" => "IFRAME",
            "width" => 768,
            "height" => 748,
            "uri" => "https://colaborador.grows.pro/deal-divider/get_form_cuotas.php?&deal_id=" . $deal_id,
            "label" => "Dividir negocio"
        );
    }
    return $respond;
    
}

header('Content-Type: application/json;charset=utf-8');
$deal_id = $_GET['associatedObjectId'];
//$deal_id = '16385417769';
$results = create_list($deal_id);
echo json_encode($results);
