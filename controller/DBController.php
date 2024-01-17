<?php

include_once 'HubspotController.php';

class DBController {

    public $db_key;
    public $server;
    public $duser;
    public $db;
    public $client_id;
    public $client_secret;

    public function __construct($pDBK, $pCI, $pCS) {
        $this->db_key = $pDBK;
        $this->server = "138.197.104.102";
        $this->duser = "usuario_db";
        $this->db = "clientes";
        $this->client_id = $pCI;
        $this->client_secret = $pCS;
    }

    function getProperty($portal_id, $prop):string {
        $token = '';
        $conn = new mysqli($this->server, $this->duser, $this->db_key, $this->db);
        if($conn->connect_error) {
            die("ERROR al conectarse a la base de datos: ".$conn->connect_error);
        }
        $sql = "SELECT ".$prop." FROM det_client WHERE PortalID=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $portal_id);
        $stmt->execute();
        $stmt->bind_result($token);
        if($stmt->fetch()) {
        }
        else {
            $token = "no-token-f";
        }
        $stmt->close();
        $conn->close();
        return $token;
    }

    function getPipeDetails($portal_id):array {
        $pipe = "";
        $stage = "";
        $conn = new mysqli($this->server, $this->duser, $this->db_key, $this->db);
        if($conn->connect_error) {
            die("ERROR al conectarse a la base de datos: ".$conn->connect_error);
        }
        $sql = "SELECT PipelineID, StageID FROM det_client WHERE PortalID=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $portal_id);
        $stmt->execute();
        $stmt->bind_result($pipe, $stage);
        $ret = array();
        if($stmt->fetch()) {
            $ret = array (
                "pipe_id" => $pipe,
                "stage_id" => $stage
            );
        }
        $stmt->close();
        $conn->close();
        return $ret;
    }

    function updateToken($portal_id):void {
        $refTok = $this->getProperty($portal_id, "RefToken");
        $url = "https://api.hubapi.com/oauth/v1/token";
        $body = "grant_type=refresh_token&client_id=".$this->client_id."&client_secret=".$this->client_secret."&refresh_token=".$refTok;
        $hs_c = new HubspotController();
        $res = $hs_c->getNewToken($url, $data = $body);
        $newData = [];
        if($res['success'] && $res['status'] == 200) {
            $newData = json_decode($res['data'], true);
        }
        else {
            echo "Error al obtener nuevo token<br>";
            print_r($res);
            print_r($refTok);
            die();
        }
        $conn = new mysqli($this->server, $this->duser, $this->db_key, $this->db);
        if($conn->connect_error) {
            die("ERROR al conectarse a la base de datos: ".$conn->connect_error);
        }
        $sql = "UPDATE det_client SET OAToken=?, RefToken=? WHERE PortalID=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $newData['access_token'], $newData['refresh_token'], $portal_id);
        if($stmt->execute()) {
        }
        else {
            echo "Error al actualizar la bd";
            die();
        }
        $stmt->close();
        $conn->close();
    }
}
