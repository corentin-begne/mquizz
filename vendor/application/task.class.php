<?
	class task{

		public static function __callStatic($name, $arguments){
			call_user_func_array(array($name.'Task', "execute"), $arguments);
		}
	}
?>