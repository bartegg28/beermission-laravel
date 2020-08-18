<?php

declare(strict_types=1);

namespace Yxvt\BeermissionLaravel\Contract;

use Yxvt\Beermission\Entity\GrantBag;

interface GrantSyncStrategy
{
    public function sync(string $bearerId, GrantBag $roles, GrantBag $permissions): void;
}
