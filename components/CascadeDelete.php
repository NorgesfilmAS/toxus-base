<?php

class CascadeDelete extends CComponent
{
	/** 
	 * the relation to test for delete
	 * @var array
	 */
	public $relations = array();
	
	public function run($record, $relations=null)
	{
		if ($relations === null)
			$relations = $this->relations;
		$localTransaction = $record->dbConnection->getCurrentTransaction() === null 
					? $record->dbConnection->beginTransaction()
					:	false;

		foreach ($relations as $relation) {
			try {
				// this is what should run
				foreach ($record->$relation as $child) {
					if (!$child->delete()) {
						$record->addErrors($child->errors);
						if ($localTransaction) {
							$localTransaction->rollback();
						}	
						return false;						
					}
			  }	
				if ($localTransaction)
					$localTransaction->commit();
				return true;
			} catch (Exception $e) {
				$record->addError('general', $e->message);
				throw $e;
			}						
		}
	}
}