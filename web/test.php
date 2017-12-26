<?php
require __DIR__ . '/../vendor/autoload.php';

use WebServCo\Settings\Config;
use WebServCo\Multimedia\Subtitles;

$subs = Subtitles::ParseSrt(Config::SRT_DIR . 'test.srt');
$data = [];
$pattern = '/' . '\[(.+)\](\s+)(.+)(\n)([0-9.]+),([0-9.]+)' . '/';
foreach ($subs as $item) {
    preg_match($pattern, $item->text, $matches);
    if (!empty($matches[6])) {
        $data[] = [
            'date' => $matches[1],
            'speed' => str_replace('Km/h' , '', $matches[3]),
            'lat' => $matches[5],
            'lng' => $matches[6],
        ];
    }
}

//echo '<pre>'; print_r($data); exit; //XXX

/*
 * https://developers.google.com/kml/articles/phpmysqlkml
 */
$dom = new DOMDocument('1.0', 'UTF-8');
$dom->preserveWhiteSpace = FALSE;
$dom->formatOutput = TRUE;
$dom->load(Config::KML_DIR . 'route_template.kml');

$documentNode = $dom->getElementsByTagName('Document')->item(0);

/**
 * Points layer
 */
$pointsNode = $documentNode->appendChild($dom->createElement('Folder'));
$pointsNode->appendChild($dom->createElement('name','Points layer title'));

/**
 * Route layer
 */
$routeNode = $documentNode->appendChild($dom->createElement('Folder'));
$routeNode->appendChild($dom->createElement('name','Route layer title'));

$routePlaceNode = $routeNode->appendChild($dom->createElement('Placemark'));
$routePlaceNode->appendChild($dom->createElement('name','Placemark name'));
$routePlaceNode->appendChild($dom->createElement('description','Placemark description'));
$routePlaceNode->appendChild($dom->createElement('styleUrl','#line-1267FF-5000-nodesc'));

$lineNode = $dom->createElement('LineString');
$routePlaceNode->appendChild($lineNode);
$lineNode->appendChild($dom->createElement('tessellate','1'));

/**
 * Data
 */
$lineData = NULL;
foreach ($data as $entry) {
    $placeNode = $pointsNode->appendChild($dom->createElement('Placemark'));
    $placeNode->appendChild($dom->createElement('name',"{$entry['date']} - {$entry['speed']} Km/h"));
    $placeNode->appendChild($dom->createElement('description','Placemark description'));
    $placeNode->appendChild($dom->createElement('styleUrl','#icon-1899-DB4436-nodesc'));
    $pointNode = $dom->createElement('Point');
    $placeNode->appendChild($pointNode);
    $pointNode->appendChild($dom->createElement('coordinates',"{$entry['lng']},{$entry['lat']},0"));
    $lineData .= "{$entry['lng']},{$entry['lat']},0" . PHP_EOL;
}

$lineNode = $dom->createElement('LineString');
$routePlaceNode->appendChild($lineNode);
$lineNode->appendChild($dom->createElement('tessellate','1'));
$lineNode->appendChild($dom->createElement('coordinates',$lineData));

$result = $dom->save(Config::KML_DIR . 'route_output.kml');

var_dump($result); exit; //XXX

/*
 * /usr/bin/php /home/radu/web/car/web/test.php
 */
?>