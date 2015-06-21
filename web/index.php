<?
    define("ENV", "dev");
    define("APP", "frontend");
	require_once(dirname(__FILE__)."/../apps/".APP."/config/init.php");
	route::check();
?>