<?php
/**
 * Interface lib to CouchDB.
 * 
 * a VERY thin layer over the couchdb
 * 
 * 
 * $couch = new CouchDB('address);
 * $couch->db->get('2e5');    // retrieve the record
 *
 * @author Toxus / Jaap van der Kreeft
 * @version 1.0 2015-10-8
 * 
 */
require_once(dirname(__FILE__).'/sag-master/src/Sag.php');


class CouchDB  extends CComponent {
  
  private $_sag=false;
  public $database = false;
  public $server = '127.0.0.1';
  public $port = '5984';
  public $username = null;
  public $password = null;
  public $loginType = false;
  
  
  public function init() {
    $this->loginType = Sag::$AUTH_BASIC;
  }
  
  public function __construct($database=false, $server=false, $port=false) {
    if ($database != false) {
      $this->database = $database;
    }
    if ($server != false) {
      $this->server = $server;
    }
    if ($port != false) {
      $this->port = $port;
    }
    
  }
  
  public function getDB() {
    if ($this->_sag === false) {
      $this->_sag = new Sag($this->server, $this->port);
      if ($this->_sag) {
        $this->_sag->setDatabase($this->database);
        // $this->_sag->login($this->username, $this->password, $this->loginType);
      }
    }
    return $this->_sag;
  }
  
  
}
