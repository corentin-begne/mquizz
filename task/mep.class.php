<?
    /**
     * @class mepTask
     * @description mep task
     */
    class mepTask{

        public static function execute($tag=null){
            
            if(!isset($tag)){
                die(cli::error('Tag missing'));
            }
            if(exec("cd ".requireCore::$basePath.";git branch --list $tag") !== ''){
                die(cli::error('Tag already exists'));
            }
            // create tag branch
            exec('cd '.requireCore::$basePath.";git checkout prod;git checkout -b $tag");
            // recompile less
            $paths = ['css'=>'http://kapeco.bennette.info', 'admin/css'=>'http://kapecoadmin.bennette.info'];
            foreach($paths as $path => $link){
                /** generate css */
                cli::success('minify css starting...')."\n";
                $files = requireCore::globRecursive(requireCore::$basePath.'/web/'.$path.'/main.less');
                foreach($files as $file)
                {
                    $targetFile = str_replace('.less', '.css', $file);
                    // compile and compress css
                    exec('lessc -s -sm=on -x --url-args="releaseDate='.time().'" --global-var=\'basepath="'.$link.'"\' '.$file.' '.$targetFile);
                    echo $targetFile."\n";
                }
                cli::success("minify css $path finished")."\n";
                /** generate minified js */
                cli::success('minify/uglify js starting...')."\n";
                $files = requireCore::globRecursive(requireCore::$basePath.'/web/'.str_replace('css', 'js', $path).'/*.js');
                foreach($files as $file)
                {
                    if(strpos($file, '/lib/ace/') !== false || strpos($file, '/lib/ckeditor/') !== false){
                        continue;
                    }
                    exec('uglifyjs '.$file.' -o '.$file.' -c');
                    echo $file."\n";
                }
                cli::success("minify/uglify js $path finished")."\n";
            }
            // commit and push
            exec("cd ".requireCore::$basePath.";git add -A;git commit -am \"relase\";git push origin $tag");
            /** upload files */
            exec('scp -r '.requireCore::$basePath.'/* bennette@ftp.bennette.info:www/kapeco/');
            // switch to master
            exec("cd ".requireCore::$basePath.";git checkout master;git branch -D 0.0.1;git push origin :0.0.1");
        }
    }
?>