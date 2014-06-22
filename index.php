<?php
require_once("config.php");

function mql_read_university($mid) {
	global $server_api_key;
	$state_query = array(
		'name' => null,
		'type|=' => array('/location/de_state', '/location/country'),
		'mid!=' => '/m/0345h', // Deutschland
		'geolocation' => array('latitude' => null),
	);
	$query = array(
		'mid' => $mid,
		'type' => '/location/location',
		'b:type' => '/education/university',
		'name' => null,
		'geolocation' => array(
			'longitude' => null,
			'latitude' => null,
		),
		'/education/educational_institution/total_enrollment' => array(
			'number' => null,
			'year' => null,
			'sort' => '-year',
			'limit' => 1,
			'optional' => 'optional',
		),
		'a:containedby' => array_merge( // if the state is an immediate container of the university...
			$state_query,
			array('optional' => 'optional')
		),
		'b:containedby' => array( // if there is an intermediate container...
			'containedby' => $state_query,
			'optional' => 'optional',
			'type' => '/location/location',
		),
	);
	$args = array(
		'lang' => '/lang/de',
		'key' => $server_api_key,
		'query' => json_encode($query),
	);
	$url = 'https://www.googleapis.com/freebase/v1/mqlread?' . http_build_query($args);
	$json = file_get_contents($url);
	return json_decode($json, true);
}

function intcmp($a, $b) {
	if($a == $b)
		return 0;
	elseif($a > $b)
		return 1;
	elseif($a < $b)
		return -1;
}

// Read data
$fd = fopen($data_file, 'r');
flock($fd, LOCK_SH);
$data = json_decode(stream_get_contents($fd), true);
flock($fd, LOCK_UN);
fclose($fd);

// Merge Freebase data with supplied values
$geolimits = array(
	'latitude'  => array('min' => 90, 'max' => -90),
	'longitude' => array('min' => 90, 'max' => -90),
);
$universities = array();
foreach($data as $mid => $supplied) {
	$info = mql_read_university($mid);
	if(!isset($info['result']))
		continue;
	else
		$info = $info['result'];
	$universities[] = array_merge($supplied, array(
		'name' => $info['name'],
		'geolocation' => $info['geolocation'],
		'state' => $info['a:containedby']? $info['a:containedby'] : $info['b:containedby']['containedby'],
		'students' => $info['/education/educational_institution/total_enrollment'],
	));
	foreach($info['geolocation'] as $dir => $val) {
		$geolimits[$dir]['max'] = max($geolimits[$dir]['max'], $val);
		$geolimits[$dir]['min'] = min($geolimits[$dir]['min'], $val);
	}
}

// Group universities by state, sort states by latitude
usort($universities, function($a, $b) {
	if($a['state']['name'] == $b['state']['name'])
		return -intcmp($a['geolocation']['latitude'], $b['geolocation']['latitude']);
	else
		return -intcmp($a['state']['geolocation']['latitude'], $b['state']['geolocation']['latitude']);
});
?>
<!DOCTYPE HTML>
<html lang="en">
<head>
	<title><?php echo $eventname; ?> Anfangsplenum</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=792, user-scalable=no">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<link rel="stylesheet" href="https://raw.githubusercontent.com/markdalgleish/fathom/master/fathom.sample.css">
	<link rel="stylesheet" href="assets/presentation.css">
</head>
<body>
<div class="map" id="map"></div>
<div id="presentation">
<!--<div class="slide">
    <h2><?php echo $eventname; ?> Anfangsplenum</h2>
</div>-->
<?php
foreach($universities as $university):
?>
<div class="slide" data-geo="<?php $loc = $university['geolocation']; echo $loc['latitude'].' '.$loc['longitude']; ?>">
    <h2><?php echo $university['name']; ?></h2>
    <dl>
        <dt>Land</dt>
        <dd><?php echo htmlspecialchars($university['state']['name']); ?></dd>
		<dt>Studierende gesamt</dt>
		<dd><?php echo $university['students']['number']; ?> (Stand <?php echo $university['students']['year']; ?>)</dd>
<?php foreach($inputs['facts'] as $i => $fact): ?>
        <dt><?php echo htmlspecialchars($fact['name']); ?></dt>
        <dd><?php if($fact['type'] == 'checkbox') {
			if($university['facts'][$i]) echo "Ja";
			else echo "Nein";
		} else echo htmlspecialchars($university['facts'][$i]); ?></dd>
<?php endforeach; ?>
    </dl>
<?php foreach($inputs['enums'] as $i => $enum): ?>
    <div class="column">
        <h3><?php echo htmlspecialchars($enum['name']); ?></h3>
        <ul>
<?php foreach($university['enums'][$i] as $item): ?>
            <li><?php echo htmlspecialchars($item); ?></li>
<?php endforeach; ?>
        </ul>
    </div>
<?php endforeach; ?>
</div>
<?php endforeach; ?>
</div>
<script src="http://www.openlayers.org/api/OpenLayers.js"></script>
<script src="https://code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="https://raw.githubusercontent.com/markdalgleish/fathom/master/fathom.min.js"></script>
<script>
var geolimits = [
	[<?php echo $geolimits['longitude']['min'] . ',' . $geolimits['latitude']['max']; ?>],
	[<?php echo $geolimits['longitude']['max'] . ',' . $geolimits['latitude']['min']; ?>]
];
</script>
<script src="assets/presentation.js"></script>
</body>
</html>
