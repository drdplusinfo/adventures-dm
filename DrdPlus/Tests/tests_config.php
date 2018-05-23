<?php
global $testsConfiguration;
$testsConfiguration = new \DrdPlus\Tests\RulesSkeleton\TestsConfiguration();
$testsConfiguration->setHasProtectedAccess(false);
$testsConfiguration->setHasAuthors(false);
$testsConfiguration->setCanBeBoughtOnEshop(false);
$testsConfiguration->setHasTables(false);
$testsConfiguration->setSomeExpectedTableIds([]);
$testsConfiguration->setHasLinksToJournals(false);
$testsConfiguration->setHasLinkToSingleJournal(false);