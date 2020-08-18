<?php

declare(strict_types=1);

namespace Yxvt\BeermissionLaravel\Test;

use Yxvt\Beermission\Entity\GrantBag;
use Yxvt\Beermission\Entity\Permission;
use Yxvt\Beermission\Entity\Role;
use Yxvt\Beermission\Entity\Scope;
use Yxvt\Beermission\Service\BuildGrantIndexService;
use Yxvt\BeermissionLaravel\Factory\GrantFactory;
use Yxvt\BeermissionLaravel\GrantSyncStrategy\ReinsertSyncStrategy;

class ReinsertSyncStrategyTest extends TestCase
{
    private ReinsertSyncStrategy $strategy;
    private GrantFactory $grantFactory;

    protected function setUp(): void {
        parent::setUp();

        $this->strategy = app()->make(ReinsertSyncStrategy::class);
        $this->grantFactory = app()->make(GrantFactory::class);
    }

    public function testStrategySynchronizesGrants(): void {
        $this->grantFactory->createGrantModelFromGrantEntity('bearerId', new Role('oldRole', new Scope('oldScope')))->save();
        $this->grantFactory->createGrantModelFromGrantEntity('bearerId', new Permission('oldPermission', new Scope('oldScope')))->save();

        $this->strategy->sync(
            'bearerId',
            new GrantBag(
                new BuildGrantIndexService(),
                new Role('oldRole', new Scope('oldScope')),
                new Role('newRole', new Scope('oldScope'))
            ),
            new GrantBag(
                new BuildGrantIndexService(),
                new Permission('oldPermission', new Scope('oldScope')),
                new Permission('newPermission', new Scope('newScope'))
            )
        );

        $this->assertDatabaseCount(config('beermission.grants_table_name'), 4);
    }
}
