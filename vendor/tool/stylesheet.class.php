<?
	class stylesheet{
		const BASEPATH = 'css';

		public static function includeCore(){
			$name = (isset(module::$config['template'])) ? module::$config['template'] : route::$action;
			if(isset(module::$config[$name]["stylesheet"])){
				foreach(module::$config[$name]["stylesheet"] as $css){
					echo self::get($css);
				}
			}
		}
		public static function get($css){
			return '<link href="'.route::$basePath.'/'.self::BASEPATH.'/'.$css.'" rel="stylesheet" type="text/css" >';
		}
	}
?>