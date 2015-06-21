<?
	/**
	 * @class compileLessTask
	 * @description compileLess task
	 */
	class compileLessTask{
		public static function execute($param=null){
			$paths = array("css", "admin/css");
            foreach($paths as $path){
                /** generate css */
                echo cli::success("complation starting...")."\n";
                $files = requireCore::globRecursive(requireCore::$basePath.'/web/'.$path.'/main.less');
                foreach($files as $file)
                {
                    $targetFile = str_replace('.less', '.css', $file);
                    // compile and compress css
                    exec('lessc -sm=on -x '.$file.' '.$targetFile);
                    echo $targetFile."\n";
                }
                echo cli::success("minify css $path finished")."\n";
            }
		}
	}
?>