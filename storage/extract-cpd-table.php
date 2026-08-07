<?php

$html = file_get_contents(__DIR__ . '/cpd-requirements-ref.html');
$pos = strpos($html, 'features-table_product');
if ($pos === false) {
    echo "not found\n";
    exit(1);
}

$start = strrpos(substr($html, 0, $pos), '<table');
$end = strpos($html, '</table>', $pos);
$snippet = substr($html, $start, $end - $start + 8);
$snippet = html_entity_decode($snippet);
$snippet = stripcslashes($snippet);
echo $snippet;
