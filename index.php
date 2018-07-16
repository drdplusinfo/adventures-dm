<?php
$documentRoot = $documentRoot ?? (PHP_SAPI !== 'cli' ? \rtrim(\dirname($_SERVER['SCRIPT_FILENAME']), '\/') : \getcwd());
$googleAnalyticsId = 'UA-121206931-3';
$hasFreeAccess = true;

require __DIR__ . '/vendor/drd-plus/rules-skeleton/index.php';