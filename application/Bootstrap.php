<?php
/**
* application/Bootstrap.php  
*/  
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap  
{  
    protected function _initDatabase()  
    {  
        $adapter = Zend_Db::factory('pdo_mysql', array(  
            'host' => 'mysql',  
            'username' => 'root',  
            'password' => '12345',  
            'dbname' => 'blog_zend',  
            'charset' => 'utf8'
            /**  
            * Some options  
            * 'port' => '3307',  
            *'unix_socket' => '/tmp/mysql2.sock'  
            */  
        ));  
        Zend_Db_Table_Abstract::setDefaultAdapter($adapter); // setting up the db adapter to DbTable  
    }
    
    public function _initAutoloader()  
    {  
        $loader = Zend_Loader_Autoloader::getInstance();  
        $loader->setFallbackAutoloader(true);  
    }
}