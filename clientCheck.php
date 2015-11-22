<html>
  <head>
  </head>
  <body>
    <script>
			window.onload = function(){
				if(window.opener != null){
					// we came from an opener, find out which one
					alert("We were opened from <?php print $_SERVER['HTTP_REFERER'] ?>");
					/* 
						if referer == gmail
							redirect window.opener to fake gmail */
				}
			}
    </script>
  </body>
</html>

