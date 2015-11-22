<?php
	include('prereq.php');
	$db = new MyDB();

	if(	isset($_POST['v']) && (int)$_POST['v'] > 0){
		$stm = $db->prepare("DELETE FROM victims WHERE id = ?");
		$stm->bindParam(1, $_POST['v'], SQLITE3_INTEGER);	
		$stm->execute();
		print "ok";
	}
	else
	{
		print "Error";
	}

	$db->close(); unset($db);

?>
