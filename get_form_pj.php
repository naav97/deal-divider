<?php
include_once 'controller/HubspotController.php';
include_once 'controller/HelperController.php';

$deal_id = $_GET['deal_id'];

$propD = [];

$env = parse_ini_file('.env');

$hubspot_obj = new HubspotController($env["ACCESS_TOKEN"]);
$helper_obj = new HelperController($env["ACCESS_TOKEN"]);

function generate_property_params($properties_list) {
    return '?properties='.join('%2C', $properties_list);
}

$deal_params = generate_property_params(["amount","cuotas"]);

$urlD = 'https://api.hubapi.com/crm/v3/objects/deals/'.$deal_id.$deal_params.'&archived=false';

$deal_resp = $hubspot_obj->api_v3($urlD, $method = "GET");

if($deal_resp['success'] && $deal_resp['status'] == 200) {
    $propD = json_decode($deal_resp['data'], true)['properties'];
}

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
    <form enctype="multipart/form-data" method="POST" action="/mavesa/documento_negocio/get_form_send_pj.php">
    <input type="hidden" value="<?php echo $deal_id; ?>" name="deal_id">
    <!-- <input type="hidden" value="<?php echo $propD['hubspot_owner_id']; ?>" name="hubspot_owner_id"> -->

    <div class="responsive-table"  style="margin-bottom: -20px;">
        <div class="content-1">
            <div>
                <span class="span-cont-1">Compañia:<input type="text" name="deal[compania]" value="<?php echo $propD['compania']; ?>"></span><br>
                <span class="span-cont-1">Agencia:<input type="text" name="deal[agencia]" value="<?php echo $propD['agencia']; ?>"></span><br>
                <span class="span-cont-1">Fecha de Solicitud:<input type="date" name="deal[fecha_de_solicitud]" value="<?php echo $propD['fecha_de_solicitud']; ?>"></span><br>
            </div>
            <div>
                <span class="span-cont-1">Solicitud de Crédito:<input type="text" name="deal[solicitud_de_credito]" value="<?php echo $propD['solicitud_de_credito']; ?>"></span><br>
                <span class="span-cont-1">Asesor de Ventas:<input type="text" name="deal[asesor]" value="<?php echo $owner_name; ?>"></span><br>
                <span class="span-cont-1">Código de Producto:<input type="text" name="deal[codigo_del_producto]" value="<?php echo $propD['codigo_del_producto']; ?>"></span><br>
                <span class="span-cont-1">Prioridad:
                    <select name="deal[hs_priority]">
                    <option value="low" <?php if (isset($propD['hs_priority']) && $propD['hs_priority'] == "low") {
                    echo "selected";
} ?>>Baja</option>
    <option value="medium" <?php if (isset($propD['hs_priority']) && $propD['hs_priority'] == "medium") {
    echo "selected";
                    } ?>>Media</option>
                        <option value="high" <?php if (isset($propD['hs_priority']) && $propD['hs_priority'] == "high") {
                        echo "selected";
    } ?>>Alta</option>
                    </select>
                </span><br>
                <span class="span-cont-1">Marca:<input type="text" name="deal[marca]" value="<?php echo $propD['marca']; ?>"></span><br>
                <span class="span-cont-1">Modelo:<input type="text" name="deal[modelo]" value="<?php echo $propD['modelo']; ?>"></span><br>
            </div>
            <div>
                <span class="span-cont-1">Cuota Inicial:<input type="text" name="deal[cuota_inicial]" value="<?php echo $propD['cuota_inicial']; ?>"></span><br>
                <span class="span-cont-1">Saldo a Financiar:<input type="number" name="deal[monto_a_financiar]" value="<?php echo $propD['monto_a_financiar']; ?>"></span><br>
                <span class="span-cont-1">Tasa de Interés:<input type="text" name="deal[tasa]" value="<?php echo $propD['tasa']; ?>"></span><br>
                <span class="span-cont-1">Periodos de Gracia:<input type="text" name="deal[periodos_de_gracia]" value="<?php echo $propD['periodos_de_gracia']; ?>"></span><br>
                <span class="span-cont-1">Valor de Cuota:<input type="text" name="deal[cuota_mensual]" value="<?php echo $propD['cuota_mensual']; ?>"></span><br>
                <span class="span-cont-1">Cupo Solicitado:<input type="text" name="deal[cupo_solicitado]" value="<?php echo $propD['cupo_solicitado']; ?>"></span><br>
            </div>
        </div>
    </div>

    <div class="responsive-table-2">
        <table class="table-2 table-2-print"  >
            <thead>
                <tr>
                    <td colspan="4" class="text-center">EMPRESA</td>
                </tr>
            </thead>
            <tbody class="text-center">
                <tr>
                   <td colspan="2">Razón Social</td>
                   <td>RUC</td>
                   <td>Fecha de Constitución</td>
                </tr>
                <tr>
                    <td colspan="2">
                        <input type="text" name="name" value="<?php echo $prop['name']; ?>">
                    </td>
                    <td>
                        <input type="text" name="ruc" value="<?php echo $prop['ruc']; ?>">
                    </td>
                    <td>
                        <input type="date" name="fecha_de_constitucion" value="<?php echo $prop['fecha_de_constitucion']; ?>">
                    </td>
                </tr>
                <tr>
                    <td colspan="4">Actividad(es) Principal(es) / Objeto Social</td>
                </tr>
                <tr>
                    <td colspan="4">
                        <textarea rows="2" name="actividad_es__principal_es____objeto_social"><?php echo $prop['actividad_es__principal_es____objeto_social']; ?></textarea>
                    </td>
                </tr>
                <!-- <tr>
                    <td colspan="4">.</td>
                </tr> -->
                <tr>
                    <td>Dirección Completa: </td>
                    <td colspan="3">
                        <input type="text" name="country" placeholder="País" value="<?php echo $prop['country']; ?>">
                        <input type="text" name="state" placeholder="Región / Provincia / Departamento / Estado" value="<?php echo $prop['state']; ?>">
                        <input type="text" name="city" placeholder="Ciudad" value="<?php echo $prop['city']; ?>">
                        <input type="text" name="address" placeholder="Dirección" value="<?php echo $prop['address']; ?>">
                    </td>
                </tr>
                <!-- <tr>
                    <td colspan="4">.</td>
                </tr> -->
                <tr>
                    <td>Telf. fijo</td>
                    <td>Email</td>
                    <td>Contacto Principal</td>
                    <td>Cargo del Contacto</td>
                </tr>
                <tr>
                    <td>
                        <input type="tel" name="phone" value="<?php echo $prop['phone']; ?>">
                    </td>
                    <td>
                        <input type="email" name="email" value="<?php echo $prop['email']; ?>">
                    </td>
                    <td>
                        <input type="text" name="contacto_principal" value="<?php echo $prop['contacto_principal']; ?>">
                    </td>
                    <td>
                        <input type="text" name="cargo_del_contacto_principal" value="<?php echo $prop['cargo_del_contacto_principal']; ?>">
                    </td>
                </tr>
                <!-- <tr>
                    <td>.</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr> -->
            </tbody>
        </table>
    </div>

    <div class="responsive-table-2">
        <table class="table-2">
            <thead>
                <tr>
                    <td colspan="5" class="text-center">REPRESENTANTE LEGAL</td>
                </tr>
            </thead>
            <tbody class="text-center">
                <tr>
                   <td colspan="2">Apellidos y nombres</td>
                   <td>Cedula de Ciudadanía</td>
                   <td colspan="2">Nacionalidad</td>
                </tr>
                <tr>
                    <td colspan="2">
                        <input type="text" name="apellidos_y_nombres_representante_legal" value="<?php echo $prop['apellidos_y_nombres_representante_legal']; ?>">
                    </td>
                    <td>
                        <input type="number" name="cedula_de_ciudadania" value="<?php echo $prop['cedula_de_ciudadania']; ?>">
                    </td>
                    <td colspan="2">
                        <input type="text" name="nacionalidad_representante_legal" value="<?php echo $prop['nacionalidad_representante_legal']; ?>">
                    </td>
                </tr>
                <tr>
                    <td>Email</td>
                    <td>Telf. fijo</td>
                    <td>Telf. Movil</td>
                    <td>Estado Civil</td>
                    <!-- <td>Edad</td> -->
                </tr>
                <tr>
                    <td>
                        <input type="email" name="email_representante_legal" value="<?php echo $prop['email_representante_legal']; ?>">
                    </td>
                    <td>
                        <input type="tel" name="telef__fijo_representante_legal" value="<?php echo $prop['telef__fijo_representante_legal']; ?>">
                    </td>
                    <td>
                        <input type="tel" name="telef__movil_representante_legal" value="<?php echo $prop['telef__movil_representante_legal']; ?>">
                    </td>
                    <td>
                        <select name="estado_civil_representante_legal__cloned_">
                        <option value="Soltero" <?php if (isset($prop['estado_civil_representante_legal__cloned_']) && $prop['estado_civil_representante_legal__cloned_'] == "Soltero") {
                        echo "selected";
                        } ?>>Soltero(a)</option>
                            <option value="Casado" <?php if (isset($prop['estado_civil_representante_legal__cloned_']) && $prop['estado_civil_representante_legal__cloned_'] == "Casado") {
                            echo "selected";
                        } ?>>Casado(a)</option>
                            <option value="Unión libre" <?php if (isset($prop['estado_civil_representante_legal__cloned_']) && $prop['estado_civil_representante_legal__cloned_'] == "Unión libre") {
                            echo "selected";
                            } ?>>Unión libre</option>
                                <option value="Separado" <?php if (isset($prop['estado_civil_representante_legal__cloned_']) && $prop['estado_civil_representante_legal__cloned_'] == "Separado") {
                                echo "selected";
                            } ?>>Separado(a)</option>
                                <option value="Divorciado" <?php if (isset($prop['estado_civil_representante_legal__cloned_']) && $prop['estado_civil_representante_legal__cloned_'] == "Divorciado") {
                                echo "selected";
                                } ?>>Divorciado(a)</option>
                                    <option value="Viudo" <?php if (isset($prop['estado_civil_representante_legal__cloned_']) && $prop['estado_civil_representante_legal__cloned_'] == "Viudo") {
                                    echo "selected";
                                } ?>>Viudo(a)</option>
                        </select>
                    </td>
                    <!-- <td></td> -->
                </tr>
            </tbody>
        </table>
    </div>

<!---------------------------------------------------------------------------------------------------------------->
    <div class="espaciado-top" >
        <div class="text-center">
            <span>REFERENCIAS BANCARIAS Y PROVEEDORES</span>
        </div>
        <div>
            <span><i>Bancarias</i></span>
        </div>
        <div class="responsive-table" style="margin-top: 0px;">
            <table class="table-4">
                <thead>
                    <tr class="text-center">
                        <td>Institución Financiera</td>
                        <td>Número de cuenta</td>
                        <td>Tipo</td>
                        <td>Antigüedad</td>
                        <td>Cifras Promedio</td>
                        <td>Crédito</td>
                        <td>Protestos</td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <input type="text" name="institucion_financiera_referencia_bancaria_1" value="<?php echo $prop['institucion_financiera_referencia_bancaria_1']; ?>">
                        </td>
                        <td>
                            <input type="text" name="numero_de_cuenta_referencia_bancaria_1" value="<?php echo $prop['numero_de_cuenta_referencia_bancaria_1']; ?>">
                        </td>
                        <td>
                            <input type="text" name="tipo_de_cuenta_referencia_bancaria_1" value="<?php echo $prop['tipo_de_cuenta_referencia_bancaria_1']; ?>">
                        </td>
                        <td>
                            <input type="number" name="antiguedad_referencia_bancaria_1" value="<?php echo $prop['antiguedad_referencia_bancaria_1']; ?>">
                        </td>
                        <td>
                            <input type="number" name="cifras_promedio_referencia_bancaria_1" value="<?php echo $prop['cifras_promedio_referencia_bancaria_1']; ?>">
                        </td>
                        <td>
                            <input type="text" name="credito_referencia_bancaria_1" value="<?php echo $prop['credito_referencia_bancaria_1']; ?>">
                        </td>
                        <td>
                            <input type="text" name="protestos_referencia_bancaria_1" value="<?php echo $prop['protestos_referencia_bancaria_1']; ?>">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="text" name="institucion_financiera_referencia_bancaria_2" value="<?php echo $prop['institucion_financiera_referencia_bancaria_2']; ?>">
                        </td>
                        <td>
                            <input type="text" name="numero_de_cuenta_referencia_bancaria_2" value="<?php echo $prop['numero_de_cuenta_referencia_bancaria_2']; ?>">
                        </td>
                        <td>
                            <input type="text" name="tipo_de_cuenta_referencia_bancaria_2" value="<?php echo $prop['tipo_de_cuenta_referencia_bancaria_2']; ?>">
                        </td>
                        <td>
                            <input type="number" name="antiguedad_referencia_bancaria_2" value="<?php echo $prop['antiguedad_referencia_bancaria_2']; ?>">
                        </td>
                        <td>
                            <input type="number" name="cifras_promedio_referencia_bancaria_2" value="<?php echo $prop['cifras_promedio_referencia_bancaria_2']; ?>">
                        </td>
                        <td>
                            <input type="text" name="credito_referencia_bancaria_2" value="<?php echo $prop['credito_referencia_bancaria_2']; ?>">
                        </td>
                        <td>
                            <input type="text" name="protestos_referencia_bancaria_2" value="<?php echo $prop['protestos_referencia_bancaria_2']; ?>">
                        </td>
                    </tr>
                        <td>
                            <input type="text" name="institucion_financiera_referencia_bancaria_3" value="<?php echo $prop['institucion_financiera_referencia_bancaria_3']; ?>">
                        </td>
                        <td>
                            <input type="text" name="numero_de_cuenta_referencia_bancaria_3" value="<?php echo $prop['numero_de_cuenta_referencia_bancaria_3']; ?>">
                        </td>
                        <td>
                            <input type="text" name="tipo_de_cuenta_referencia_bancaria_3" value="<?php echo $prop['tipo_de_cuenta_referencia_bancaria_3']; ?>">
                        </td>
                        <td>
                            <input type="number" name="antiguedad_referencia_bancaria_3" value="<?php echo $prop['antiguedad_referencia_bancaria_3']; ?>">
                        </td>
                        <td>
                            <input type="number" name="cifras_promedio_referencia_bancaria_3" value="<?php echo $prop['cifras_promedio_referencia_bancaria_3']; ?>">
                        </td>
                        <td>
                            <input type="text" name="credito_referencia_bancaria_3" value="<?php echo $prop['credito_referencia_bancaria_3']; ?>">
                        </td>
                        <td>
                            <input type="text" name="protestos_referencia_bancaria_3" value="<?php echo $prop['protestos_referencia_bancaria_3']; ?>">
                        </td>
                    <tr>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="espaciado-top-2" style="margin-top: 16px;">
            <span><i>Proveedores Nacionales y/o Extranjeros</i></span>
        </div>
        <div class="responsive-table" style="margin-top: 0px;">
            <table class="table-4">
                <thead>
                    <tr class="text-center">
                        <td>Nombres o Razón Comercial</td>
                        <td>Insumo / Producto</td>
                        <td>Compras Mensuales</td>
                        <td>Plazo</td>
                        <td>Forma de Pago</td>
                        <td>Cargo del Contacto</td>
                        <td>Telf. Contacto</td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <input type="text" name="nombre_o_razon_comercial_proveedor_1" value="<?php echo $prop['nombre_o_razon_comercial_proveedor_1']; ?>">
                        </td>
                        <td>
                            <input type="text" name="insumo___producto_proveedor_1" value="<?php echo $prop['insumo___producto_proveedor_1']; ?>">
                        </td>
                        <td>
                            <input type="number" name="compras_mensuales_proveedor_1" value="<?php echo $prop['compras_mensuales_proveedor_1']; ?>">
                        </td>
                        <td>
                            <input type="text" name="plazo_proveedor_1" value="<?php echo $prop['plazo_proveedor_1']; ?>">
                        </td>
                        <td>
                            <input type="text" name="forma_de_pago_proveedor_1" value="<?php echo $prop['forma_de_pago_proveedor_1']; ?>">
                        </td>
                        <td>
                            <input type="text" name="cargo_de_contacto_proveedor_1" value="<?php echo $prop['cargo_de_contacto_proveedor_1']; ?>">
                        </td>
                        <td>
                            <input type="tel" name="tel__contacto_proveedor_1" value="<?php echo $prop['tel__contacto_proveedor_1']; ?>">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="text" name="nombre_o_razon_comercial_proveedor_2" value="<?php echo $prop['nombre_o_razon_comercial_proveedor_2']; ?>">
                        </td>
                        <td>
                            <input type="text" name="insumo___producto_proveedor_2" value="<?php echo $prop['insumo___producto_proveedor_2']; ?>">
                        </td>
                        <td>
                            <input type="number" name="compras_mensuales_proveedor_2" value="<?php echo $prop['compras_mensuales_proveedor_2']; ?>">
                        </td>
                        <td>
                            <input type="text" name="plazo_proveedor_2" value="<?php echo $prop['plazo_proveedor_2']; ?>">
                        </td>
                        <td>
                            <input type="text" name="forma_de_pago_proveedor_2" value="<?php echo $prop['forma_de_pago_proveedor_2']; ?>">
                        </td>
                        <td>
                            <input type="text" name="cargo_de_contacto_proveedor_2" value="<?php echo $prop['cargo_de_contacto_proveedor_2']; ?>">
                        </td>
                        <td>
                            <input type="tel" name="tel__contacto_proveedor_2" value="<?php echo $prop['tel__contacto_proveedor_2']; ?>">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="text" name="nombre_o_razon_comercial_proveedor_3" value="<?php echo $prop['nombre_o_razon_comercial_proveedor_3']; ?>">
                        </td>
                        <td>
                            <input type="text" name="insumo___producto_proveedor_3" value="<?php echo $prop['insumo___producto_proveedor_3']; ?>">
                        </td>
                        <td>
                            <input type="number" name="compras_mensuales_proveedor_3" value="<?php echo $prop['compras_mensuales_proveedor_3']; ?>">
                        </td>
                        <td>
                            <input type="text" name="plazo_proveedor_3" value="<?php echo $prop['plazo_proveedor_3']; ?>">
                        </td>
                        <td>
                            <input type="text" name="forma_de_pago_proveedor_3" value="<?php echo $prop['forma_de_pago_proveedor_3']; ?>">
                        </td>
                        <td>
                            <input type="text" name="cargo_de_contacto_proveedor_3" value="<?php echo $prop['cargo_de_contacto_proveedor_3']; ?>">
                        </td>
                        <td>
                            <input type="tel" name="tel__contacto_proveedor_3" value="<?php echo $prop['tel__contacto_proveedor_3']; ?>">
                        </td>
                    </tr>
                </tbody>
            </table>
         </div>
         <div class="espaciado-top-2 text-center">
            <span>RESUMEN FINANCIERO</span>
        </div>
        <div class="responsive-table estados">
            <table class="table-4">
                <thead>
                    <tr class="text-left">
                        <td>Periodo Fiscal:</td>
                        <td>
                            <input type="text" name="periodo_fiscal_1" value="<?php echo $prop['periodo_fiscal_1']; ?>">
                        </td>
                        <td>Periodo Fiscal:</td>
                        <td>
                            <input type="text" name="periodo_fiscal_2" value="<?php echo $prop['periodo_fiscal_2']; ?>">
                        </td>
                    </tr>
                </thead>
                <tbody>
                    <tr class="text-left">
                        <td>Activos:</td>
                        <td>
                            <input id="activos-1" type="number" name="activos_periodo_1" value="<?php echo $prop['activos_periodo_1']; ?>">
                        </td>
                        <td>Activos:</td>
                        <td>
                            <input id="activos-2" type="number" name="activos_periodo_2" value="<?php echo $prop['activos_periodo_2']; ?>">
                        </td>
                    </tr>
                    <tr class="text-left">
                        <td>Pasivos:</td>
                        <td>
                            <input id="pasivo-1" type="number" name="pasivos_periodo_1" value="<?php echo $prop['pasivos_periodo_1']; ?>">
                        </td>
                        <td>Pasivos:</td>
                        <td>
                            <input id="pasivo-2" type="number" name="pasivos_periodo_2" value="<?php echo $prop['pasivos_periodo_2']; ?>">
                        </td>
                    </tr>
                    <tr class="text-left">
                        <td>Patrimonio:</td>
                        <td>
                            <input id="patri-1" type="number" name="patrimonio_periodo_1__cloned_" readonly value="<?php echo $prop['patrimonio_periodo_1__cloned_']; ?>">
                        </td>
                        <td>Patrimonio:</td>
                        <td>
                            <input id="patri-2" type="number" name="patrimonio_periodo_2__cloned_" readonly value="<?php echo $prop['patrimonio_periodo_2__cloned_']; ?>">
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="espaciado-top-2 text-center">
            <span>ESTADOS DE PÉRDIDAS Y GANANCIAS</span>
        </div>
        <div class="responsive-table estados">
            <table class="table-4">
                <thead>
                    <tr class="text-left">
                        <td>Periodo Fiscal:</td>
                        <td>
                            <input type="text" name="periodo_fiscal_1_estados" value="<?php echo $prop['periodo_fiscal_1_estados']; ?>">
                        </td>
                        <td>Periodo Fiscal:</td>
                        <td>
                            <input type="text" name="periodo_fiscal_2_estados" value="<?php echo $prop['periodo_fiscal_2_estados']; ?>">
                        </td>
                    </tr>
                </thead>
                <tbody>
                    <tr class="text-left">
                        <td>Ingresos:</td>
                        <td class="text-right">
                            <input id="ingre-1" type="number" name="ingresos_periodo_1" value="<?php echo $prop['ingresos_periodo_1']; ?>">
                        </td>
                        <td>Ingresos:</td>
                        <td>
                            <input id="ingre-2" type="number" name="ingresos_periodo_2" value="<?php echo $prop['ingresos_periodo_2']; ?>">
                        </td>
                    </tr>
                    <tr class="text-left">
                        <td>Costos y Gastos:</td>
                        <td class="text-right">
                            <input id="gasto-1" type="number" name="costos_y_gastos_periodo_1" value="<?php echo $prop['costos_y_gastos_periodo_1']; ?>">
                        </td>
                        <td>Costos y Gastos:</td>
                        <td>
                            <input id="gasto-2" type="number" name="costos_y_gastos_periodo_2" value="<?php echo $prop['costos_y_gastos_periodo_2']; ?>">
                        </td>
                    </tr>
                    <tr class="text-left">
                        <td>Utilidad:</td>
                        <td class="text-right">
                            <input id="util-1" type="number" name="utilidad_periodo_1__cloned_" readonly value="<?php echo $prop['utilidad_periodo_1__cloned_']; ?>">
                        </td>
                        <td>Utilidad:</td>
                        <td>
                            <input id="util-2" type="number" name="utilidad_periodo_2__cloned_" readonly value="<?php echo $prop['utilidad_periodo_2__cloned_']; ?>">
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="espaciado-top-2 text-center">
            <span>GARANTÍAS</span>
        </div>
        <div class="responsive-table estados">
            <table class="table-4">
                <thead>
                    <tr class="text-left">
                        <td>Periodo Fiscal:</td>
                        <td>
                            <input type="text" name="periodo_fiscal_1_garantias" value="<?php echo $prop['periodo_fiscal_1_garantias']; ?>">
                        </td>
                        <td>Periodo Fiscal:</td>
                        <td>
                            <input  type="text" name="periodo_fiscal_2_garantias" value="<?php echo $prop['periodo_fiscal_2_garantias']; ?>">
                        </td>
                    </tr>
                </thead>
                <tbody>
                    <tr class="text-left">
                        <td>Suc. de la Garantía:</td>
                        <td class="text-right">
                            <input  type="text" name="suc__de_la_garantia" value="<?php echo $prop['suc__de_la_garantia']; ?>">
                        </td>
                        <td>Valor de la Garantía:</td>
                        <td>
                            <input type="number" name="valor_de_la_garantia" value="<?php echo $prop['valor_de_la_garantia']; ?>">
                        </td>
                    </tr>
                    <tr class="text-left">
                        <td>ID Garantía:</td>
                        <td class="text-right">
                            <input type="text" name="id_garantia" value="<?php echo $prop['id_garantia']; ?>">
                        </td>
                        <td>Fecha de Inicio:</td>
                        <td>
                            <input type="date" name="fecha_de_inicio_garantia" value="<?php echo $prop['fecha_de_inicio_garantia']; ?>">
                        </td>
                    </tr>
                    <tr class="text-left">
                        <td>Desc. Garantía:</td>
                        <td class="text-right">
                            <input type="text" name="desc__garantia" value="<?php echo $prop['desc__garantia']; ?>">
                        </td>
                        <td>Fecha de Finalización:</td>
                        <td>
                            <input type="date" name="fecha_de_finalizacion_garantia" value="<?php echo $prop['fecha_de_finalizacion_garantia']; ?>">
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <!------------------------------------------------------------------------------------------------------------>
    <div class="espaciado-top croquis-print">
        <div class="text-center">
            <span>CROQUIS</span>
        <hr>
        </div>
        <div class="text-center">
            <span>N</span>
        </div>
        <div class="croquis">
            <div>O</div>
            <input type="file" name="croquis" value="<?php echo $prop['croquis']; ?>">
            <div>E</div>
        </div>
        <div class="text-center">
            <span>S</span>
        </div>
        <hr>
    </div>
    <input type="submit">
    </form>
    <!------------------------------------------------------------------------------------------------------------>
    <script src="js/script-pj.js"></script>
</body>
</html>
