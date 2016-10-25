<?php

class JsonGenerate extends CComponent 
{
	/**
	 * To prevent the overload version of run 
	 * @param type $data
	 * @param type $format
	 */
	protected function run($data, $format)
	{
    ini_set('max_execution_time', 3000);
		return $this->runInternal($data, $format);
	}
	/**
	 * convert a record definition into an array
	 * 
	 * 
	 * @param CMap or CRecord $data
	 * @param array $format
	 * @return array
	 */
	public function runInternal($data, $format)
	{
		$result = array();
		// data can be an object CActiveRecord or array of CActiveRecord
		if (is_object($data)) {
			$useIndex = false;
			$data = array($data);
		} elseif (empty($data)) {	
			return false;
		} else {	
			$useIndex = true;
		}

		foreach ($data as $index => $record) {			
			if ($useIndex) {
				$result[$index] = array();
			}
			foreach ($format as $key => $field) {

				if (is_numeric($key)) {			// 3 => 'name' or 3 => array(...)
					if (is_string($field)) {		// 3 => fieldname
						$keyName = $field; 
						if (isset($record->$field)) {
							$value = $record->$field;														
						} else {
							Yii::log('Field is unknown of null: '.$field, CLogger::LEVEL_INFO, 'toxus.json.generate');
							$value = '';
						}
					} elseif (is_array($field)) {		// what would that mean??
						Yii::log('Unknown definition: [number] => array(..)', CLogger::LEVEL_ERROR, 'toxus.json.generate');
					} else {
						Yii::log('Unknown definition: '.$field, CLogger::LEVEL_ERROR, 'toxus.json.generate');
					} 
				} elseif (is_string($key)) {  
					if (is_string($field)) { // 'is_temp' => 'isTemp'
						if (isset($record->$key)) {
							$value = $record->$key;
							$keyName = $field;
            } elseif (strpos($key, '.')) { // it's field using a relation
              $rel = explode('.', $key);
              if (isset($record->$rel[0])) {
                $data = $record->$rel[0];
                if (!empty($data)) {
                  if (isset($data->{$rel[1]} )) {
                    $value = $data->{$rel[1]};
                    $keyName = $field;
                  }
                } else {
                  Yii::log('Empty relation: '.$rel[0], CLogger::LEVEL_ERROR, 'toxus.json.generate');
                }
              } else {
                Yii::log('Unknown relation: '.$rel[0], CLogger::LEVEL_ERROR, 'toxus.json.generate');
              }
						} else  {
							Yii::log('Unknown field: '.$field, CLogger::LEVEL_ERROR, 'toxus.json.generate');
						}
					} elseif (is_array($field) || strpos($key,':' ) > 0) { // user => array('id', 'username')
            if (strpos($key,':') > 0) { // rename the relation
              $split = explode(':', $key);
              if (isset($record->{$split[0]})) {
                $keyName = $split[1];
                if (($value = $this->runInternal($record->{$split[0]}, $field)) === false) {
                  continue; // skip the processing of this one                
                }
              }
            } elseif (isset($record->$key)) {
							$keyName = $key;
							if (($value = $this->runInternal($record->$key, $field)) === false) {
								continue; // skip the processing of this one
							};
						} else {
							Yii::log('Unknown relation: '.(is_string($field) ? $field : var_export($field, true)), CLogger::LEVEL_ERROR, 'toxus.json.generate');
						} 
					} else {
						Yii::log('Unknown key type: '.$key, CLogger::LEVEL_ERROR, 'toxus.json.generate');
					}		
				}
				if (is_numeric($value)) {
					$value = $value+0;
				} elseif (is_bool($value)) {
					$value = $value ? 1 : 0;
				}
//				if (!$value) $value = 0;
				if ($useIndex) {
          if ($keyName) {
            $result[$index][$keyName] = $value;
          } else {
            $result[$index] = $value;
          }
				} else {
					$result[$keyName] = $value;
				}
			}	
		}
		return $result;
	}	
}