<?php
class ModelModuleLTNewsletter extends Model
{
	public function row($email)
	{
		$query = $this->db->query("SELECT * FROM ". DB_PREFIX ."lt_newsletter where email = '". $email ."'");
		return $query->row;
	}
	
	public function subscribe($email)
	{
		$this->db->query("INSERT INTO ". DB_PREFIX ."lt_newsletter SET email = '". $email ."' ");
	}
	
	public function unsubscribe($email)
	{
		$this->db->query("DELETE FROM ". DB_PREFIX ."lt_newsletter WHERE email = '". $email ."'");
	}
	
	public function add_table()
	{
		$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "lt_newsletter` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `email` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
		");
	}
}