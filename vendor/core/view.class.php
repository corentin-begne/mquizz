<?
	class view{
		public static $basePath;

		public static function includeCore(){
			self::$basePath = requireCore::$basePath.'/apps/'.APP.'/module';
			// get all data from action
			foreach(module::$data as $name => &$value){
				${$name} = $value;
			}
			if(route::$layout != ""){
				ob_start();
				include(self::$basePath.'/'.route::$module.'/view/'.route::$action.'.php');
				$content = ob_get_contents();
				ob_end_clean();
				include(self::$basePath.'/template/layout/'.route::$layout.'.php');
			}else{
				include(self::$basePath.'/'.route::$module.'/view/'.route::$action.'.php');
			}

		}
	}
?>