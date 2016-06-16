<?php
// Version
define('VERSION', '2.0.3.1');

if(	substr_count($_SERVER['HTTP_HOST'],"192.168") || substr_count($_SERVER['HTTP_HOST'],"no-ip") ||
    $_SERVER['HTTP_HOST'] == "200.175.198.99" ||
    $_SERVER['HTTP_HOST'] == "gwgroup.no-ip.info" ||
    $_SERVER['HTTP_HOST'] == "127.0.0.1" ||
    $_SERVER['HTTP_HOST'] == "gwgroup" ||
    $_SERVER['HTTP_HOST'] == "localhost")
{
    $ambient = "local";
    error_reporting(E_ALL);
    ini_set("display_errors", 1);
}
else
{
    $ambient = "publicado";
    error_reporting(E_ALL);
    ini_set("display_errors", 1);
}

if(	substr_count($_SERVER['REQUEST_URI'],"core"))
{
    if($ambient)
    set_include_path('/ag904/core/admin');
}
else
{
    set_include_path('/ag904/core/admin');
}

    // Configuration
	require_once('config_core.php');

// Install
if (!defined('DIR_APPLICATION')) {
	'erro na definição de DEFINERS';
	exit;
}

define('DIR_MODIFICATION', dirname($_SERVER["SCRIPT_FILENAME"]).'/../system/modification/');

//VirtualQMOD
require_once($base_core.'vqmod/vqmod.php');
VQMod::bootup();

// VQMODDED Startup
require_once(VQMod::modCheck(DIR_SYSTEM . 'startup.php'));

// Registry
$registry = new Registry();

// Config
$config = new Config();
$registry->set('config', $config);

// Configuration
require('config.php');

// Database (dbcore ja esta estanciado nesse momento)
$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
$registry->set('db', $db);

// Settings
$query = $db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE store_id = '0'");

foreach ($query->rows as $setting) {
	if (!$setting['serialized']) {
		$config->set($setting['key'], $setting['value']);
	} else {
		$config->set($setting['key'], unserialize($setting['value']));
	}
}

// Loader
$loader = new Loader($registry);
$registry->set('load', $loader);

// Url
$url = new Url(HTTP_SERVER, $config->get('config_secure') ? HTTPS_SERVER : HTTP_SERVER);
$registry->set('url', $url);

// Log
$log = new Log($config->get('config_error_filename'));
$registry->set('log', $log);

function error_handler($errno, $errstr, $errfile, $errline) {
	GLOBAL $log, $config;

	// error suppressed with @
	if (error_reporting() === 0) {
		return false;
	}

	switch ($errno) {
		case E_NOTICE:
		case E_USER_NOTICE:
			$error = 'Notice';
			break;
		case E_WARNING:
		case E_USER_WARNING:
			$error = 'Warning';
			break;
		case E_ERROR:
		case E_USER_ERROR:
			$error = 'Fatal Error';
			break;
		default:
			$error = 'Unknown';
			break;
	}


	if ($config->get('config_error_display') == 1) {
		echo '<b>' . $error . '</b>: ' . $errstr . ' in <b>' . $errfile . '</b> on line <b>' . $errline . '</b>';
	}

	if ($config->get('config_error_log') == 1) {
		$log->write('PHP ' . $error . ':  ' . $errstr . ' in ' . $errfile . ' on line ' . $errline);
	}

	return true;
}

// Error Handler
set_error_handler('error_handler');

// Request
$request = new Request();
$registry->set('request', $request);

// Response
$response = new Response();
$response->addHeader('Content-Type: text/html; charset=utf-8');
$response->addHeader("Access-Control-Allow-Origin: *");
$registry->set('response', $response);

// Cache
$cache = new Cache('file');
$registry->set('cache', $cache);

// Session
$session = new Session();
$registry->set('session', $session);

// Language
$languages = array();

$query = $db->query("SELECT * FROM `" . DB_PREFIX . "language`");

foreach ($query->rows as $result) {
	$languages[$result['code']] = $result;
}

$config->set('config_language_id', $languages[$config->get('config_admin_language')]['language_id']);

// Language
$language = new Language($languages[$config->get('config_admin_language')]['directory']);
$language->load($languages[$config->get('config_admin_language')]['directory']);
$registry->set('language', $language);

// Document
$registry->set('document', new Document());

// Currency
$registry->set('currency', new Currency($registry));

// Weight
$registry->set('weight', new Weight($registry));

// Length
$registry->set('length', new Length($registry));

// User
$registry->set('user', new User($registry));


//OpenBay Pro
$registry->set('openbay', new Openbay($registry));

// Event
$event = new Event($registry);
$registry->set('event', $event);

$query = $db->query("SELECT * FROM " . DB_PREFIX . "event");

foreach ($query->rows as $result) {
	$event->register($result['trigger'], $result['action']);
}

// Front Controller
$controller = new Front($registry);

// Login
$controller->addPreAction(new Action('common/login/check'));

// Permission
$controller->addPreAction(new Action('error/permission/check'));

// Router
if (isset($request->get['route'])) {
	$action = new Action($request->get['route']);
} else {
	$action = new Action('common/dashboard');
}

// Dispatch
$controller->dispatch($action, new Action('error/not_found'));

// Output
$response->output();