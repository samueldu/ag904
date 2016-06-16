<?php
session_start();

if($ambient == "local")
{
    $base_url_admin = "http://localhost/ag904/core/admin/";
    $base_core = 'H:\samuel\xamp\htdocs\ag904\core\\';
    //$base_core = 'H:/samuel/xamp/htdocs/ag904/core/';
    $host = "localhost";
    $user = "root";
    $pass = "";
    $base = "ag904";
}
else
{
    $base_url_admin = "http://core.agenciahost.com.br/admin/";
    $base_core = '/dados/www/core/';
    $host = "localhost";
    $user = "root";
    $pass = "agencia904";
    $base = "ag904";
}

if(substr_count($base_url_admin,getBaseUrl())) {
    define('RODANDO_NO_CORE', '1');
}
else {
    define('RODANDO_NO_CORE', '0');
}

$base_core = str_replace("{base_url}", $_SERVER['HTTP_HOST'], $base_core);
$base_url_admin = str_replace("{base_url}", $_SERVER['HTTP_HOST'], $base_url_admin);

define('DB_DRIVER_CORE', 'mysqli');
define('DB_HOSTNAME_CORE', $host);
define('DB_USERNAME_CORE', $user);
define('DB_PASSWORD_CORE', $pass);
define('DB_DATABASE_CORE', $base);
define('DB_PREFIX_CORE', 'oc_');
define('DB_PORT_CORE', '3306');

define('BASE_CORE',$base_core);
define('BASE_STRUCTURE',BASE_CORE.'base');
define('AMBIENT',$ambient);

define('DIR_SYSTEM', BASE_CORE.'system/');
define('DIR_DATABASE', BASE_CORE.'system/database/');
define('DIR_CONFIG', BASE_CORE.'system/config/');
/*define('DIR_MODIFICATION', BASE_CORE.'system/modification/');*/

define('DIR_APPLICATION', BASE_CORE.'admin/');
define('DIR_LANGUAGE', BASE_CORE.'admin/language/');
define('DIR_TEMPLATE', BASE_CORE.'admin/view/template/');
define('DIR_LANGUAGE_CORE', BASE_CORE.'admin/language/');
define('DIR_IMAGE_CORE', BASE_CORE.'image/');
define('HTTP_CORE', $base_url_admin);

function getBaseUrl()
{
    // output: /myproject/index.php
    $currentPath = $_SERVER['PHP_SELF'];

    // output: Array ( [dirname] => /myproject [basename] => index.php [extension] => php [filename] => index )
    $pathInfo = pathinfo($currentPath);

    // output: localhost
    $hostName = $_SERVER['HTTP_HOST'];

    // output: http://
    $protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,5))=='https://'?'https://':'http://';

    // return: http://localhost/myproject/
    return $protocol.$hostName.$pathInfo['dirname']."";
}



/*
define('DIR_CACHE', BASE_DIR.'system/cache/');
define('DIR_DOWNLOAD', BASE_DIR.'download/');
define('DIR_LOGS', BASE_DIR.'system/logs/');
define('HTTP_SERVER', BASE_URL);
define('HTTP_IMAGE', BASE_URL."image/");
define('HTTPS_SERVER', BASE_URL);
define('DIR_UPLOAD', BASE_DIR.'upload/');
define('DIR_MODIFICATION', BASE_DIR.'system/modification/');
define('DIR_LANGUAGE', BASE_DIR.'catalog/language/');
define('DIR_TEMPLATE', BASE_DIR.'catalog/view/theme/');
define('DIR_IMAGE', BASE_DIR.'image/');
*/