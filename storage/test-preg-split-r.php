<?php
$line = 'open-course|公开课';
echo 'explode count: ' . count(explode('|', $line, 2)) . PHP_EOL;
var_export(explode('|', $line, 2));
echo PHP_EOL;
$raw = "china-online|中文直播\nchina-offline|中文线下\nenglish-online|英文线上\nopen-course|公开课";
echo 'preg_split R count: ' . count(preg_split('/\R/', trim($raw))) . PHP_EOL;
var_export(preg_split('/\R/', trim($raw)));
echo PHP_EOL;
