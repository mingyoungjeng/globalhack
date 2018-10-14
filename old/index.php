<?php
	$db = mysqli_connect('127.0.0.1','root','M@c1nt0$h','globalhack')
	or die('Error connecting to MySQL server.');
 ?>

<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8"/>
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<!-- <title>Slim 3</title> -->
		<link href='../css/style.css' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" href='../css/bootstrap.min.css'>

		<link rel="stylesheet" href="https://unpkg.com/leaflet@1.3.4/dist/leaflet.css">
		<script src="https://unpkg.com/leaflet@1.3.4/dist/leaflet.js"></script>

		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<script>
			$(document).ready(function(){
				$("#addStry").click(function(){
					$(".footer").animate({
						height: "toggle"
					});
				});
			});
		</script>
	</head>
	<body>

		<div id="header">
			<div style="display:flex;align-items:center;">The United Compass Project</div>
		</div>

		<div id="sidebar">
			<button id="addStry" type="button" class="btn btn-primary btn-lg btn-block">Add your story</button>

			<br>
			<!-- Search form -->
			<input class="form-control" type="text" placeholder="Search" aria-label="Search">
			<br>

			<div>
				<?php

					if ($sideCountResult = mysqli_query($db, "SELECT * FROM messages")) {
						$count = mysqli_num_rows($sideCountResult);
						$random = rand(1, $count);
						$randomQuery = "SELECT * FROM messages WHERE id = '$random'";
					}

					if ($randomResult = mysqli_query($db, $randomQuery)) {
						$row = mysqli_fetch_array($randomResult, MYSQLI_NUM);

						$sideName = $row[1];
						$sideText = $row[2];
						$sidePlaces = "$row[3] to $row[4]";

					}
				?>
				<b><h2><?php echo $sideName ?></h2></b>
				<small><p><?php echo $sidePlaces ?></p></small>
				<p><?php echo $sideText ?></p>
			</div>
		</div>

		<div id="map"></div>
		<div class="footer">
			<form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" style="padding: 0 10pt;">
				<div class="form-check form-check-inline" style="margin: 10pt 0;">
					<input class="form-control" type="text" name="name" placeholder="Name" style="margin-right: 5pt;">
					<input class="form-control" type="text" name="origin" placeholder="Origin" style="margin-right: 5pt;">
					<input class="form-control" type="text" name="destination" placeholder="Destination" style="margin-right: 5pt;">
					<button type="submit" class="btn btn-primary">Submit</button>
				</div>
				<input class="form-control" type="text" name="story" placeholder="Story" style="margin-bottom: 10pt; height: 10vh;">
			</form>
		</div>
		<?php
			$name = $_POST["name"];
			$message = $_POST["message"];
			$origin = $_POST["origin"];
			$destination = $_POST["destination"];
			$sql = "INSERT INTO messages(name, message, origin, destination) VALUES ('$name', '$message', '$origin', '$destination')";
			if(mysqli_query($db, $sql)){
			} else{
				echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
			}
		?>

		<script>
			var map = L.map('map',{
			center: [25, 0],
			zoom: 2
			});

			L.tileLayer('https://cartodb-basemaps-{s}.global.ssl.fastly.net/rastertiles/voyager_nolabels/{z}/{x}/{y}{r}.png', {
			attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a> &copy; <a href="http://cartodb.com/attributions">CartoDB</a>',
			subdomains: 'abcd',
			maxZoom: 19}).addTo(map);
		</script>

		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
		<script src='../js/bootstrap.min.js'></script>
	</body>
</html>
		
