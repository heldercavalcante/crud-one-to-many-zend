<?php
/**
 * public/index.php  
 */
// Define path to application directory  
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));
// Define application environment  
defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development')); // change to development now
// Ensure library/ is on include_path  
set_include_path(implode(PATH_SEPARATOR, array(  
    realpath(APPLICATION_PATH . '/../library'),  
    realpath(APPLICATION_PATH . '/models/DbTable'), // we will just call 'new Posts()'  
    get_include_path(),  
)));
/** Zend_Application */  
require_once 'Zend/Application.php';
// Create application, bootstrap, and run  
$application = new Zend_Application(  
    APPLICATION_ENV,  
    APPLICATION_PATH . '/configs/application.ini'  
);
//
defined('PUBLIC_PATH')
|| define('PUBLIC_PATH', realpath(dirname(__FILE__)));
defined('BASE_URL')
|| define('BASE_URL', "http://localhost/");
defined('UPLOAD_DIR')
|| define('UPLOAD_DIR', "public/image");
//
$application->bootstrap()->run();