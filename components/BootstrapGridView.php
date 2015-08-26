<?php

Yii::import('zii.widgets.grid.CGridView');
class BootstrapGridView extends CGridView
{
	public $updateLink = null;
	
	public function init() {
		parent::init();
		$this->htmlOptions = array_merge(
				$this->htmlOptions, 
				array(
					'class' => 'table table-striped table-hover',
				)		
		);		
		if ($this->updateLink !== null) {
			$this->selectionChanged = "function(id){window.location='" . Yii::app()->urlManager->createUrl($this->updateLink, array('id'=>'')) . "' + $.fn.yiiGridView.getSelection(id);}";
			$this->htmlOptions['style'] = 'cursor: pointer;';
		}	
		
//		$this->selectableRows = 1;	
		$this->pagerCssClass = 'pagination-left';
		$this->template = '{items} {pager} {summary}';
		$this->pager = array_merge(
						$this->pager,
						array(
							'class'					 => 'BootstrapLinkPager',	
							'header'         => '', //<div class="pagination pagination-left">',
							'footer'				 => '', // </div>',	
							'selectedPageCssClass' => 'active',	
							'htmlOptions'		 =>	array('class' => 'pagination pagination-sm tox-pager-grid'),
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
