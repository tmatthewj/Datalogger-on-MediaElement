<?php
/**
 * Created by PhpStorm.
 * User: Matthew John
 * Date: 5/21/14
 * Time: 7:00 PM
 */

define('DEVELOPMENT', 'localhost');
define('PRODUCTION', 'vdohive.com');

switch ($_SERVER['SERVER_NAME']) {
    case DEVELOPMENT:
        // development server
        $config['mysql_server'] = "localhost";
        $config['mysql_user']   = "user";
        $config['mysql_pwd']    = "user";
        $config['mysql_db_name'] = "vp";

        $config['couchbase_server'] = "localhost";
        $config['couchbase_user']   = "user";
        $config['couchbase_pwd']    = "user00";
        $config['couchbase_port']   = "8091";
		
		$config['can_log']			= true;
        break;

    default:
        // live server
        $config['mysql_server'] = "localhost";
        $config['mysql_user']   = "root";
        $config['mysql_pwd']    = "beehive";
        $config['mysql_db_name'] = "vdohive";

        $config['couchbase_server'] = "localhost";
        $config['couchbase_user']   = "vdoadmin";
        $config['couchbase_pwd']    = "beehive";
        $config['couchbase_port']   = "8091";

		$config['can_log']			= false;
        break;
}

?>