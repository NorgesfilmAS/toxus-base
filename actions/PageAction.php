<?php

/**
 * send a page to the user
 */

class PageAction extends CAction
{
  /**
   * the alias to the place where the pages are located
   * 
   * @var string
   */
  public $pageRoot = 'site.pages';
  
  public function run($pagename)
  {
    $this->controller->render($pagename);
  }
}