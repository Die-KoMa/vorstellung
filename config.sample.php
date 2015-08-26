<?php
$eventname = "KoMa 75";
$inputs = array(
    'facts' => array(
        array('name' => 'vertretene Studierende', 'type' => 'number'),
        array('name' => 'aktive Fachschaftler', 'type' => 'number'),
        array('name' => 'verfasste Stud.schaft', 'type' => 'checkbox'),
        array('name' => 'Letzte besuchte KoMa', 'type' => 'text')
    ),
    'enums' => array(
        array('name' => 'Aktuelle Probleme'),
        array('name' => 'Projekte/Aktionen'),
    )
);
$browser_api_key = '';
$server_api_key = '';

$data_file = 'data.json';

$manual_data = array("/m/0df2pf" => array(
			"name" => "Technische Universität Kaiserslautern",
			"a:containedby" => array(
				"name" => "Rheinland-Pfalz",
				"geolocation" => array(
					"latitude" => 49.913056
				),
			),
			"geolocation" => array(
				"latitude" => 49.444722,
				"longitude" => 7.768889,
			),
			"/education/educational_institution/total_enrollment" => array(
				"number" => 12510,
				"year" => 2010,
			),
			"mid" => "/m/0df2pf",
		));
