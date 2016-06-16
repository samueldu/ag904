<?php
session_start();

if($ambient == "local")
{
    $base_url_admin = "http://localhost/ag904/core/admin/";
    $base_core = 'H:\samuel\xamp\htdocs\ag904\core\\';
    $host = "localhost";
    $user = "root";
    $pass = "";
    $base = "ag904";
}
else
{
    $base_url_admin = "https://core.agenciahost.com.br/admin/";
    $base_core = '/dados/www/core/';
    $host = "localhost";
    $user = "root";
    $pass = "agencia904";
    $base = "ag904";
}

define('DB_DRIVER_CORE', 'mysqli');
define('DB_HOSTNAME_CORE', $host);
define('DB_USERNAME_CORE', $user);
define('DB_PASSWORD_CORE', $pass);
define('DB_DATABASE_CORE', $base);
define('DB_PREFIX_CORE', 'oc_');
define('DB_PORT_CORE', '3306');

define('BASE_CORE',$base_core);

define('DIR_APPLICATION', BASE_CORE.'catalog/');
define('DIR_SYSTEM', BASE_CORE.'system/');
define('DIR_DATABASE', BASE_CORE.'system/database/');
define('DIR_CONFIG', BASE_CORE.'system/config/');
/*define('DIR_MODIFICATION', BASE_CORE.'system/modification/');*/
define('DIR_LANGUAGE_CORE', BASE_CORE.'catalog/language/');