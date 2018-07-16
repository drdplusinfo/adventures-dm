<?php
$documentRoot = $documentRoot ?? (PHP_SAPI !== 'cli' ? \rtrim(\dirname($_SERVER['SCRIPT_FILENAME']), '\/') : \getcwd());

/** @noinspection PhpIncludeInspection */
require_once $documentRoot . '/vendor/drd-plus/rules-skeleton/parts/rules-skeleton/safe_autoload.php';

$dirs = new \DrdPlus\RulesSkeleton\Dirs($documentRoot);
$controller = $controller ?? new \DrdPlus\RulesSkeleton\RulesController(
        'UA-121206931-3',
        \DrdPlus\RulesSkeleton\HtmlHelper::createFromGlobals($documentRoot),
        $dirs
    );
$controller->setFreeAccess();

require __DIR__ . '/vendor/drd-plus/rules-skeleton/index.php';