<?php

use Alfred\Workflows\Workflow;

use Algolia\AlgoliaSearch\SearchClient as Algolia;
use Algolia\AlgoliaSearch\Support\UserAgent as AlgoliaUserAgent;

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/functions.php';

$query = $argv[1];

$workflow = new Workflow;
$algolia = Algolia::create('R90K1756AM', 'a6e52654d6b591febdf42b07e0e7374a');

AlgoliaUserAgent::addCustomUserAgent('TailwindCSS Alfred Workflow', '2.0.1');

$results = getResults($algolia, 'v0_tailwindcss', $query);

if (empty($results)) {
    $workflow->result()
        ->title('No matches')
        ->icon('google.png')
        ->subtitle("No match found in the docs. Search Google for: \"TailwindCSS+{$query}\"")
        ->arg("https://www.google.com/search?q=tailwindcss+{$query}")
        ->quicklookurl("https://www.google.com/search?q=tailwindcss+{$query}")
        ->valid(true);

    echo $workflow->output();
    exit;
}

foreach ($results as $hit) {
    list($title, $titleLevel) = getTitle($hit);

    if ($title === null) {
        continue;
    }

    $title = html_entity_decode($title);

    $workflow->result()
        ->uid($hit['objectID'])
        ->title($title)
        ->autocomplete($title)
        ->subtitle(html_entity_decode(getSubtitle($hit, $titleLevel)))
        ->arg($hit['url'])
        ->quicklookurl($hit['url'])
        ->valid(true);
}

echo $workflow->output();
