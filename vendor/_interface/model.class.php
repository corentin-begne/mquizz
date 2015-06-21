<?
	abstract class model {
		public static $statement;

		public static function getTableName(){
			return get_called_class();
		}

		public static function getLastInsertId(){
			return (int)pdoManager::$pdoHandle->lastInsertId() ;
		}

		public static function getFoundRows(){
			self::prepare('select found_rows() as nb');
			self::execute();
			$row = pdoManager::$statement->fetch();
			return (int)$row['nb'] ;
		}

		public static function prepare($query){
			pdoManager::prepare($query);        	
		}

		public static function execute($params=null){
			pdoManager::$statement->execute($params);
		}

		public static function insert($params, $type="insert"){
			if(strpos($type, 'insert') !== false && strpos(self::getTableName(), 'sf_guard_user') !== false){
				$time = date('Y/m/d H:i:s');
				$params['created_at'] = $time;
				$params['updated_at'] = $time;
			}
			foreach($params as &$param){
				if(is_array($param)){
					$param = json_encode($param);
				}
			}
 			$fields = implode(',', array_keys($params));
        	$values = implode(',:', array_keys($params));
        	self::prepare("
				$type into 
					".self::getTableName()." 
				($fields)
				    values
				(:$values)
			");
			self::execute($params);
		}

		public static function delete($params=null, $where=null){
			$where = !isset($where) ? array_keys($params) : $where;
			self::prepare("
				delete from
					".self::getTableName()."
				".(isset($where) ? " where ".self::setWhereFields($params, $where) : '')	
			);
			self::execute($params);
		}

		public static function replace($params){
			self::insert($params, "replace");
		}

		public static function update($params, $fields, $where=null){
			foreach($params as &$param){
				if(is_array($param)){
					$param = json_encode($param);
				}
			}
			self::prepare("
				update ".self::getTableName()." 
				set ".self::setUpdateFields($fields)." 
				".(isset($where) ? " where ".self::setWhereFields($params, $where) : '')
			);
			self::execute($params);
		}

		public static function findOne($params=null, $clause=null){
			self::find($params, $clause);
			return pdoManager::$statement->fetch();
		}

		public static function findAll($params=null, $clause=null){
			self::find($params, $clause);
			$rows = pdoManager::$statement->fetchAll();
			self::checkCount($clause);
			return $rows;
		}

		public static function findOneWhereAll($params=null, $clause=null){
			$clause['where'] = array_keys($params);
			self::find($params, $clause);
			return pdoManager::$statement->fetch();
		}

		public static function findAllWhereAll($params=null, $clause=null){
			$clause['where'] = array_keys($params);
			self::find($params, $clause); 
			$rows = pdoManager::$statement->fetchAll();
			self::checkCount($clause);
			return $rows;
		}

		public static function find($params=null, $clause=null){
			if(isset($clause['where']) && count($clause['where']) === 0){
				unset($clause['where']);
			}
			self::prepare("
				select ".self::getFields($clause['fields'])." 
				from ".self::getTableName().(isset($clause['alias']) ? " ".$clause['alias'] : '')." 
				".(isset($clause['tables']) ? ", ".$clause['tables'] : '')."
				".(isset($clause['where']) ? " where ".self::setWhereFields($params, $clause['where']) : '')."
				".(isset($clause['group']) ? " group by ".$clause['group'] : '')."
				".(isset($clause['order']) ? " order by ".$clause['order'] : '')."
				".(isset($clause['limit']) ? " limit ".$clause['limit'][0].', '.$clause['limit'][1] : '')
			);
			self::execute($params);			
		}

		public static function checkCount(&$clause){
			if(isset($clause['fields']) && stripos($clause['fields'], 'SQL_CALC_FOUND_ROWS') !== false){
				rest::$count = self::getFoundRows();
				if(isset($clause['limit'])){
					rest::$nbPage = ceil(rest::$count/(int)$clause['limit'][1]);
				}
			}
		}

		public static function getFields(&$fields){
			return (isset($fields)) ? $fields : '*';
		}

    	public static function setUpdateFields(&$fields){
    		$data = '';
    		$nbFields = count($fields)-1;
			foreach($fields as $index => &$field){
				$last = ((int)$index === ($nbFields)) ? true : false;
				$data .= "$field = :$field ".(!$last ? ', ' : '');
			}
			return $data;
    	}

    	public static function setWhereFields(&$params, &$fields){
			$data = '';
			$nbFields = count($fields)-1;
			foreach($fields as $index => &$field){
				$last = ((int)$index === ($nbFields)) ? true : false;
				$data .= self::getFieldInfos($params, $field, $last);
			}
			return $data;
		}    	

		public static function getFieldInfos(&$params, $field, $last){						
			if(is_array($field)){	
				$name = $field['name'];							
				$operator = isset($field['type']) ? $field['type'] : '=' ;
				$separator = isset($field['link']) ? $field['link'] : 'and';
				$value = isset($field['expr']) ? $field['expr'] : ":".$field['name'];
				if($operator === 'in'){
					if(!isset($field['expr'])){
						$value = "('".str_replace(',', "','", $params[$field['name']])."')";
					}else{
						$value = '('.$value.')';
					}
				}					
			}else{
				$name = $field;
				$value = ":$field";
				$operator = '=';
				$separator = 'and';
			}
			return " $name $operator $value ".(!$last ? $separator.' ' : '');
		}

	}
?>