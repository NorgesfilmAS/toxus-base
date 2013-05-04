<?php

Yii::import('zii.widgets.grid.CGridView');
class BootstrapGridView extends CGridView
{
	public function init() {
		parent::init();
		$this->htmlOptions = array_merge(
				$this->htmlOptions, 
				array(
					'class' => 'table table-striped table-hover',
				)		
		);				
		$this->pagerCssClass = 'pagination pagination-left';
		$this->template = '{items} {pager} {summary}';
		$this->pager = array_merge(
						$this->pager,
						array(
							'class'					 => 'BootstrapLinkPager',	
							'header'         => '', //<div class="pagination pagination-left">',
							'footer'				 => '', // </div>',	
							'selectedPageCssClass' => 'active',	
							'htmlOptions'		 =>	array('class' => 'pager'),
							'hiddenPageCssClass' => 'hide-it',	
							'prevPageLabel'  => '&larr; ',
							'nextPageLabel'  => '&rarr;',	
							//'nextPageLabel'  => '<img src="images/pagination/right.png">',
							'lastPageLabel'  => null, //'&gt;&gt;',							
						)	
					);			
	}
	
}

class BootstrapLinkPager extends CLinkPager
{
	protected function createPageButtons()
	{
		$buttons = parent::createPageButtons();
		array_shift($buttons);					// remove the firstPage link
		$b = array_reverse($buttons);		// remove the last
		array_shift($b);
		return array_reverse($b);
	}		
}
