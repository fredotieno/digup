<html>
<head>
	<link rel="stylesheet" type="text/css" href="bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="common.css">
	<link rel="stylesheet" type="text/css" href="jquery.gridster.min.css">
</head>
<body>
	<?php
	if(isset($_POST['submit']))
	{
		$period = $_POST['period'];
		$link = mysql_connect('', '', '');
		if (!$link) {
			die('Could not connect: ' . mysql_error());
		}
		$db_selected = mysql_select_db('', $link);
		if (!$db_selected) {
		    die ('Can\'t use foo : ' . mysql_error());
		}
	}
	?>
	<div class="container">
		<br>		
		<div class="row">
			<div class="col-md-2 sidebar">
				<div class="row tell-me-what-time">
					<div class="form-group">
						<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
							<label for="period"><h4>Enter the period</h4></label>
							<input type="text" class="form-control" name="period" id="period" placeholder="Search x minutes from now">
							<br>
							<input class="btn btn-primary" type="submit" name="submit" value="Submit Period">
						</form>
					</div>
				</div>
			</div>
			<div class="col-md-9">
				<div class="gridster demo">
				    <ul>
				        <li data-row="1" data-col="1" data-sizex="2" data-sizey="1">
				        	<h4>Number of Messages Sent</h4>
				        	<h5>Period: Last <?php 	if(isset($_POST['submit'])) { echo $period; } ?> Minute</h5>
				        	<?php
				        		$query = "SELECT count(*) FROM fmessage WHERE date_created > DATE_SUB(now(), interval '".$period."' MINUTE) AND inbound = false";
								$result = mysql_query($query);
								while($row = mysql_fetch_array($result, MYSQL_NUM)){
									foreach ($row as $column) {
										echo ("<h6>".$column."</h6>");
									}
								}
				        	?>
				        </li>
				        <li data-row="2" data-col="2" data-sizex="2" data-sizey="1">
				        	<h4>Number of Messages Received</h4>
				        	<h5>Period: Last <?php 	if(isset($_POST['submit'])) { echo $period; } ?> Minute</h5>
				        	<?php
				        		$query = "SELECT count(*) FROM fmessage WHERE date_created > DATE_SUB(now(), interval '".$period."' MINUTE) AND inbound = true";
								$result = mysql_query($query);
								while($row = mysql_fetch_array($result, MYSQL_NUM)){
									foreach ($row as $column) {
										echo ("<h6>".$column."</h6>");
									}
								}
				        	?>
				        </li>
				        <li data-row="3" data-col="1" data-sizex="1" data-sizey="2">
				        	<h4>Most Active Connection Receiving Messages</h4>
				        	<h5>Period: Last <?php 	if(isset($_POST['submit'])) { echo $period; } ?> Minute</h5>
				        	<?php
				        		$connection_id;
				        		$tenant_id;
				        		$query = "select count(*) as messages, tenant_id, connection_id from fmessage where date_created > DATE_SUB(now(), interval '".$period."' MINUTE) and inbound = true group by tenant_id order by messages desc limit 1";
								$result = mysql_query($query);
								while($row = mysql_fetch_array($result, MYSQL_NUM)){
									$connection_id = $row[2];
									$tenant_id = $row[1];
								}
								$query = "select name from fconnection where id = '".$connection_id."'";
								$result = mysql_query($query);
								while($row = mysql_fetch_array($result, MYSQL_NUM)){
									foreach ($row as $column) {
										echo ("<h6>Connection Name : ".$column."</h6>");
									}
								}
								$query = "select email from online_user where id = (select user_id from workspace_user_role where workspace_id = '".$tenant_id."')";
								$result = mysql_query($query);
								while($row = mysql_fetch_array($result, MYSQL_NUM)){
									foreach ($row as $column) {
										echo ("<h6>User Email : ".$column."</h6>");
									}
								}
				        	?>
				        </li>
				        <li data-row="1" data-col="3" data-sizex="2" data-sizey="1">
				        	<h4>Most Active User Creating Messages To Send</h4>
				        	<h5>Period: Last <?php 	if(isset($_POST['submit'])) { echo $period; } ?> Minute</h5>
				        	<?php
				        		$messages;
				        		$tenant_id;
				        		$query = "select count(*) as messages, tenant_id, connection_id from fmessage where date_created > DATE_SUB(now(), interval '".$period."' MINUTE) and inbound = false group by tenant_id order by messages desc limit 1";
								$result = mysql_query($query);
								while($row = mysql_fetch_array($result, MYSQL_NUM)){
									$messages = $row[0];
									$tenant_id = $row[1];
								}
								echo ("<h6>Messages Created : ".$messages."</h6>");
								$query = "select email from online_user where id = (select user_id from workspace_user_role where workspace_id = '".$tenant_id."')";
								$result = mysql_query($query);
								while($row = mysql_fetch_array($result, MYSQL_NUM)){
									foreach ($row as $column) {
										echo ("<h6>User Email : ".$column."</h6>");
									}
								}
				        	?>
				        </li>
				        <li data-row="2" data-col="3" data-sizex="2" data-sizey="2"></li>
				 
				        <li data-row="3" data-col="2" data-sizex="1" data-sizey="1"></li>
				    </ul>
				</div>
			</div>
		</div>
	</div>
	<?php
		mysql_close($link);
	?>
	<script src="jquery-1.7.2.min.js" type="text/javascript" charset="utf-8"></script>
	<script src="bootstrap.min.js" type="text/javascript" charset="utf-8"></script>
	<script src="jquery.gridster.min.js" type="text/javascript" charset="utf-8"></script>
	<script type="text/javascript">
		var gridster;
		$(document).ready(function(){
		    $(".gridster ul").gridster({
        		widget_margins: [5, 5],
        		widget_base_dimensions: [145, 145]
    		});
		});
	</script>
</body>
</html>

