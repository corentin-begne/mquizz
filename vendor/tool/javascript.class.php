<?
	class javascript{
		const BASEPATH = 'js';

		public static function includeCore(){
			$name = (isset(module::$config['template'])) ? module::$config['template'] : route::$action;
			if(isset(module::$config[$name]["javascript"])){
				foreach(module::$config[$name]["javascript"] as $js){
					echo self::get($js);
				}
			}
		}

		public static function add($paths){
			$name = (isset(module::$config['template'])) ? module::$config['template'] : route::$action;
			foreach($paths as $path){
				if(!in_array($path, module::$config[$name]["javascript"])){
					array_splice(module::$config[$name]["javascript"], (count(module::$config[$name]["javascript"])-1), 0, $path);
				}
			}
		}

		public static function get($js){
			return '<script type="text/javascript" src="'.route::$basePath.'/'.self::BASEPATH.'/'.$js.'"></script>';
		}
	}
?>