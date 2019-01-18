<?php
declare(strict_types=1);

namespace Granam\Tests\Git;

use Granam\Git\Git;
use PHPUnit\Framework\TestCase;

class GitTest extends TestCase
{
    /**
     * @test
     */
    public function I_can_get_git_status(): void
    {
        self::assertNotEmpty($this->getGit()->getGitStatus(__DIR__), 'Expected some GIT status');
    }

    private function getGit(): Git
    {
        return new Git();
    }

    /**
     * @test
     */
    public function I_can_get_diff_against_origin(): void
    {
        self::assertIsArray($this->getGit()->getDiffAgainstOriginMaster(__DIR__));
    }

    /**
     * @test
     */
    public function I_can_get_last_commit(): void
    {
        $expectedLastCommit = \trim(\file_get_contents(__DIR__ . '/../../../.git/refs/heads/master'));
        self::assertSame($expectedLastCommit, $this->getGit()->getLastCommitHash(__DIR__));
    }

    /**
     * @test
     */
    public function I_can_ask_if_remote_branch_exists(): void
    {
        self::assertTrue(
            $this->getGit()->remoteBranchExists('master'),
            'Expected master branch to be detected as existing in remote repository'
        );
        self::assertFalse(
            $this->getGit()->remoteBranchExists('nonsense'),
            "'nonsense' branch is not expected to exists at all"
        );
    }

    /**
     * @test
     */
    public function I_can_get_all_patch_versions(): void
    {
        self::assertNotEmpty($this->getGit()->getAllPatchVersions(__DIR__));
    }

    /**
     * @test
     * @dataProvider provideBranchesSourceFlags
     * @param bool $includeLocalBranches
     * @param bool $includeRemoteBranches
     */
    public function I_can_get_all_minor_versions(bool $includeLocalBranches, bool $includeRemoteBranches): void
    {
        self::assertContains(
            '1.0',
            $this->getGit()->getAllMinorVersions(__DIR__, $includeLocalBranches, $includeRemoteBranches)
        );
    }

    public function provideBranchesSourceFlags(): array
    {
        return [
            'both local and remote branches' => [Git::INCLUDE_LOCAL_BRANCHES, Git::INCLUDE_REMOTE_BRANCHES],
            'only local branches' => [Git::INCLUDE_LOCAL_BRANCHES, Git::EXCLUDE_REMOTE_BRANCHES],
            'only remote branches' => [Git::EXCLUDE_LOCAL_BRANCHES, Git::INCLUDE_REMOTE_BRANCHES],
        ];
    }

    /**
     * @test
     * @expectedException \Granam\Git\Exceptions\LocalOrRemoteBranchesShouldBeRequired
     */
    public function I_can_not_exclude_both_local_and_remote_branches_when_asking_to_versions(): void
    {
        $this->getGit()->getAllMinorVersions(__DIR__, Git::EXCLUDE_LOCAL_BRANCHES, Git::EXCLUDE_REMOTE_BRANCHES);
    }

    /**
     * @test
     */
    public function I_can_get_last_stable_minor_version(): void
    {
        self::assertRegExp(
            '~^v?\d+[.]\d+$~',
            $this->getGit()->getLastStableMinorVersion(__DIR__),
            'Some last stable minor version expected'
        );
    }

    /**
     * @test
     */
    public function I_can_get_last_patch_version_of_minor_version(): void
    {
        self::assertRegExp(
            '~^1[.]0[.]\d+$~',
            $this->getGit()->getLastPatchVersionOf('1.0', __DIR__),
            'Some last patch version to a minor version expected'
        );
    }

    /**
     * @test
     * @expectedException \Granam\Git\Exceptions\NoPatchVersionsMatch
     * @expectedExceptionMessageRegExp ~999[.]999~
     */
    public function I_am_stopped_when_asking_for_last_patch_version_of_non_existing_minor_version(): void
    {
        $this->getGit()->getLastPatchVersionOf('999.999', __DIR__);
    }

    /**
     * @test
     */
    public function I_can_get_last_patch_version(): void
    {
        self::assertRegExp(
            '~^v?(\d+[.]){2}\d+$~',
            $this->getGit()->getLastPatchVersion(__DIR__),
            'Some last patch version expected'
        );
    }

    /**
     * @test
     */
    public function I_can_get_current_branch_name(): void
    {
        self::assertRegExp('~^(master|v?(\d+[.]){2}\d+)$~', $this->getGit()->getCurrentBranchName(__DIR__));
    }
}