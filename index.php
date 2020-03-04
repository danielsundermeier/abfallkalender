<?php

    include_once('vendor/autoload.php');
    include_once('config.php');

    use GuzzleHttp\Client;

    $alphabet = range('A', 'Z');
    $alphabet[] = '[';

    $ical = new \Abfallkalender\Ical();

    $client = new Client([
        'base_uri' => BASE_URI,
        'timeout'  => 2.0,
    ]);
    $headers    = [];
    $response   = $client->post(strtolower(STADT) . '/index.php', [
        'headers' => $headers,
        'form_params' => [
            'anzeigen'  => 'anzeigen',
            'hausnr'    => HAUSNR,
            'strasse'   => STRASSE,
        ],
        'query' => [
            'von' => VON,
            'bis' => $alphabet[(array_search(VON, $alphabet) + 1)],
            'mit_container' => 'Ja',
        ],
    ]);
    $body = $response->getBody();

    // echo $body;

    $matches = [];
    $pattern = '/\w{2}\. den \d{2}\.\d{2}\.\d{4}/m';
    $anzahl = preg_match_all($pattern, $body, $matches);

    $keys = [
        'restmuell',
        'biotonne',
        'papiertonne',
        'gelbe_saecke',
        'schadstoffanlieferung'
    ];
    $dates  = [];
    $key    = 0;
    $i      = 1;
    $maxDates = count($keys) * 3;
    foreach ($matches[0] as $value) {

        $parts = explode(' ', $value);
        $dates[$keys[$key]][] = DateTime::createFromFormat('d.m.Y', $parts[2], new DateTimeZone('Europe/Berlin'));

        if ($i % 3 == 0) {
            $key++;
        }
        $i++;
    }

    // echo '<pre>';
    // var_dump($dates);
    // echo '</pre>';

    echo $ical->handle($dates);
?>