<?php
class ModelIntegrationIntegration extends Model
{
    public function checkStatus($option)
    {
        $sqlAux = "";

        if(isset($option['id_order']))
            $sqlAux .= "AND `" . DB_PREFIX . "integration`.id_order ='".$option['id_order']."'";

        if(isset($option['flag']))
            $sqlAux .= "AND `" . DB_PREFIX . "integration`.flag ='".$option['flag']."'";

        if(isset($option['vendor']))
            $sqlAux .= "AND `" . DB_PREFIX . "integration`.vendor ='".$option['vendor']."'";

        if(isset($option['type']))
            $sqlAux .= "AND `" . DB_PREFIX . "integration`.type ='".$option['type']."'";

        $sql = "SELECT * FROM `" . DB_PREFIX . "integration` where 1=1 ".$sqlAux;

        $result = $this->db->query($sql);
        if ($result->rows) {
            return true;
        } else {
            return false;
        }
    }

    public function insertStatus($data)
    {
        $sql = "INSERT INTO `" . DB_PREFIX . "integration`
        (id_order,flag,vendor,`type`) VALUES ('".$data['id_order']."','".$data['flag']."','".$data['vendor']."','".$data['type']."')";

        $result = $this->db->query($sql);
    }
}