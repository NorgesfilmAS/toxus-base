<?php

/**
 * Description of FillerWords
 *
 * @author jaap
 */
class FillerWords extends CComponent {
  // used: http://opac.zebi.nl/webopac/help/stopwoorden.htm
  public $defaultLanguage = 'en';
  
  private $_en = array(
    'a', 'about', 'after', 'all', 'als', 'an', 'and', 'as', 'at',
    'by','bij',
    'ca',
    'can',
    'dr',
    'ed','en','et',
    'for', 'from',
    'given',
    'his', 'how',
    'into', 'is', 'its','in',
    'met',
    'nabij','near','niet','not',
    'on','other', 'our', 'over','of','oorspr', 'or','ou',
    'prof', 'publ','pas','pres',
    'rev',
    'sept', 'some',
    'th', 'the', 'their','to','toward',
    'under', 'us','used',
    'vert','van','voortgez','voortz',
    'what','with',
    'your',
  );
  public function getEn() {
    return $this->_en;
  }
  
  public function languageExists($language) {
    return in_array($language, array('en'));
  }
}
