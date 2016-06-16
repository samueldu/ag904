<?php

// Database for core
$dbcore = new DB(DB_DRIVER_CORE, DB_HOSTNAME_CORE, DB_USERNAME_CORE, DB_PASSWORD_CORE, DB_DATABASE_CORE);
$registry->set('dbcore', $dbcore);

$url = rtrim(getBaseUrl(),"/");

$sql = "SELECT * FROM `" . DB_PREFIX_CORE . "store_to_client`
INNER JOIN `" . DB_PREFIX_CORE . "clients` on `" . DB_PREFIX_CORE . "store_to_client`.id_client = `" . DB_PREFIX_CORE . "clients`.id
WHERE
(dev_url LIKE '%".str_replace("http://","",$url)."%'
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
or prod_url = 'http://".$url."/')

 or (prod_ssl LIKE '%".rtrim(str_replace(array("http://","www."),"",$url), "/")."%'
or prod_ssl = '".$url."'
or prod_ssl = '".str_replace("www.","",$url)."'
or prod_ssl = '".str_replace("www.","",$url)."'
or prod_ssl = '".$url."/'
or prod_ssl = 'http://".$url."'
or prod_ssl = '".rtrim($url, "/")."'
or prod_ssl = 'http://".$url."/')

LIMIT 1";

$query = $dbcore->query($sql);

if($query->num_rows==0)
{
    print "loja nao encontrada";

    print "<pre>". $sql;
    exit;
}

if($ambient == "local")
{
    $var = "dev_";
}
else
{
    $var = "prod_";
}

define('DB_DRIVER', 'mysqli');
define('DB_HOSTNAME', $query->row['config_'.$var.'host']);
define('ID_CLIENT', $query->row['id_client']);
define('DB_USERNAME', $query->row['config_'.$var.'user']);
define('DB_PASSWORD', $query->row['config_'.$var.'pass']);
define('DB_DATABASE', $query->row['config_'.$var.'base']);
define('DB_PREFIX', 'oc_');
define('DB_PORT', '3306');

define('BASE_DIR',$query->row[$var.'path']);
define('BASE_URL', $query->row[$var.'url'].'/');

define('BASE_URL_SSL', $query->row[$var.'url'].'/');
/*
define('DIR_APPLICATION', BASE_CORE.'catalog/');
define('DIR_SYSTEM', BASE_CORE.'system/');
define('DIR_DATABASE', BASE_CORE.'system/database/');
define('DIR_CONFIG', BASE_CORE.'system/config/');
*/

define('DIR_IMAGE', BASE_DIR.'image/');
define('DIR_CACHE', BASE_DIR.'system/cache/');
define('DIR_DOWNLOAD', BASE_DIR.'download/');
define('DIR_LOGS', BASE_DIR.'system/logs/');
define('HTTP_SERVER', BASE_URL);
define('HTTP_IMAGE', BASE_URL."image/");
// HTTPS
define('HTTPS_SERVER', BASE_URL_SSL);
define('HTTPS_IMAGE', BASE_URL_SSL."image/");

define('DIR_LANGUAGE', BASE_DIR.'catalog/language/');

if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    define('DIR_TEMPLATE', 'catalog/view/theme/');
} else {
    define('DIR_TEMPLATE', 'catalog/view/theme/');
}

define('DIR_UPLOAD', BASE_DIR.'upload/');

function getBaseUrl()
{
    // output: /myproject/index.php
    $currentPath = $_SERVER['PHP_SELF'];

    // output: Array ( [dirname] => /myproject [basename] => index.php [extension] => php [filename] => index )
    $pathInfo = pathinfo($currentPath);

    // output: localhost
    $hostName = $_SERVER['HTTP_HOST'];

    // output: http://
    if($_SERVER["HTTPS"] == 1)
        $protocol = "https://";
    else
    $protocol = 'http://';

    // return: http://localhost/myproject/
    return $protocol.$hostName.$pathInfo['dirname']."";
}

