<?php
declare(strict_types=1);
/** be strict for parameter types, https://www.quora.com/Are-strict_types-in-PHP-7-not-a-bad-idea */

namespace DrdPlus\RulesSkeleton;

use DrdPlus\FrontendSkeleton\Cache;

class PassCache extends Cache
{
    protected function getCachePrefix(): string
    {
        return 'pass';
    }

}