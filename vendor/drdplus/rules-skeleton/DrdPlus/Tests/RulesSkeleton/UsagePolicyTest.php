<?php
declare(strict_types=1);

namespace DrdPlus\Tests\RulesSkeleton;

use DeviceDetector\Parser\Bot;
use DrdPlus\RulesSkeleton\CookiesService;
use DrdPlus\RulesSkeleton\Request;
use DrdPlus\RulesSkeleton\UsagePolicy;
use PHPUnit\Framework\TestCase;

class UsagePolicyTest extends TestCase
{
    /**
     * @test
     * @expectedException \DrdPlus\RulesSkeleton\Exceptions\ArticleNameCanNotBeEmptyForUsagePolicy
     */
    public function I_can_not_create_it_without_article_name(): void
    {
        new UsagePolicy('', new Request(new Bot()), new CookiesService());
    }

    /**
     * @test
     * @backupGlobals enabled
     */
    public function I_can_confirm_ownership_of_visitor(): void
    {
        $_COOKIE = [];
        $usagePolicy = new UsagePolicy('foo', new Request(new Bot()), new CookiesService());
        self::assertNotEmpty($_COOKIE);
        self::assertSame('confirmedOwnershipOfFoo', $_COOKIE[UsagePolicy::OWNERSHIP_COOKIE_NAME]);
        self::assertSame('trialOfFoo', $_COOKIE[UsagePolicy::TRIAL_COOKIE_NAME]);
        self::assertSame(UsagePolicy::TRIAL_EXPIRED_AT, $_COOKIE[UsagePolicy::TRIAL_EXPIRED_AT_NAME]);
        self::assertArrayNotHasKey('confirmedOwnershipOfFoo', $_COOKIE);
        $usagePolicy->confirmOwnershipOfVisitor($expiresAt = new \DateTime());
        self::assertSame((string)$expiresAt->getTimestamp(), $_COOKIE['confirmedOwnershipOfFoo']);
    }

    /**
     * @test
     * @backupGlobals enabled
     */
    public function I_can_find_out_if_trial_expired(): void
    {
        $usagePolicy = new UsagePolicy('foo', new Request(new Bot()), new CookiesService());
        self::assertFalse($usagePolicy->trialJustExpired(), 'Did not expects trial expiration yet');
        $_GET[UsagePolicy::TRIAL_EXPIRED_AT] = \time();
        self::assertTrue($usagePolicy->trialJustExpired(), 'Expected trial expiration');
        $_GET[UsagePolicy::TRIAL_EXPIRED_AT] = \time() + 2;
        self::assertFalse($usagePolicy->trialJustExpired(), 'Did not expects trial expiration as its time is in future');
        $_GET[UsagePolicy::TRIAL_EXPIRED_AT] = 0;
        self::assertFalse($usagePolicy->trialJustExpired(), 'Did not expects trial expiration now');
    }

    /**
     * @test
     * @backupGlobals enabled
     */
    public function I_can_find_out_if_visitor_is_using_valid_trial(): void
    {
        $usagePolicy = new UsagePolicy('foo', new Request(new Bot()), new CookiesService());
        unset($_GET[UsagePolicy::TRIAL_EXPIRED_AT], $_COOKIE[$usagePolicy->getTrialName()]);
        self::assertFalse($usagePolicy->isVisitorUsingValidTrial(), 'Did not expects valid trial yet');
        self::assertFalse($usagePolicy->trialJustExpired(), 'Did not expects trial expiration yet');
        $_COOKIE[$usagePolicy->getTrialName()] = true;
        self::assertTrue($usagePolicy->isVisitorUsingValidTrial(), 'Expects valid trial');
        self::assertFalse($usagePolicy->trialJustExpired(), 'Did not expects trial expiration yet');
        $_GET[UsagePolicy::TRIAL_EXPIRED_AT] = \PHP_INT_MAX;
        self::assertTrue($usagePolicy->isVisitorUsingValidTrial(), 'Expects valid trial');
        self::assertFalse($usagePolicy->trialJustExpired(), 'Did not expects trial expiration yet');
        $_GET[UsagePolicy::TRIAL_EXPIRED_AT] = \time() - 1;
        self::assertFalse($usagePolicy->isVisitorUsingValidTrial(), 'Expects trial to be valid no more');
        self::assertTrue($usagePolicy->trialJustExpired(), 'Expects trial to be expired');
    }
}