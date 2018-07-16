<?php
declare(strict_types=1);
/** be strict for parameter types, https://www.quora.com/Are-strict_types-in-PHP-7-not-a-bad-idea */

namespace DrdPlus\Tests\FrontendSkeleton;

use DrdPlus\FrontendSkeleton\WebVersions;
use DrdPlus\Tests\FrontendSkeleton\Partials\AbstractContentTest;

class WebVersionsTest extends AbstractContentTest
{

    /**
     * @test
     */
    public function I_can_get_current_version(): void
    {
        $webVersions = new WebVersions($this->getDocumentRoot());
        self::assertSame($this->executeCommand('git rev-parse --abbrev-ref HEAD'), $webVersions->getCurrentVersion());
    }

    /**
     * @test
     */
    public function I_can_get_current_patch_version(): void
    {
        $webVersions = new WebVersions($this->getDocumentRoot());
        if ($webVersions->getCurrentVersion() === $this->getTestsConfiguration()->getExpectedLastUnstableVersion()) {
            self::assertSame(
                $this->getTestsConfiguration()->getExpectedLastUnstableVersion(),
                $webVersions->getCurrentPatchVersion()
            );
        } else {
            self::assertRegExp(
                '~^' . \preg_quote($webVersions->getCurrentVersion(), '~') . '[.]\d+$~',
                $webVersions->getCurrentPatchVersion()
            );
        }
    }

    /**
     * @test
     */
    public function I_can_ask_it_if_code_has_specific_version(): void
    {
        $webVersions = new WebVersions($this->getDocumentRoot());
        self::assertTrue($webVersions->hasVersion($this->getTestsConfiguration()->getExpectedLastUnstableVersion()));
        if ($this->getTestsConfiguration()->hasMoreVersions()) {
            self::assertTrue($webVersions->hasVersion('1.0'));
        }
        self::assertFalse($webVersions->hasVersion('-1'));
    }

    /**
     * @test
     */
    public function I_can_get_last_stable_version(): void
    {
        $webVersions = new WebVersions($this->getDocumentRoot());
        $lastStableVersion = $webVersions->getLastStableVersion();
        if (!$this->isSkeletonChecked() && !$this->getTestsConfiguration()->hasMoreVersions()) {
            self::assertSame($this->getTestsConfiguration()->getExpectedLastUnstableVersion(), $webVersions->getLastStableVersion());
        } else {
            self::assertNotSame($this->getTestsConfiguration()->getExpectedLastUnstableVersion(), $lastStableVersion);
            self::assertGreaterThanOrEqual(0, \version_compare($lastStableVersion, '1.0'));
        }
        self::assertSame(
            $this->getTestsConfiguration()->getExpectedLastVersion(),
            $lastStableVersion,
            'Tests configuration requires different version'
        );
    }

    /**
     * @test
     */
    public function I_can_get_last_unstable_version(): void
    {
        $webVersions = new WebVersions($this->getDocumentRoot());
        self::assertSame($this->getTestsConfiguration()->getExpectedLastUnstableVersion(), $webVersions->getLastUnstableVersion());
    }

    /**
     * @test
     */
    public function I_can_get_all_stable_versions(): void
    {
        $webVersions = new WebVersions($this->getDocumentRoot());
        $allVersions = $webVersions->getAllVersions();
        $expectedStableVersions = [];
        foreach ($allVersions as $version) {
            if ($version !== $this->getTestsConfiguration()->getExpectedLastUnstableVersion()) {
                $expectedStableVersions[] = $version;
            }
        }
        self::assertSame($expectedStableVersions, $webVersions->getAllStableVersions());
    }

    /**
     * @test
     */
    public function I_can_get_czech_version_name(): void
    {
        $webVersions = new WebVersions($this->getDocumentRoot());
        self::assertSame('testovací!', $webVersions->getVersionName($this->getTestsConfiguration()->getExpectedLastUnstableVersion()));
        self::assertSame('verze 1.2.3', $webVersions->getVersionName('1.2.3'));
    }

    /**
     * @test
     */
    public function I_can_get_current_commit_hash(): void
    {
        $webVersions = new WebVersions($this->getDocumentRoot());
        self::assertSame($this->getLastCommitHashFromHeadFile(), $webVersions->getCurrentCommitHash());
    }

    /**
     * @return string
     * @throws \DrdPlus\Tests\FrontendSkeleton\Exceptions\CanNotReadGitHead
     */
    private function getLastCommitHashFromHeadFile(): string
    {
        $head = \file_get_contents($this->getDocumentRoot() . '/.git/HEAD');
        if (\preg_match('~^[[:alnum:]]{40,}$~', $head)) {
            return $head; // the HEAD file contained the has itself
        }
        $gitHeadFile = \trim(\preg_replace('~ref:\s*~', '', \file_get_contents($this->getDocumentRoot() . '/.git/HEAD')));
        $gitHeadFilePath = $this->getDocumentRoot() . '/.git/' . $gitHeadFile;
        if (!\is_readable($gitHeadFilePath)) {
            throw new Exceptions\CanNotReadGitHead(
                "Could not read $gitHeadFilePath, in that dir are files "
                . \implode(',', \scandir(\dirname($gitHeadFilePath), SCANDIR_SORT_NONE))
            );
        }

        return \trim(\file_get_contents($gitHeadFilePath));
    }

    /**
     * @test
     */
    public function I_can_get_all_web_versions(): void
    {
        $webVersions = new WebVersions($this->getDocumentRoot());
        $allWebVersions = $webVersions->getAllVersions();
        self::assertNotEmpty($allWebVersions, 'At least single web version (from GIT) expected');
        if (!$this->getTestsConfiguration()->hasMoreVersions()) {
            self::assertSame([$this->getTestsConfiguration()->getExpectedLastUnstableVersion()], $allWebVersions);
        } else {
            self::assertSame($this->getBranchesFromFileSystem(), $allWebVersions);
        }
    }

    private function getBranchesFromFileSystem(): array
    {
        return $this->runCommand('ls -1 .git/logs/refs/heads/ | sort --version-sort --reverse'); // from latest to oldest
    }

    /**
     * @test
     */
    public function I_can_get_patch_versions(): void
    {
        $tags = $this->runCommand('git -C ' . \escapeshellarg($this->getDocumentRoot()) . ' tag');
        $expectedVersionTags = [];
        foreach ($tags as $tag) {
            if (\preg_match('~^(\d+[.]){2}[[:alnum:]]+([.]\d+)?$~', $tag)) {
                $expectedVersionTags[] = $tag;
            }
        }
        if (!$this->isSkeletonChecked() && !$this->getTestsConfiguration()->hasMoreVersions()) {
            self::assertCount(0, $expectedVersionTags, 'No version tags expected as there are no versions');

            return;
        }
        $webVersions = new WebVersions($this->getDocumentRoot());
        self::assertNotEmpty(
            $expectedVersionTags,
            'Some version tags expected as we have versions ' . \implode(',', $webVersions->getAllStableVersions())
        );
        $sortedExpectedVersionTags = $this->sortVersionsFromLatest($expectedVersionTags);
        self::assertSame($sortedExpectedVersionTags, $webVersions->getPatchVersions());
        $this->I_can_get_last_patch_version_for_every_stable_version($sortedExpectedVersionTags, $webVersions);
    }

    private function sortVersionsFromLatest(array $versions): array
    {
        \usort($versions, 'version_compare');

        return \array_reverse($versions);
    }

    private function I_can_get_last_patch_version_for_every_stable_version(array $expectedVersionTags, WebVersions $webVersions): void
    {
        foreach ($webVersions->getAllStableVersions() as $stableVersion) {
            $matchingPatchVersionTags = [];
            foreach ($expectedVersionTags as $expectedVersionTag) {
                if (\strpos($expectedVersionTag, $stableVersion) === 0) {
                    $matchingPatchVersionTags[] = $expectedVersionTag;
                }
            }
            self::assertNotEmpty($matchingPatchVersionTags, "Missing patch version tags for version $stableVersion");
            $sortedMatchingVersionTags = $this->sortVersionsFromLatest($matchingPatchVersionTags);
            self::assertSame(
                \reset($sortedMatchingVersionTags),
                $webVersions->getLastPatchVersionOf($stableVersion),
                "Expected different patch version tag for $stableVersion"
            );
        }
    }

    /**
     * @test
     */
    public function I_will_get_last_unstable_version_as_patch_version(): void
    {
        $webVersions = new WebVersions($this->getDocumentRoot());
        self::assertSame($webVersions->getLastUnstableVersion(), $webVersions->getLastPatchVersionOf($webVersions->getLastUnstableVersion()));
    }

    /**
     * @test
     * @expectedException \DrdPlus\FrontendSkeleton\Exceptions\NoPatchVersionsMatch
     */
    public function I_can_not_get_last_patch_version_for_non_existing_version(): void
    {
        $nonExistingVersion = '-999.999';
        $webVersions = new WebVersions($this->getDocumentRoot());
        try {
            self::assertNotContains($nonExistingVersion, $webVersions->getAllVersions(), 'This version really exists?');
        } catch (\Exception $exception) {
            self::fail('No exception expected so far: ' . $exception->getMessage());
        }
        $webVersions->getLastPatchVersionOf($nonExistingVersion);
    }
}