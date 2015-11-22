<pre><?php
	print '$_SERVER:';
	print_r($_SERVER);
	print '$_REQUEST:';
	print_r($_REQUEST);

?></pre>
<script>
document.write(document.referrer);
if(window.opener){
	document.write("We have an opener");
}
</script>
