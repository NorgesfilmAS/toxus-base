<?php

class JsonGenerate extends CComponent 
{
	/**
	 * convert a record definition into an array
	 * 
	 * 
	 * @param CMap or CRecord $data
	 * @param array $format
	 * @return array
	 */
	public function run($data, $format)
	{
		$result = array();
		// data can be an object CActiveRecord or array of CActiveRecord
		if (is_object($data)) {
			$useIndex = false;
			$data = array($data);
		} else {	
			$useIndex = true;
		}
		foreach ($data as $index => $record) {			
			if ($useIndex) {
				$result[$index] = array();
			}
			foreach ($format as $key => $field) {
				
				if (is_numeric($key)) {			// 3 => 'name' or 3 => array(...)
					if (is_string($field)) {		// 3 => fieldnam
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
						} else {
							Yii::log('Unknown field: '.$field, CLogger::LEVEL_ERROR, 'toxus.json.generate');
						}
					} elseif (is_array($field)) { // user => array('id', 'username')
						if (isset($record->$key)) {
							$keyName = $key;
							$value = $this->run($record->$key, $field);
						} else {
							Yii::log('Unknown relation: '.$field, CLogger::LEVEL_ERROR, 'toxus.json.generate');
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
					$result[$index][$keyName] = $value;
				} else {
					$result[$keyName] = $value;
				}
			}	
		}
		return $result;
	}	
}