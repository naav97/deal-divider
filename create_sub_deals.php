<?php
include_once 'controller/HubspotController.php';

$env = parse_ini_file('.env');

$hs_controller = new HubspotController($env["ACCESS_TOKEN_G"]);



$filterData = array_filter($_POST, function ($val) {
	return !empty($val);
});
//$contact_id = $filterData['contact_id'];
//$hubspot_owner_id = $filterData['hubspot_owner_id'];

//print_r($filterData);
//$body_contact_arr = array (
//	"properties" => $filterData
//);
//Obtener nombre del Asesor
//Registrar contacto en Hubspot
//$url = "https://api.hubapi.com/crm/v3/objects/contacts/".$contact_id;
//$response = $hs_controller->api_v3($url, $method = "PATCH", $data = $body_contact_arr);
//if ($response['success'] && $response['status'] == 200) {		
//}else{
//	print_r($response['data']);
//	echo '<br>Ocurrió un error al actualizar el contacto';	
//	die();
//}

$inputs = array();

for ($i = 1; $i <= $filterData['deal_num_cuo']; $i++) {
    $deal = array (
        "properties" => [
            "amount" => $filterData['valor_cuota_'.$i],
            "dealname" => $filterData['deal_name']." cuota # ".$i,
            "dealstage" => $filterData['deal_stage'],
        ],
        "associations" => [
            [
                'to' => [
                    'id' => $filterData['deal_id'],
                ],
                'types' => [
                    [
                        'associationCategory' => 'HUBSPOT_DEFINED',
                        'associationTypeId' => 451,
                    ],
                ],
            ],
        ]
    );

    array_push($inputs, $deal);
}

$url = "https://api.hubapi.com/crm/v3/objects/deals/batch/create";
$response = $hs_controller->api_v3($url, $method = "POST", $data = $inputs);
/*echo '<br>DEAL<br>';
print_r($response);*/
if ($response['success'] && $response['status'] == 200) {
        
}else{
    print_r($response);
    echo '<br>Ocurrió un error al actualizar el negocio';
    die();
}