<?php
global $testsConfiguration;
$testsConfiguration = new \DrdPlus\Tests\RulesSkeleton\TestsConfiguration('http://dobrodruzstvi.ppj.drdplus.loc', 'https://dobrodruzstvi.ppj.drdplus.info');
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
$testsConfiguration->setExpectedWebName('DrD+ dobrodružství');
$testsConfiguration->setExpectedPageTitle('💥 DrD+ dobrodružství');
$testsConfiguration->setExpectedGoogleAnalyticsId('UA-121206931-3');
$testsConfiguration->disableHasMoreVersions();