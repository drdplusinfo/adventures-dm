<?php
$documentRoot = $documentRoot ?? (PHP_SAPI !== 'cli' ? \rtrim(\dirname($_SERVER['SCRIPT_FILENAME']), '\/') : \getcwd());

/** @noinspection PhpIncludeInspection */
require_once $documentRoot . '/vendor/autoload.php';

$controller = $controller ?? new \DrdPlus\RulesSkeleton\RulesController(
        'UA-121206931-3',
        \DrdPlus\RulesSkeleton\HtmlHelper::createFromGlobals($documentRoot),
        new \DrdPlus\RulesSkeleton\Dirs($documentRoot)
    );
$controller->setFreeAccess();

require __DIR__ . '/vendor/drd-plus/rules-skeleton/index.php';