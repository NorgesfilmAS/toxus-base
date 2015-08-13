<?php

class WordSplitter extends CComponent
{
	const MIN_WORD_LENGTH = 1;
	public $seperators = array('/','_','.',';','-','(',')','\'','\'','\\', '?','"',"\n","\r","\t",',',':','!','@','#',
						'”','’','‘','“','[',']',' ','–','„','…','`','´','»','«','−','—','�','©','*','=','>','&','+','•','·','יי','ð',
						'}','{','$','%','˜','¿','¾','±','®','¬','^','|','‚','˜');

	public $wordModelClass = 'SearchWord';
	public $wordRefClass = 'SearchRef';
	
	public function split($text)
	{
		$text = trim(mb_strtolower(Util::trimSpaces(str_replace($this->seperators, ' ', $text)), 'UTF-8'), ',');
		return explode(' ', $text);
	}
	
	/**
	 * split the text into words and return the ids of these words
	 * 
	 * 
	 * @param string $text
	 * @return array of Ids
	 * @throws CDbException if word can be save
	 */
	public function splitToWords($text, $wordModelClass = false, $addIfNotFound = true)
	{
		if ($wordModelClass == false) {
			$wordModelClass = $this->wordModelClass;
		}
		$words = $this->split($text);
		// convert the words to a word_id => text version
		$result = array();
		foreach ($words as $word) {
			if (strlen($word) >= self::MIN_WORD_LENGTH) {
				if (!in_array($word, $result)) { // only once
					$searchWord = $wordModelClass::model()->find(array(
						'condition' => 'word=:word',
						'params' => array('word' => $word)	
					));
					if (!$searchWord && $addIfNotFound) {		// add to the search words
						$searchWord = new $wordModelClass();
						$searchWord->word = $word;
						if (!$searchWord->save()) {
						  throw new CDbException('Error saving word: '.Util::errorToString($searchWord->errors));
						}
					}
					if ($searchWord) {
						$wordIds[$searchWord->id] = $searchWord;
					}	
				}	
			}	
		}
		return $wordIds;
	}	
	
	/**
	 * stores / update the word ref table
   * $refField is the fieldname for the 
   * WordRefClass is the name of the class to use to store the links in. Default 'SearchRef'
   * 
	 * 
	 * @param array $words a id => word array
	 */
	public function storeWords($words, $id, $refField, $wordRefClass = false, $extraFields = array())
	{
		if ($wordRefClass === false) {
			$wordRefClass = $this->wordRefClass;
		}
		if (!is_array($words)) return;
		$wordIds = array_keys($words);
		$wordRefs = $wordRefClass::model()->findAll($refField.'=:id',
			array(':id' => $id)	
		);
		$existing = array();
		foreach ($wordRefs as $wordRef) {
			$existing[$wordRef->word_id] = $wordRef;
		}
		// we have to arrays: $existing with the one that are there, $wordIds with the needed ones
		$newIds = array();
		foreach ($wordIds as $wordId) {	
			if (isset($existing[$wordId])) { // remove the existing ones
				unset($existing[$wordId]);
			} else {												// remember the new ones	
				$newIds[] = $wordId;
			}
		}
		// reuse the existing ones
		foreach ($newIds as $newId) {
			if (count($existing) == 0) {	break; } // nothing left to reuse
			$ref = array_shift($existing);
			$ref->word_id = $newId;
			$ref->save();
		}
		// remove all not reuse exsting ones
		foreach ($existing as $exist) {
			$exist->delete();
		}
		// store new ones
		foreach ($newIds as $newId) {
			$ref = new $wordRefClass();
			$ref->$refField = $id;
			$ref->word_id = $newId;
			$ref->save();
		}
	}
	
  /**
   * 
   * @param type $ids             array of SearchWord
   * @param type $docId           value of the doc
   * @param type $docFieldname    the name to store the docId in 
   * @params options            
   *    extraFields   - extrafield that should be set
   *    modelClass    - the name of the record class
   *    docFieldname  - the fieldname of the doc field
   *    
   */
	public function saveNewWords($ids, $docId, $options = array()) {
    $extraFields = isset($options['extraFields']) ? $options['extraFields'] : array();    
    $modelClass = isset($options['modelClass']) ? $options['modelClass'] : 'SearchRef';
    $docFieldname = isset($options['docFieldname']) ? $options['docFieldname'] : 'doc_id';
    
    if (is_array($ids)) {
      foreach ($ids as $searchWord) {
        $sr = new $modelClass();
        $sr->$docFieldname = $docId;
        $sr->word_id = $searchWord->id;
        foreach ($extraFields as $fieldname => $value) {
          $sr->$fieldname = $value;
        }
        if (!$sr->save()) {
          throw new CDbException('Can save '.$modelClass.': '.Util::errorToString($sr->errors));
        }
      }
    }
  }
  
}