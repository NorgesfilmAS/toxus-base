<?php
/**
 * general purpose actions for ajax forms
 * 
 * forms are default render by ajaxForm (overloadable)
 * view are default ajaxView, which show the form as an view
 * 
 */

class AjaxAction extends CAction
{
	/**
	 * find the base view in the url:
	 *   /site/courseIndex/1			=> Course
	 *   /site/courseView/12?a=x	=> Course
	 * 
	 * @return string
	 */
	protected function getView()
	{
		$elms = array_reverse(explode('/', Yii::app()->request->url));
		$className = get_class($this);
		$classKey = substr($className, 4, strlen($className) - 6 - 4);
		foreach ($elms as $key) {
			$p = strripos($key, $classKey);
			if ($p) {
				return ucfirst(substr($key, 0, ($p) ));
			}
		}
	}	
	
}