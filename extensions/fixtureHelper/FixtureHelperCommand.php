<?php
/**
 * FixtureHelper is a command application lets you work with your fixtures outside 
 * testing. Currently what it does is just helping you to load you fixtures from your
 * fixture files to your database, without the need to invoke PHPUnit.
 * 
 * @author Sum-Wai Low
 * @link https://github.com/sumwai/fixtureHelper
 * @copyright Copyright &copy; 2010 Sum-Wai Low
 */
class FixtureHelperCommand extends CConsoleCommand {
	private $fixture;
	function getHelp() {
		return <<<EOD
USAGE
  fixture [load]
	
DESCRIPTION
  This command lets you work with your fixtures outside testing
	
PARAMETERS
  * load: Load fixtures into the database
  *   --alias: The alias to the directory that contains "models" and "tests" 
      folders. Please note that folder "models" should contain the Model class of 
      the fixtures to be loaded. Defaults to "application". Optional for "load".
  *   --tables: Name of the tables to be loaded with your defined fixtures. Name  
      values are comma separated. Required for "load".  
	
  * generate: Generates the fixture file from the db
	*   --table: the table to convert to a php script
	
  * db: explains what db is being used
	
EXAMPLES
  yiic fixture load --alias=application.modules.mymodule --tables=fruit,transport,country
  yiic fixture generate --table=note	


EOD;
	}
	
	
	/**
	 * function to confirm what we are doing
	 */
	public function confirmYes($ask)
	{
		echo $ask."\n";
		echo "Would you like to continue [Yes|No]? ";
		if (strncasecmp(trim(fgets(STDIN)), 'y', 1) !== 0) {
			exit;
		}
	}
	/**
	 * Loads fixtures into the database tables from fixtures.
	 * @param string $alias alias to the path that contains both models and tests folders
	 * @param string $tables comma separated value of table names that should be loaded with fixtures
	 */
	function actionLoad($tables = '', $alias='application'){

		Yii::import($alias.'.models.*');
		$this->fixture = Yii::app()->getComponent('fixture');
		$this->fixture->basePath = Yii::getPathOfAlias($alias.'.tests.fixtures');
		$this->fixture->init();
		
		if ($tables != '') {
			$this->confirmYes('Loading tables: '.$tables);
		 	$tables = explode(',', $tables);
		} else {
			$this->confirmYes('Loading all fixtures');
			$files = CFileHelper::findFiles(Yii::getPathOfAlias($alias.'.tests.fixtures'), array('fileTypes'=> array('php'), 'level' => 0));			
			$tables = array();
			foreach($files as $file) {
				$p = pathinfo($file);
				$tables[] = $p['filename'];
			}
		}
		Yii::app()->db->createCommand('SET FOREIGN_KEY_CHECKS=0;')->execute();
		$db = $this->fixture->getDbConnection();
		$tablesInDb = $db->schema->tables;
		foreach ($tables as $table) {
			if (isset($tablesInDb[$table])) {
				echo "clearing $table\n";				
				$this->fixture->resetTable($table);			
			}	else {
				echo "Skipped clearing $table, table does not exists.\n";
			}
		}
		foreach ($tables as $table) {
			try {
				if (isset($tablesInDb[$table])) {				
					echo "loading $table\n";
					$this->fixture->loadFixture($table);		
				}	
			} catch (Exception $e) {
				echo "ERROR: There is a problem working with the table $table. ".
					"Is it spelled correctly or exist?\n\n";
			}
		}
		Yii::app()->db->createCommand('SET FOREIGN_KEY_CHECKS=1;')->execute();

		echo "Done.\n\n";
	}
	
	public function actionGenerate($table, $alias='application')
	{
		
		$this->confirmYes('Generating fixture file');
		Yii::import($alias.'.models.*');
		$con = Yii::app()->db;
		$sql = 'SELECT * FROM '.$table;
		$cmd = $con->createCommand($sql);
		$dr = $cmd->query();
		
		$php = "<?php\n /*\n * autogenerate file\n */\n\n return array(\n";
		$id = 1;
		$dr->setFetchMode(PDO::FETCH_ASSOC);
		$rows = $dr->readAll();
		// list all the rows

		foreach ($rows as $row) {
			$php .= "    'r$id' => array(\n";
			foreach ($row as $name => $value) {
				if ($value === null) {
					$php .= "      '$name' => null,\n";				
				} elseif (is_numeric($value)) {
					$php .= "      '$name' => $value,\n";				
				} else {
					$php .= "      '$name' => \"".  str_replace('\\\'', '\'', addslashes($value))."\",\n";					
					// $php .= "      '$name' => ".$con->pdoInstance->quote($value).",\n";					
				}	
			}
			$php .= "    ),\n";
			$id += 1;
		} 
		$php .= "  );\n?>";
		
		// find a new name for this file
		$filename = Yii::getPathOfAlias($alias.'.tests.fixtures').'/'.$table.'.php';
		if (file_exists($filename)) {	// the file exists
			$id = 1;
			$s = Yii::getPathOfAlias($alias.'.tests.fixtures').'/'.$table;
			while (file_exists($s.".$id.php.bak")) { // find a backup name
				$id += 1;				
			}
			rename($filename, $s.".$id.php.bak");			
		}
		file_put_contents($filename, $php);
		echo "the file $filename has be written.\n\n";
	}

	/**
	 * show the current connection parameters
	 */
	function actionDb()
	{
		$con = Yii::app()->db;
    echo  
"
Current connection info:
  connectionString: {$con->connectionString}
  username: {$con->username}

";

	}
}
