<?php
// Version
define('VERSION', '0.2.0');

// Configuration
require_once('../admin/config.php');


// Additional configs
//require_once(DIR_CONFIG  . 'config_tuning.php');
// Startup
require_once(DIR_SYSTEM . 'startup.php');

// Application Classes
require_once(DIR_SYSTEM . 'library/cart/currency.php');
require_once(DIR_SYSTEM . 'library/cart/user.php');
require_once(DIR_SYSTEM . 'library/cart/weight.php');
require_once(DIR_SYSTEM . 'library/cart/length.php');

// Registry
$registry = new Registry();

// Loader
$loader = new Loader($registry);
$registry->set('load', $loader);

// Config
$config = new Config();
$registry->set('config', $config);

// Database
$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
$registry->set('db', $db);

// Settings
$query = $db->query("SELECT * FROM " . DB_PREFIX . "setting");

foreach ($query->rows as $setting) {
	$config->set($setting['key'], $setting['value']);
}


// Log
$log = new Log($config->get('config_error_filename'));
$registry->set('log', $log);

// Request
$request = new Request();
$registry->set('request', $request);

// Response
$response = new Response();
$response->addHeader('Content-Type: text/html; charset=utf-8');
$registry->set('response', $response);

// Session
$registry->set('session', new Session());

// Cache
$registry->set('cache', new Cache('file'));

// Document
$registry->set('document', new Document());

// Language
$languages = array();

$query = $db->query("SELECT * FROM " . DB_PREFIX . "language");

foreach ($query->rows as $result) {
	$languages[$result['code']] = $result;
}

$config->set('config_language_id', $languages[$config->get('config_admin_language')]['language_id']);

$language = new Language($languages[$config->get('config_admin_language')]['directory']);
$language->load('default');
$registry->set('language', $language);

// Currency
$registry->set('currency', new \Cart\Currency($registry));

// Weight
$registry->set('weight', new \Cart\Weight($registry));

// Length
$registry->set('length', new \Cart\Length($registry));

// User
$registry->set('user', new \Cart\User($registry));

// Event
$event = new Event($registry);
$registry->set('event', $event);

$query = $db->query("SELECT * FROM " . DB_PREFIX . "event");

foreach ($query->rows as $result) {
	$event->register($result['trigger'], new Action($result['action']));
}

function debug($data){
	echo '<pre>'.print_r($data, true).'</pre>';exit;
}

?>