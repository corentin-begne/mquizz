<?
	require_once(dirname(__FILE__).'/vendor/tool/cli.class.php');
	cli::getOptions($argv);
	define("ENV", cli::$options['env']);
    define("APP", cli::$options['app']);
	require_once(dirname(__FILE__)."/apps/".APP."/config/init.php");

	if(isset($argv[1])){
		if(strpos($argv[1], ':') !== false){
			list($action, $type) = explode(':', $argv[1]);
			if(class_exists($action)){
				array_splice($argv, 0, 2);
				try{
					call_user_func_array(array($action, $type), $argv);
				}catch(Exception $e){
					echo cli::error($e->getMessage())."\n";
				}
			}
		}
	}else{
		echo cli::error("Missing params")."\n";
	}
?>