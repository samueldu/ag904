<?php

// Database for core
$dbcore = new DB(DB_DRIVER_CORE, DB_HOSTNAME_CORE, DB_USERNAME_CORE, DB_PASSWORD_CORE, DB_DATABASE_CORE);
$registry->set('dbcore', $dbcore);

if(isset($_SERVER['HTTPS'])){
    $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
}
else{
    $protocol = 'http';
}

$request_uri = explode("index.php",$_SERVER['REQUEST_URI']);

$url = rtrim(str_replace("admin","",getBaseUrl()),'/');;

$sql = "SELECT * FROM `" . DB_PREFIX_CORE . "store_to_client`
INNER JOIN `" . DB_PREFIX_CORE . "clients` on `" . DB_PREFIX_CORE . "store_to_client`.id_client = `" . DB_PREFIX_CORE . "clients`.id
WHERE
(dev_url LIKE '%".rtrim(str_replace(array("http://","www."),"",$url), "/")."%'
or dev_url = '".$url."'
or dev_url = '".rtrim($url, "/")."'
or dev_url = 'http://".$url."'
or dev_url = 'http://".$url."/')
or (prod_url LIKE '%".rtrim(str_replace(array("http://","www."),"",$url), "/")."%'
or prod_url = '".$url."'
or prod_url = '".str_replace("www.","",$url)."'
or prod_url = '".str_replace("www.","",$url)."'
or prod_url = '".$url."/'
or prod_url = 'http://".$url."'
or prod_url = '".rtrim($url, "/")."'
or prod_url = 'http://".$url."/') LIMIT 1";

$query = $dbcore->query($sql);

if($ambient == "local")
{
    $var = "dev_";
    $base_admin = "http://{base_url}/ag904/core/admin/";
    $base_core = BASE_CORE;
}
else
{
    $var = "prod_";
    $base_admin = "http://core.agenciahost.com.br/admin/";
    $base_core = '/dados/www/core/';
}

$base_core = str_replace("{base_url}", $_SERVER['HTTP_HOST'], $base_core);
$base_admin = str_replace("{base_url}", $_SERVER['HTTP_HOST'], $base_admin);

define('DB_DRIVER', 'mysqli');
define('DB_HOSTNAME', $query->row['config_'.$var.'host']);
define('DB_USERNAME', $query->row['config_'.$var.'user']);
define('DB_PASSWORD', $query->row['config_'.$var.'pass']);
define('DB_DATABASE', $query->row['config_'.$var.'base']);
define('DB_PREFIX', 'oc_');
define('DB_PORT', '3306');
define('ID_CLIENT', $query->row['id_client']);

/* fiz para ambiente seguro no admin */
$query->row[$var.'url'] = str_replace("https","http",$query->row[$var.'url']);

/*
define('DB_DRIVER', 'mysqli',true);
define('DB_DATABASE', $var->database->base,true);
define('DB_HOSTNAME', $var->database->host,true);
define('DB_USERNAME', $var->database->user,true);
define('DB_PASSWORD', $var->database->pass,true);
*/

define('BASE_DIR',$query->row[$var.'path']);
define('BASE_URL', $query->row[$var.'url'].'/');
define('BASE_URL_PROD', $query->row['prod_url'].'/');

define('BASE_URL_ADMIN',$base_admin);
define('NOME_LOJA','Admin');

define('DIR_IMAGE', BASE_DIR.'image/');
define('DIR_CACHE', BASE_DIR.'system/cache/');
define('DIR_DOWNLOAD', BASE_DIR.'download/');
define('DIR_LOGS', BASE_DIR.'system/logs/');
define('HTTP_IMAGE', BASE_URL."image/");
define('DIR_UPLOAD', BASE_DIR.'upload/');
define('DIR_CATALOG', BASE_DIR.'catalog/');

// HTTP
define('HTTP_SERVER', BASE_URL.'admin/');
define('HTTP_CATALOG', BASE_URL);

// HTTPS
define('HTTPS_SERVER', BASE_URL);
define('HTTPS_CATALOG',  BASE_URL);