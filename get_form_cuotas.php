<?php
include_once 'controller/HubspotController.php';
include_once 'controller/HelperController.php';
include_once 'controller/DBController.php';

$portal_id = $_GET['portalId'];
$deal_id = $_GET['deal_id'];
//$deal_id = "16385417769";

$propD = [];

$env = parse_ini_file('.env');

$db_cont = new DBController($env['DB_PASS'], $env['CLIENT_ID'], $env['CLIENT_SECRET']);

$hubspot_obj = new HubspotController($db_cont->getProperty($portal_id, "OAToken"));
//$helper_obj = new HelperController($env["ACCESS_TOKEN"]);

function generate_property_params($properties_list) {
    return '?properties='.join('%2C', $properties_list);
}

function get_deal_props($deal_id, $hubspot_obj) {
    $deal_params = generate_property_params(["amount","cuotas","dealname","dealstage"]);

    $urlD = 'https://api.hubapi.com/crm/v3/objects/deals/'.$deal_id.$deal_params.'&archived=false';

    $deal_resp = $hubspot_obj->api_v3($urlD, $method = "GET");

    if($deal_resp['success'] && $deal_resp['status'] == 200) {
        return json_decode($deal_resp['data'], true)['properties'];
    }
    elseif($deal_resp['success'] && $deal_resp['status'] == 401) {
        return "OT";
    }
    else {
        echo "Error obtenindo la informacion del negocio";
        die();
    }
}

function mainp($deal_id, $dbc, $portal_id, $hs_c) {
    $i = 0;
    $ret = "";
    while($i < 3) {
        $ret = get_deal_props($deal_id, $hs_c);
        if($ret == "OT") {
            $dbc->updateToken($portal_id);
            $i = $i + 1;
        }
        else {
            return $ret;
        }
    }
    echo "No se pudo completar la operacion";
    die();
}

$propD = mainp($deal_id, $db_cont, $portal_id, $hubspot_obj);

//$pipelines = [];
//
//$url = 'https://api.hubapi.com/crm/v3/pipelines/deal';
//
//$resp = $hubspot_obj->api_v3($url, $method = "GET");
//
//if($resp['success'] && $resp['status'] == 200) {
//    $pipelines = json_decode($resp['data'], true)['results'];
//}

//Obtener nombre sel asesor
//$url_owner = 'https://api.hubapi.com/crm/v3/owners/' . $propD['hubspot_owner_id'];
//
//$owner_resp = $hubspot_obj->api_v3($url_owner, $method = "GET");
//
//if ($owner_resp['success'] && $owner_resp['status'] == 200) {
//    $data_owner = json_decode($owner_resp['data'], true);
//    $owner_name = $data_owner['firstName'].' '.$data_owner['lastName'];
//    $owner_team = array_filter($data_owner['teams'], function ($team) {
//        return $team['primary'];
//    })[0];
//}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Deal Divider Grows</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- <div class="cabecera">
        <div class="cabecera-1">
            <img src="img/mavesa.png" width="60%" alt="">
        </div>
        <div class="cabecera-2">
            <span>Solicitud de Crédito - Corporativo</span>
        </div>
    </div> -->

    <div class="container">
        <!-- <form enctype="multipart/form-data" method="POST" action="/mavesa/documento_negocio/get_form_send_pj.php"> -->
        <form enctype="multipart/form-data" method="POST" action="https://colaborador.grows.pro/deal-divider/create_sub_deals.php">
            <!-- <input type="hidden" value="<?php echo $propD['hubspot_owner_id']; ?>" name="hubspot_owner_id"> -->
            <input type="hidden" value="<?php echo $deal_id; ?>" name="deal_id">
            <input type="hidden" value="<?php echo $propD['dealname']; ?>" name="deal_name">
            <input id="imp-cuo" type="hidden" value="<?php echo $propD['cuotas']; ?>" name="deal_num_cuo">
            <input id="imp-amo" type="hidden" value="<?php echo $propD['amount']; ?>" name="deal_amo">
            <input type="hidden" value="<?php echo $propD['dealstage']; ?>" name="deal_stage">
            <table class="table">
                <thead>
                    <tr>
                        <th>CUOTA</th>
                        <th>MONTO</th>
                        <th>FECHA DE PAGO</th>
                    </tr>
                </thead>
                <tbody>
                    <?php for ($i = 1; $i <= $propD['cuotas']; $i++) { ?>
                    <tr>
                        <td>
                            <span>Cuota <?php echo $i; ?></span>
                        </td>
                        <td>
                            <input id="imp-cuo-<?php echo $i; ?>" class="private-form__control" type="number" value="<?php echo $propD['amount']/$propD['cuotas']; ?>" name="valor_cuota_<?php echo $i; ?>">
                        </td>
                        <!-- <span>Fecha de pago</span> -->
                        <td>
                            <input class="private-form__control" type="date" name="fecha_pago_cuota_<?php echo $i; ?>">
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
            <span id="sum-warn">Los campos no suman la cantidad correcta.</span>
            <input id="imp-sub" type="submit" value="Dividir negocio">
        </form>
    </div>

    <script src="js/script.js"></script>
</body>
</html>
