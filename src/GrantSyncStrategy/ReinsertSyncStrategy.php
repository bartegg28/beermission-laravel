<?php

declare(strict_types=1);

namespace Yxvt\BeermissionLaravel\GrantSyncStrategy;

use Yxvt\Beermission\Entity\GrantBag;
use Yxvt\BeermissionLaravel\Contract\GrantSyncStrategy;
use Yxvt\BeermissionLaravel\Factory\GrantFactory;
use Yxvt\BeermissionLaravel\Model\Grant;

class ReinsertSyncStrategy implements GrantSyncStrategy
{
    private GrantFactory $grantFactory;

    public function __construct(GrantFactory $grantFactory) {
        $this->grantFactory = $grantFactory;
    }

    public function sync(string $bearerId, GrantBag $roles, GrantBag $permissions): void {
        $grantsToBeInserted = [];

        foreach ($roles->toArray() as $permission) {
            $grantsToBeInserted[] = $this->grantFactory->createGrantModelFromGrantEntity($bearerId, $permission)->toArray();
        }

        foreach ($permissions->toArray() as $permission) {
            $grantsToBeInserted[] = $this->grantFactory->createGrantModelFromGrantEntity($bearerId, $permission)->toArray();
        }

        Grant::where('bearer_id', $bearerId)->delete();
        Grant::query()->insert($grantsToBeInserted);
    }
}
