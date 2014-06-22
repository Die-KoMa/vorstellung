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
