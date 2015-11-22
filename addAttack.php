<?php
	include('prereq.php');
	$db = new MyDB();

	if(	isset($_POST['o']) && isset($_POST['n']) && isset($_POST['c'] )){
		$stm = $db->prepare("INSERT INTO attacks (orig_url, new_url, control_code) VALUES (?,?,?)");
		$stm->bindParam(1, $_POST['o'], SQLITE3_TEXT);	
		$stm->bindParam(2, $_POST['n'], SQLITE3_TEXT);	
		$stm->bindParam(3, $_POST['c'], SQLITE3_TEXT);	
		$stm->execute();
		print "ok";
	}
	else
	{
		print "Error";
	}

	$db->close(); unset($db);

?>
