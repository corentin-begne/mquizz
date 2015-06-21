<?
	class partial{

		public static function get($partial){
			if($partial[0] === '/'){
				$pathPartialIncluded = requireCore::$basePath.'/apps/'.APP.'/module/template/partial/'.substr($partial, 1);
			}else{
				$module = substr($partial, 0, strpos($partial, '/'));
				$path = substr($partial, strpos($partial, '/')+1);
				$pathPartialIncluded = requireCore::$basePath.'/apps/'.APP.'/module/'.$module.'/view/partial/'.$path;
			}
			// get all data from action
			foreach(module::$data as $name => &$value){
				${$name} = $value;
			}
			include($pathPartialIncluded.'.php');
		}
		public static function includeCore($partial){
			echo self::get($partial);
		}

		public static function getCore($partial){
			ob_start();
			self::get($partial);
			$content = ob_get_contents();
			ob_end_clean();
			return $content;
		}
	}
?>