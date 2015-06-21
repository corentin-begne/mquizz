<?
	class cli{
		const ERROR = 41;
		const WARNING = 43;
		const SUCCESS = 42;
		const GREEN = "0;32";
		const BLACK = "0;30";

		public static $options;

		public static function colorBackgroundString($string, $color){
			return "\033[".self::BLACK."m\033[".$color."m".$string."\033[0m"; 
		}

		public static function colorString($string, $color=self::GREEN){
			return "\033[".$color."m".$string."\033[0m"; 
		}

		public static function error($string){
			return self::colorBackgroundString($string, self::ERROR);
		}
		public static function success($string){
			return self::colorBackgroundString($string, self::SUCCESS);
		}
		public static function warning($string){
			return self::colorBackgroundString($string, self::WARNING);
		}

		public static function getOptions(&$args){
			self::$options = array();
			foreach($args as $index => &$arg){
				if(strpos($arg, '--') === 0){
					$arg = substr($arg, 2);					
					list($name, $value) = explode('=', $arg);
					self::$options[$name] = $value;
					unset($args[$index]);
				}
			}
			self::$options['env'] = isset(self::$options['env']) ? self::$options['env'] :"dev";
			self::$options['app'] = isset(self::$options['app']) ? self::$options['app'] :"frontend";
		}
	}
?>