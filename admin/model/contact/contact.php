<?php 
Class ModelContactContact extends Model{

public function getContactinfo($data = array()) {

        $sql = "SELECT * FROM " . DB_PREFIX . "contact";

/*        if (!empty($data['filter_name'])) {
            $sql .= " AND cd2.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
        }

        $sql .= " GROUP BY cp.category_id";
*/

        $sort_data = array(
            'data',
            'sort_order'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY sort_order";
        }


        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }



    public function getContacts($data = array()) {
        $query = $this->db->query("SELECT count(*) as total FROM " . DB_PREFIX . "contact");

        return $query->row['total'];

    }

public function getSingledata($id='') {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "contact where contact_id='$id' ");

     return $query->rows;
		
}

public function csvdata(){
	$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "contact");
    return $query->rows;
}


public function insertvalue($view_id) {
$this->db->query("UPDATE " . DB_PREFIX . "contact SET is_read='$view_id' where contact_id ='$view_id'");	
}

public function deletecontact($contact_id) {
		$query = $this->db->query("DELETE  FROM " . DB_PREFIX . "contact where contact_id = '".$contact_id."' ");

     return $query;	
}
    public function install(){
        $this->log->write('CONTACT Module --> Starting install');
		
		
        $sql = "
				CREATE TABLE `".DB_PREFIX."contact` (
					`contact_id` INT( 11 ) NOT NULL PRIMARY KEY AUTO_INCREMENT,
					`firstname` VARCHAR( 50 ) NOT NULL ,
					`email` VARCHAR( 100 ) NOT NULL ,
					`enquiry` TEXT( 1000 ) NOT NULL,
					`ipaddress` VARCHAR( 40 ) NOT NULL,
					`data` DATETIME NOT NULL,
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
        ";
        $query = $this->db->query($sql);
	   
	    $this->log->write('CONTACT Module --> Completed install');
    }
	
	 public function uninstall(){
        $this->log->write('CONTACT Module --> Starting uninstall');
		$this->db->query("DROP TABLE `".DB_PREFIX."contact`");
        $this->log->write('CONTACT Module --> Completed uninstall');
    }
}
?>