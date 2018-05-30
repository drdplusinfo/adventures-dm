<?php
global $testsConfiguration;
$testsConfiguration = new \DrdPlus\Tests\RulesSkeleton\TestsConfiguration();
$testsConfiguration->disableHasProtectedAccess();
$testsConfiguration->disableHasAuthors();
$testsConfiguration->disableCanBeBoughtOnEshop();
$testsConfiguration->disableHasTables();
$testsConfiguration->disableHasLinksToJournals();
$testsConfiguration->disableHasLinkToSingleJournal();
$testsConfiguration->disableHasDebugContacts();
$testsConfiguration->disableHasCustomBodyContent();
$testsConfiguration->setSomeExpectedTableIds([]);
$testsConfiguration->setBlockNamesToExpectedContent([]);
