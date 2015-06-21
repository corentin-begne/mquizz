<?
    class form{
        public static $schema;
        public static $clause;
        public static $header = true;
        public static $template = true;
        public static $foundRows;

        public static function getSchema(){
            if(!isset(self::$schema)){
                self::$schema = yaml_parse_file(requireCore::$basePath."/config/schema.yml");
            }
        }

        public static function generate($table, $clause=null, $restricts=[]){
            self::getSchema();
            self::$clause = $clause;
            $desc = self::$schema[$table];
            $constraints = array();
            /** get foreign values */
            if(isset($desc['constraints'])){
                foreach($desc['constraints'] as &$constraint){
                    $constraintClause = (isset($restricts[$constraint['local']])) ? $restricts[$constraint['local']] : null;
                    $rows = $constraint['table']::findAll(null, $constraintClause);
                    $constraints[$constraint['local']] = array();
                    foreach($rows as &$row){
                        $constraints[$constraint['local']][$row[$constraint['foreign']]] = $row['name'];
                    }
                }
            }
            $data = null;
            if(isset($clause['data'])){
                $data = $clause['data'];
                unset($clause['data']);
            }
            $content = '';
            if(self::$header){
                $content = '<div class="formContainer">';            
                $content .= self::createHeader($desc['columns']);
            }            
            $rows = $table::findAll($data, $clause);
            $content .= self::createContent($desc['columns'], $rows, $constraints);
            self::$foundRows = $table::getFoundRows();
            if(self::$template){
                $content .= self::createTemplate($desc['columns'], $constraints);
            }
            if(self::$header){
                $content .= '</div>';
            }
            return $content;
        }

        public static function createHeader($columns){
            $content = '<div class="headerForm">';
            $init = true;
            foreach($columns as $name => &$column){
                if(self::isAllowed($name, $column)){
                    $content .= '<div id="'.$name.'">'.(($init) ? "<img class='add' src='".requireCore::$config['path']."/images/form/add.png' />" : '').str_replace('_id', '', $name).'</div>';
                    $init = false;
                }
            }
            return $content.'</div>';
        }
        public static function createContent($columns, &$rows, &$constraints){
            $content = '';
            foreach($rows as &$row){
                $content .= '<div class="row" id="'.$row['id'].'">';
                foreach($columns as $name => &$column){
                    if(self::isAllowed($name, $column)){
                        $value = $row[$name];
                        $ref='';
                        if(isset($column['Key']) && $column['Key'] === 'MUL'){
                            $value = $constraints[$name][$row[$name]];
                            $ref = "value='".$row[$name]."'";
                        }
                        $content .= '<div id="'.$name.'"'.$ref.'>'.$value.'</div>';
                    }
                }
                $content .= '</div>';
            }
            return $content;
        }
        public static function createTemplate($columns, $constraints){
            $content = '<div class="templateForm hide">';
            foreach($columns as $name => &$column){
                if(self::isAllowed($name, $column)){
                    $content .= '<div class="formColumn"><label for="'.$name.'">'.str_replace('_id', '', $name).' : </label>';
                    $key = isset($column['Key']) ? $column['Key'] : '';
                    switch($key){
                        case 'MUL' :
                            $content .= '<select id="'.$name.'" name="'.$name.'">';
                            foreach($constraints[$name] as $id => &$value){
                                $content .= '<option id="'.$id.'" value="'.$id.'">'.$value.'</option>';
                            }
                            $content .= '</select>';
                            break;
                        default :
                            $maxlength = '';
                            $length = null;                            
                            if(strpos($column['type'], '(') !== false){                                
                                $length = substr($column['type'], strpos($column['type'], '(')+1);
                                $length = (int)substr($length, 0 , strpos($length, ')'));
                                $maxlength = ' maxlength="'.$length.'"';
                                $column['type'] = substr($column['type'], 0, strpos($column['type'], '('));
                            }
                            $attr = 'name="'.$name.'" id="'.$name.'"';
                            $size = isset($length) ? ' size="'.$length.'"' : ' size="64"';
                            $type='';
                            $required = ($column['null']) ? '' : ' required';
                            if($column['type'] === 'int' || $column['type'] === 'tinyint'){
                                $max = ($column['type'] === 'tinyint') ? 1 : ((int)str_pad('1', $length, '0')-1);
                                $type = ' type="number" min="0" max="'.$max.'"';
                            } else
                            if($name === 'password'){
                                $type = ' rel="password"';
                            } else
                            if($name === 'email'){
                                $type = ' type="email"';
                            } else
                            if($column['type'] === 'varchar') {
                                $type = ' type="text"';
                            }
                            if($name === 'phone'){
                                $type .= ' pattern="\(?([0-9]{3})\)?([ .-]?)([0-9]{3})\2([0-9]{4})"';
                            } else
                            if($name === 'postal_code'){
                                $type .= ' pattern="^(([0-8][0-9])|(9[0-5]))[0-9]{3}$"';
                            }
                            $attr .= $maxlength.$type.$size.$required;
                            if(isset($type) && isset($length) && $length <= 64){
                                $content .= '<input '.$attr.'  />';
                            } else {
                                $content .= '<textarea '.$attr.' cols="63" rows="4"></textarea>';
                            }
                            break;
                    }
                    $content .= '</div>';
                }                
            }
            return $content.'</div>';
        }
        public static function isAllowed($name, $column){
            if(!in_array($column['type'], ['timestamp', 'blob']) && $name !== "id"){
                return true;
            }
            return false;
        }
    }
?>