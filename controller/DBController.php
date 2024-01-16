<?php
class DBController {

    public $db_key;
    public $server;
    public $duser;
    public $db;

    public function __construct($pDBK) {
        $this->db_key = $pDBK;
        $this->server = "138.197.104.102";
        $this->duser = "usuario_db";
        $this->db = "clientes";
    }

    function getToken($portal_id):string {
        $token = '';
        $conn = new mysqli($this->server, $this->duser, $this->db_key, $this->db);
        if($conn->connect_error) {
            die("ERROR al conectarse a la base de datos: ".$conn->connect_error);
        }
        $sql = "SELECT OAToken FROM det_client WHERE PortalID='?'";
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
}
