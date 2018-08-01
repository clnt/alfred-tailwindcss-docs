<?php

use Alfred\Workflows\Workflow;

use AlgoliaSearch\Client as Algolia;
use AlgoliaSearch\Version as AlgoliaUserAgent;

require __DIR__ . '/vendor/autoload.php';

$query = $argv[1];

$workflow = new Workflow;
$parsedown = new Parsedown;
$algolia = new Algolia('R90K1756AM', 'cd63556797a9e2dc23249752a4e33b67');

AlgoliaUserAgent::addSuffixUserAgentSegment('TailwindCSS Alfred Workflow', '0.1.0');

$index = $algolia->initIndex('tailwindcss');
$search = $index->search($query);
$results = $search['hits'];

if (empty($results)) {
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
    exit;
}

foreach ($results as $hit) {
    $highestLvl = $hit['hierarchy']['lvl6'] ? 6 : (
        $hit['hierarchy']['lvl5'] ? 5 : (
            $hit['hierarchy']['lvl4'] ? 4 : (
                $hit['hierarchy']['lvl3'] ? 3 : (
                    $hit['hierarchy']['lvl2'] ? 2 : (
                        $hit['hierarchy']['lvl1'] ? 1 : 0
                    )
                )
            )
        )
    );

    $title = $hit['hierarchy']['lvl' . $highestLvl];
    $currentLvl = 0;
    $subtitle = $hit['hierarchy']['lvl0'];
    while ($currentLvl < $highestLvl) {
        $currentLvl = $currentLvl + 1;
        $subtitle = $subtitle . ' » ' . $hit['hierarchy']['lvl' . $currentLvl];
    }

    $workflow->result()
        ->uid($hit['objectID'])
        ->title($title)
        ->autocomplete($title)
        ->subtitle($subtitle)
        ->arg($hit['url'])
        ->quicklookurl($hit['url'])
        ->valid(true);
}

echo $workflow->output();
