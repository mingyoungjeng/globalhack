<?php
	$db = mysqli_connect('127.0.0.1','root','M@c1nt0$h','globalhack')
	or die('Error connecting to MySQL server.');
 ?>

<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8"/>
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<title>United Compass</title>
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
			<img src="../logo.png" style="height: 80%;">
		</div>

		<div id="sidebar">
			<button id="addStry" type="button" class="btn btn-primary btn-lg btn-block">Add your story</button>

			<br>
			<div class="pac-card" id="pac-card">
				<form id="compare" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>"">
				<div id="pac-container">
					<input id="pac-input" class="form-control" type="text" placeholder="Enter a location" aria-label="Search">
					<p id="compare"></p>
				</div>
				</form>
			</div>
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
						$sidePlace0 = $row[3];
						$sidePlace1 = $row[4];
					}
				?>
				<b><h2><?php echo $sideName ?></h2></b>
				<p><small id="sidePlace0"><?php echo $sidePlace0; ?></small><small> to </small><small id="sidePlace1"><?php echo $sidePlace1; ?></small></p>
				<p><?php echo $sideText ?></p>
			</div>
		</div>
		<div id="map"></div>
		<div class="footer">
			<form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" style="padding: 0 10pt;">
				<div class="form-check form-check-inline" style="margin: 10pt 0;">
					<input class="form-control" type="text" name="name" placeholder="Name" style="margin-right: 5pt;">

					<input id="origin-input" class="controls form-control" type="text" placeholder="Origin" name="origin" style="margin-right: 5pt;">
					<input id="destination-input" class="controls form-control" type="text" placeholder="Destination" name="destination" style="margin-right: 5pt;">
					<button type="submit" class="btn btn-primary">Submit</button>
				</div>
				<input class="form-control" type="text" name="story" placeholder="Submit your Story" style="margin-bottom: 10pt; height: 10vh;">
			</form>
		</div>
		<?php
			$name = $_POST["name"];
			$message = $_POST["story"];
			$origin = $_POST["origin"];
			$destination = $_POST["destination"];
			$sql = "INSERT INTO messages(name, message, origin, destination) VALUES ('$name', '$message', '$origin', '$destination')";
			if ($name!=null && $message!=null && $origin!=null && $destination!=null) {
				if(mysqli_query($db, $sql)){

				} else{
					echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
				}
			}	
		?>

		<script>
			// This example requires the Places library. Include the libraries=places
			// parameter when you first load the API. For example:
			// <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places">

			function initMap() {
				var map = new google.maps.Map(document.getElementById('map'), {
					center: {lat: 25, lng: 0},
					zoom: 2
				});
				var input = document.getElementById('pac-input');
				var originInput = document.getElementById('origin-input');
				var destinationInput = document.getElementById('destination-input');

				var options = {
					types: ['(cities)'] // Add Countries if possible
				};

				var autocomplete = new google.maps.places.Autocomplete(input, options);
				var originAutocomplete = new google.maps.places.Autocomplete(originInput, options);
				var destinationAutocomplete = new google.maps.places.Autocomplete(destinationInput, options);

				// Set the data fields to return when the user selects a place.
				autocomplete.setFields(['address_components', 'geometry', 'icon', 'name']);

				var marker = new google.maps.Marker({
					map: map,
					anchorPoint: new google.maps.Point(0, -29)
				});
				var markerO = new google.maps.Marker({
					map: map,
					anchorPoint: new google.maps.Point(0, -29)
				});
				var markerD = new google.maps.Marker({
					map: map,
					anchorPoint: new google.maps.Point(0, -29)
				});

				autocomplete.addListener('place_changed', function() {
					marker.setVisible(false);
					var place = autocomplete.getPlace();

					// If the place has a geometry, then present it on a map.
					if (place.geometry.viewport) {
						map.fitBounds(place.geometry.viewport);
					} else {
						map.setCenter(place.geometry.location);
						map.setZoom(17);  // Why 17? Because it looks good.
					}
					marker.setPosition(place.geometry.location);
					marker.setVisible(true);
				});

				var flightPlanCoordinates = ["", ""];
				var flightPath = new google.maps.Polyline({
					path: flightPlanCoordinates,
					geodesic: true,
					strokeColor: '#FF0000',
					strokeOpacity: 1.0,
					strokeWeight: 2
					});

				originAutocomplete.addListener('place_changed', function() {
					markerO.setVisible(false);
					var place = originAutocomplete.getPlace();
					markerO.setPosition(place.geometry.location);
					markerO.setVisible(true);

					flightPath.setMap(null);
					flightPlanCoordinates[0] = place.geometry.location;

					flightPath = new google.maps.Polyline({
					path: flightPlanCoordinates,
					geodesic: true,
					strokeColor: '#FF0000',
					strokeOpacity: 1.0,
					strokeWeight: 2
					});

					flightPath.setMap(map);
				});

				destinationAutocomplete.addListener('place_changed', function() {
					markerD.setVisible(false);
					var place = destinationAutocomplete.getPlace();
					markerD.setPosition(place.geometry.location);
					markerD.setVisible(true);

					flightPath.setMap(null);
					flightPlanCoordinates[1] = place.geometry.location;

					flightPath = new google.maps.Polyline({
					path: flightPlanCoordinates,
					geodesic: true,
					strokeColor: '#FF0000',
					strokeOpacity: 1.0,
					strokeWeight: 2
					});

					flightPath.setMap(map);
				});

				var geocoder = new google.maps.Geocoder();
				geocodeAddress(geocoder, map);
			}

			function geocodeAddress(geocoder, resultsMap) {
				var flightPlanCoordinates1 = [];
				var sidePlace0 = document.getElementById('sidePlace0').innerHTML;
				var sidePlace1 = document.getElementById('sidePlace1').innerHTML;
				geocoder.geocode({'address': sidePlace0}, function(results, status) {
					if (status === 'OK') {
						flightPlanCoordinates1[0] = results[0].geometry.location;
						var marker = new google.maps.Marker({map: resultsMap, position: results[0].geometry.location});
					}
				});
				geocoder.geocode({'address': sidePlace1}, function(results, status) {
					if (status === 'OK') {
						flightPlanCoordinates1[1] = results[0].geometry.location;
						var marker1 = new google.maps.Marker({map: resultsMap, position: results[0].geometry.location});

						var flightPath1 = new google.maps.Polyline({
						path: flightPlanCoordinates1,
						geodesic: true,
						strokeColor: '#FF0000',
						strokeOpacity: 1.0,
						strokeWeight: 2
						});

						flightPath1.setMap(map);
					}
				});
	  		}
		</script>
		<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAJW6mJh2HviSTKbwQ3npzDcAoZR4C-je4&libraries=places&callback=initMap"
				async defer></script>

		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
		<script src='../js/bootstrap.min.js'></script>
	</body>
</html>