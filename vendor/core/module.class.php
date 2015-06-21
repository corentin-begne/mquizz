<?
	class module{
		public static $config;
		public static $data;

		public static function get(){
			$data = null;			
			self::$config = yaml_parse_file(requireCore::$basePath.'/apps/'.APP.'/module/'.route::$module.'/config.yml');
			$class = route::$module.'Action';			
			$action = route::$action;
			if(class_exists($class)){
				self::$data = new $class;
				self::includePostData();
				if(method_exists(self::$data, "hook")){
					self::$data->hook();
				}
				if(method_exists(self::$data, $action)){
					$data = self::$data->$action();
				}
			}
			return $data;	
		}

		public static function includePostData(){
			foreach($_POST as $name => $value){
				self::$data->$name = $value;
			}
		}
	}
?>