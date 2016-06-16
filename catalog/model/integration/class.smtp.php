<?php
class ModelIntegrationClearSale extends Model {
    function insert($data) {

        $query = $this->db->query("insert into " . DB_PREFIX . "clearSale (idPedido,status,score) VALUES ('$data[ID]','$data[status]','$data[score]')");
        
    }
}
?>