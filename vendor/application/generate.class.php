<?
	class generate{
		public static function schema(){
			$indents = array("  ", "    ");
			$schemaYml = requireCore::$basePath.'/config/schema.yml';
			$content = '';
			// save old schema
			if(file_exists($schemaYml)){
				exec("mv $schemaYml $schemaYml.BAK".time());
			}
			// get tables list
			pdoManager::prepare('show tables');
			pdoManager::execute();
			$tables = pdoManager::$statement->fetchAll(PDO::FETCH_NUM);			
			foreach($tables as &$table){
				$name = $table[0];				
				$content .= "$name:\n".
					$indents[0]."columns:\n";					
				/** get columns table desc */
				pdoManager::prepare("desc $name");
				pdoManager::execute();
				$fields = pdoManager::$statement->fetchAll();
				foreach($fields as &$field){
					$content .= $indents[1].$field['Field'].': ';
					$content .= '{type: '.$field['Type'];
					$content .= ', null: '.(($field['Null'] === 'NO') ? "false" : "true");
					$content .= (isset($field['Default'])) ? ', default: '.$field['Default'] : '';
					$content .= (isset($field['Extra'])) ? ', extra: '.$field['Extra'] : '';
					$content .= (isset($field['Key'])) ? ', Key: '.$field['Key'] : '';
					$content .= "}\n";

				}	
				/** get table relations desc */
				pdoManager::prepare("select
				    constraint_name,column_name,referenced_table_name,referenced_column_name
				from
				    information_schema.key_column_usage
				where
				    referenced_table_name is not null
				    and table_schema = '".pdoManager::$config["db"]."' 
				    and table_name = '$name'");
				pdoManager::execute();
				$constraints = pdoManager::$statement->fetchAll();
				if(count($constraints) > 0){
					$content .= $indents[0]."constraints:\n";
					foreach($constraints as $constraint){
						$content .= $indents[1].$constraint['constraint_name'].": ";
						$content .= '{table: '.$constraint['referenced_table_name'].', local: '.$constraint['column_name'].', foreign: '.$constraint['referenced_column_name']."}\n";
					}				
				}	
			}			
			file_put_contents($schemaYml, $content);
		}

		public static function module($module, $action='index'){
			$tabs = "\t\t";
			$rest = "\n\t".$tabs."rest::init();\n$tabs\t\n".$tabs."\trest::renderJson(['title'=>'Succès']);\n\n";
			$secure = "\n".$tabs."public function hook(){\n".$tabs."\tif(!userManager::isAuthentificated()){\n".$tabs.$tabs."route::redirectByName('index');\n$tabs\t}\n$tabs}\n\n";
			cli::$options['type'] = isset(cli::$options['type']) ? cli::$options['type'] : 'action';			
			cli::$options['secure'] = isset(cli::$options['secure']) ? cli::$options['secure'] : 'no';
			$fn = ((cli::$options['secure'] === 'yes') ? $secure : '')."\t\tpublic function $action(){\n".((cli::$options['type'] === 'rest') ? $rest : '')."$tabs}";
		    $targetPath = requireCore::$basePath.'/apps/'.APP.'/module/'.$module;
		    $sourcePath = dirname(__FILE__).'/template/php/module';
		    if(!file_exists($targetPath)){
				exec("mkdir -p $targetPath/view");
			}
			$path = $module.(($action !== 'index') ? '/'.$action : '');
		    $files = glob($sourcePath.'/*.*');
		    foreach($files as &$file){
		    	$name = basename($file);
		    	$targetFile = $targetPath.'/'.$name;
		    	$classAction = 'class '.$module.'Action{';
		    	if(!file_exists($targetFile)){
		    		if($name === 'config.yml' && cli::$options['type'] === 'rest'){
		    			continue;
		    		}
		    		$content = file_get_contents($file);
		    		$content = str_replace(['module/action', 'module', 'Module', 'defaultName', $classAction], [$path,$module, ucfirst($module), $action, $classAction."\n".$fn."\n"], $content);
		    		file_put_contents($targetFile, $content);
		    	} else {
		    		$content = file_get_contents($targetFile);
		    		switch($name){
		    			case 'action.class.php':
		    				if(strpos($content, 'function '.$action) === false){
		    					$content = str_replace($classAction, $classAction."\n\n".$fn, $content);
		    					file_put_contents($targetFile, $content);
		    				}
		    				break;
		    			case 'config.yml':
		    				if(cli::$options['type'] !== 'rest' && strpos($content, $action.':') === false){
		    					$tmp = file_get_contents($file);
		    					$tmp = str_replace(['module/action', 'defaultName'], [$path, $action], $tmp);
		    					file_put_contents($targetFile, $content."\n\n".$tmp);
		    				}
		    				break;
		    		}
		    	}
		    }
		    if(cli::$options['type'] === 'action' && !file_exists("$targetPath/view/$action.php")){
		    	exec("cp $sourcePath/view/index.php $targetPath/view/$action.php");
		    }
		    if(cli::$options['type'] === 'action'){
			    cli::$options['include'] = isset(cli::$options['include']) ? cli::$options['include'] : 'all';
		    	$cmd = str_replace('/', ' ', $path);
		    	switch(cli::$options['include']){
		    		case 'js':
		    			exec('php '.requireCore::$basePath.'/application.php generate:javascript '.$cmd.' --app='.APP);
		    			break;
		    		case 'css':
		    			exec('php '.requireCore::$basePath.'/application.php generate:stylesheet '.$cmd.' --app='.APP);
		    			break;	
		    		case 'all':
		    			exec('php '.requireCore::$basePath.'/application.php generate:javascript '.$cmd.' --app='.APP);
		    			exec('php '.requireCore::$basePath.'/application.php generate:stylesheet '.$cmd.' --app='.APP);
		    			break;
		    	}
		    }
		}

		public static function task($name){
		    $targetFile = requireCore::$basePath."/task/$name.class.php";
		    $sourcePathFile = dirname(__FILE__).'/template/php/task/template.class.php';
			if(!file_exists($targetFile)){
				$content = file_get_contents($sourcePathFile);
				$content = str_replace('template', $name, $content);
				file_put_contents($targetFile, $content);
				echo $targetFile."\n";
			}
		}

		public static function models(){
		    $targetPath = requireCore::$basePath."/model";
		    $sourcePathFile = dirname(__FILE__)."/template/php/model/template.class.php";
			$shemaYml = yaml_parse_file(requireCore::$basePath.'/config/schema.yml');	
		    foreach($shemaYml as $table => &$desc){
		      $targetFile = $targetPath.'/'.lcfirst($table).'.class.php';
		      if(!file_exists($targetFile)){
		        $content = file_get_contents($sourcePathFile);
		        $content = str_replace("Template", $table, $content);
		        file_put_contents($targetFile, $content);
		        echo $targetFile."\n";
		      }
		    }
		}

		public static function javascript($module, $action=null){

		    $path = "/".$module.(isset($action) ? "/".$action : "");
		    $name = isset($action) ? $action.ucfirst($module) : $module;
		    $tags = array("template", "Template", "path");
    		$replaces = array($name, ucfirst($name), trim($path, '/'));
		    $targetPath = requireCore::$basePath."/web/".requireCore::$config['webPath']."js".$path;
		    $sourcePath = dirname(__FILE__)."/template/js";
		    if(!file_exists($targetPath)){
				exec("mkdir -p $targetPath");
			}
		    $files = glob($sourcePath.'/*.js');
		    foreach($files as &$file){
		      $targetFile = $targetPath."/".basename($file);
		      if(!file_exists($targetFile)){		        
		        if(!isset($action) || basename($file) !== "action.js"){
		        	$content = file_get_contents($file);
		        	$content = str_replace($tags, $replaces, $content);
		        	file_put_contents($targetFile, $content);
		        }	
		      }
		    } 
		}

		public static function stylesheet($module, $action=null){
		    $path = "/".$module.(isset($action) ? "/".$action : "");
		    $replace = isset($action) ? true : false;
		    $targetPath = requireCore::$basePath."/web/".requireCore::$config['webPath']."css".$path;
		    $sourcePath = dirname(__FILE__)."/template/css";
		    if(!file_exists($targetPath)){
				exec("mkdir -p $targetPath");
			}
		    $files = glob($sourcePath.'/*.less');
		    foreach($files as &$file){
		      $targetFile = $targetPath."/".basename($file);
		      if(!file_exists($targetFile)){
		        $content = file_get_contents($file);
		        if($replace){
		        	$content = str_replace("../", "../../", $content);
		        }
		        file_put_contents($targetFile, $content);
		      }
		    } 
		}
	}
?>