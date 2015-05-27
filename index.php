<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/bootstrap/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="css/common.css">
	<link rel="stylesheet" type="text/css" href="css/jquery.gridster.min.css">
	<link rel="stylesheet" type="text/css" href="css/bootstrap-datetimepicker.min.css">
	<link rel="shortcut icon" href="assets/favicon.ico">
</head>
<body>
	<?php
	if(isset($_POST['submit']))
	{
		$period = $_POST['period'];
		$fromDate = $_POST['fromDate'];
		$endDate = $_POST['endDate'];
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
							<label for="period"><h5>Or From Start Date</h4></label>
							<div class="form-group">
				                <div class="input-group date" id="datetimepicker1">
				                    <input type="text" class="form-control" name="fromDate" />
				                    <span class="input-group-addon">
				                        <span class="glyphicon glyphicon-calendar">
				                        </span>
				                    </span>
				                </div>
				            </div>
							<br>
							<label for="period"><h5>to End Date</h4></label>
							<div class="form-group">
				                <div class="input-group date" id="datetimepicker2">
				                    <input type="text" class="form-control" name="endDate"/>
				                    <span class="input-group-addon">
				                        <span class="glyphicon glyphicon-calendar">
				                        </span>
				                    </span>
				                </div>
				            </div>
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
				        	<?php
				        		if($fromDate && $endDate) {
				        			echo ("<h5>Period: Between ".$fromDate." and ".$endDate."</h5>");
					        		$query = "SELECT count(*) FROM fmessage WHERE date_created > '".$fromDate."' AND date_created < '".$endDate."' AND inbound = false";
				        		}
				        		else {
				        			echo ("<h5>Period: Last ".$period." Minutes</h5>");
					        		$query = "SELECT count(*) FROM fmessage WHERE date_created > DATE_SUB(now(), interval '".$period."' MINUTE) AND inbound = false";
				        		}
								$result = mysql_query($query);
								while($row = mysql_fetch_array($result, MYSQL_NUM)){
									foreach ($row as $column) {
										echo ("<h2>".$column."</h2>");
									}
								}
				        	?>
				        </li>
				        <li data-row="2" data-col="2" data-sizex="2" data-sizey="1">
				        	<h4>Number of Messages Received</h4>
				        	<?php
				        		if($fromDate && $endDate) {
				        			echo ("<h5>Period: Between ".$fromDate." and ".$endDate."</h5>");
					        		$query = "SELECT count(*) FROM fmessage WHERE date_created > '".$fromDate."' AND date_created < '".$endDate."' AND inbound = true";
				        		}
				        		else {
				        			echo ("<h5>Period: Last ".$period." Minutes</h5>");
					        		$query = "SELECT count(*) FROM fmessage WHERE date_created > DATE_SUB(now(), interval '".$period."' MINUTE) AND inbound = true";
				        		}
								$result = mysql_query($query);
								while($row = mysql_fetch_array($result, MYSQL_NUM)){
									foreach ($row as $column) {
										echo ("<h2>".$column."</h2>");
									}
								}
				        	?>
				        </li>
				        <li data-row="3" data-col="1" data-sizex="2" data-sizey="2">
				        	<h4>Most Active Connection Receiving Messages</h4>
				        	<?php
				        		$connection_id = '';
				        		$tenant_id = '';
				        		if($fromDate && $endDate) {
				        			echo ("<h5>Period: Between ".$fromDate." and ".$endDate."</h5>");
				        			$query = "select count(*) as messages, tenant_id, connection_id from fmessage where date_created > '".$fromDate."' AND date_created < '".$endDate."' and inbound = true group by tenant_id order by messages desc limit 1";
				        		}
				        		else {
				        			echo ("<h5>Period: Last ".$period." Minutes</h5>");
				        			$query = "select count(*) as messages, tenant_id, connection_id from fmessage where date_created > DATE_SUB(now(), interval '".$period."' MINUTE) and inbound = true group by tenant_id order by messages desc limit 1";
				        		}
								$result = mysql_query($query);
								while($row = mysql_fetch_array($result, MYSQL_NUM)){
									$connection_id = $row[2];
									$tenant_id = $row[1];
									echo("<h6>Tenant: ".$row[1]."</h6>");
									echo ("<h6>Messages Received: ".$row[0]."</h6>");
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
				        <li data-row="1" data-col="3" data-sizex="2" data-sizey="2">
				        	<h4>Most Active User Creating Messages To Send</h4>
				        	<?php
				        		$messages = '';
				        		$tenant_id = '';
				        		if($fromDate && $endDate) {
				        			echo ("<h5>Period: Between ".$fromDate." and ".$endDate."</h5>");
				        			$query = "select count(*) as messages, tenant_id, connection_id from fmessage where date_created > '".$fromDate."' AND date_created < '".$endDate."' and inbound = false group by tenant_id order by messages desc limit 1";
				        		}
				        		else {
				        			echo ("<h5>Period: Last ".$period." Minutes</h5>");
				        			$query = "select count(*) as messages, tenant_id, connection_id from fmessage where date_created > DATE_SUB(now(), interval '".$period."' MINUTE) and inbound = false group by tenant_id order by messages desc limit 1";
				        		}
								$result = mysql_query($query);
								while($row = mysql_fetch_array($result, MYSQL_NUM)){
									$messages = $row[0];
									$tenant_id = $row[1];
									echo("<h6>Tenant: ".$row[1]."</h6>");
									echo ("<h6>Messages Created : ".$messages."</h6>");

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
				        <li data-row="2" data-col="3" data-sizex="2" data-sizey="2">
				        	<h4>Most Active Connection Successfully Sending Messages</h4>
							<?php
				        		$tenant_id = '';
				        		$connection_id = '';
				        		if($fromDate && $endDate) {
				        			echo ("<h5>Period: Between ".$fromDate." and ".$endDate."</h5>");
				        			$query = "select count(*) as messages, fconnection_id, tenant_id FROM dispatch WHERE date_sent > '".$fromDate."' AND date_sent < '".$endDate."' group by tenant_id order by messages desc limit 1";
				        		}
				        		else {
				        			echo ("<h5>Period: Last ".$period." Minutes</h5>");
				        			$query = "select count(*) as messages, fconnection_id, tenant_id FROM dispatch WHERE date_sent > DATE_SUB(now(), interval '".$period."' MINUTE) group by tenant_id order by messages desc limit 1";
				        		}
								$result = mysql_query($query);
								while($row = mysql_fetch_array($result, MYSQL_NUM)){
									$connection_id = $row[1];
									$tenant_id = $row[2];
									echo("<h6>Tenant: ".$row[2]."</h6>");
									echo ("<h6>Messages Sent: ".$row[0]."</h6>");

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
				 
				        <li data-row="3" data-col="2" data-sizex="2" data-sizey="1">
				        	<h4>With Most Recipe Events</h4>
							<?php
				        		$tenant_id = '';
				        		if($fromDate && $endDate) {
				        			echo ("<h5>Period: Between ".$fromDate." and ".$endDate."</h5>");
				        			$query = "select count(*) as recipes, tenant_id FROM recipe_event WHERE date_created > '".$fromDate."' AND date_created < '".$endDate."' and conditions_matched = true group by tenant_id order by recipes desc limit 1";
				        		}
				        		else {
				        			echo ("<h5>Period: Last ".$period." Minutes</h5>");
				        			$query = "select count(*) as recipes, tenant_id FROM recipe_event WHERE date_created > DATE_SUB(now(), interval '".$period."' MINUTE) and conditions_matched = true group by tenant_id order by recipes desc limit 1";
				        		}
								$result = mysql_query($query);
								while($row = mysql_fetch_array($result, MYSQL_NUM)){
									$tenant_id = $row[1];
									echo("<h6>Tenant: ".$row[1]."</h6>");
									echo ("<h6>Recipes Triggered With All Condition Passed: ".$row[0]."</h6>");

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
				        <li data-row="4" data-col="1" data-sizex="2" data-sizey="1">
				        	<h4>Top 5 With Most Recipe Events</h4>
				        	<?php
				        		if($fromDate && $endDate) {
				        			echo ("<h5>Period: Between ".$fromDate." and ".$endDate."</h5>");
				        			$query = "select count(*) as recipes, tenant_id FROM recipe_event WHERE date_created > '".$fromDate."' AND date_created < '".$endDate."' and conditions_matched = true group by tenant_id order by recipes desc limit 5";
				        		}
				        		else {
				        			echo ("<h5>Period: Last ".$period." Minutes</h5>");
				        			$query = "select count(*) as recipes, tenant_id FROM recipe_event WHERE date_created > DATE_SUB(now(), interval '".$period."' MINUTE) and conditions_matched = true group by tenant_id order by recipes desc limit 5";
				        		}
								$result = mysql_query($query);
								while($row = mysql_fetch_array($result, MYSQL_NUM)){
									echo ("<h6>Recipe Events ".$row[0]." Tenant ".$row[1]."</h6>");
								}
				        	?>
				        </li>
				        <li data-row="4" data-col="2" data-sizex="2" data-sizey="1">
				        	<h4>Top 5 With Most Received Messages</h4>
				        	<?php
				        		if($fromDate && $endDate) {
				        			echo ("<h5>Period: Between ".$fromDate." and ".$endDate."</h5>");
					        		$query = "SELECT count(*) as messages, tenant_id FROM fmessage WHERE date_created > '".$fromDate."' AND date_created < '".$endDate."' AND inbound = true group by tenant_id order by messages desc limit 5";
				        		}
				        		else {
				        			echo ("<h5>Period: Last ".$period." Minutes</h5>");
					        		$query = "SELECT count(*) as messages, tenant_id FROM fmessage WHERE date_created > DATE_SUB(now(), interval '".$period."' MINUTE) AND inbound = true group by tenant_id order by messages desc limit 5";
				        		}
								$result = mysql_query($query);
								while($row = mysql_fetch_array($result, MYSQL_NUM)){
									echo ("<h6>Messages ".$row[0]." Tenant ".$row[1]."</h6>");
								}
				        	?>
				        </li>
				        <li data-row="4" data-col="3" data-sizex="2" data-sizey="1">
				        	<h4>Top 5 With Most Sent Messages</h4>
				        	<?php
				        		if($fromDate && $endDate) {
				        			echo ("<h5>Period: Between ".$fromDate." and ".$endDate."</h5>");
					        		$query = "SELECT count(*) as messages, tenant_id FROM fmessage WHERE date_created > '".$fromDate."' AND date_created < '".$endDate."' AND inbound = false group by tenant_id order by messages desc limit 5";
				        		}
				        		else {
				        			echo ("<h5>Period: Last ".$period." Minutes</h5>");
					        		$query = "SELECT count(*) as messages, tenant_id FROM fmessage WHERE date_created > DATE_SUB(now(), interval '".$period."' MINUTE) AND inbound = false group by tenant_id order by messages desc limit 5";
				        		}
								$result = mysql_query($query);
								while($row = mysql_fetch_array($result, MYSQL_NUM)){
									echo ("<h6>Messages ".$row[0]." Tenant ".$row[1]."</h6>");
								}
				        	?>
				        </li>
				    </ul>
				</div>
			</div>
		</div>
	</div>
	<?php
		mysql_close($link);
	?>
	<script src="js/jquery-2.1.3.min.js" type="text/javascript" charset="utf-8"></script>
	<script src="js/bootstrap.min.js" type="text/javascript" charset="utf-8"></script>
	<script src="js/jquery.gridster.min.js" type="text/javascript" charset="utf-8"></script>
	<script src="js/moment.min.js" type="text/javascript" charset="utf-8"></script>
	<script src="js/bootstrap-datetimepicker.min.js" type="text/javascript" charset="utf-8"></script>
	<script type="text/javascript">
		var gridster;
		$(document).ready(function(){
		    $(".gridster ul").gridster({
        		widget_margins: [5, 5],
        		widget_base_dimensions: [145, 145]
    		});
    		$('#datetimepicker1').datetimepicker({
    			format: "YYYY-MM-DD HH:mm:ss",
    		});
    		$('#datetimepicker2').datetimepicker({
    			format: "YYYY-MM-DD HH:mm:ss",
    		});
		});
	</script>
</body>
</html>

