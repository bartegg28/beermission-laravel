<?php

declare(strict_types=1);

namespace Yxvt\BeermissionLaravel\Test;

use Yxvt\Beermission\Entity\Permission;
use Yxvt\Beermission\Entity\Role;
use Yxvt\Beermission\Entity\Scope;
use Yxvt\BeermissionLaravel\Factory\GrantFactory;

class UserAsBearerTest extends TestCase
{
    private GrantFactory $grantFactory;

    public function setUp(): void {
        parent::setUp();

        $this->grantFactory = app()->make(GrantFactory::class);
    }

    public function testUserReadsItsGrants(): void {
        $this->grantFactory->createGrantModelFromGrantEntity('bearer', new Role('r', new Scope('s', 'v')))->save();
        $this->grantFactory->createGrantModelFromGrantEntity('bearer', new Permission('p', new Scope('s', 'v')))->save();

        /** @var User $u */
        $u = User::create(['bearer_id' => 'bearer', 'name' => 'User']);

        $this->assertCount(1, $u->bearer()->getPermissions()->toArray());
        $this->assertCount(1, $u->bearer()->getRoles()->toArray());
    }

    public function testUserCanAddRolesAndPermissions(): void {
        $this->assertDatabaseCount(config('beermission.grants_table_name'), 0);

        /** @var User $u */
        $u = User::create(['bearer_id' => 'bearer', 'name' => 'User']);
        $u->grantPermission('permission', 'scope', 'scopeValue');
        $u->assignRole('role', 'scope', 'scopeValue');
        $u->syncGrants();

        $this->assertDatabaseCount(config('beermission.grants_table_name'), 2);
    }

    public function testUserCanDropRolesAndPermissions(): void {
        $this->grantFactory->createGrantModelFromGrantEntity('bearer', new Role('r', new Scope('s', 'v')))->save();
        $this->grantFactory->createGrantModelFromGrantEntity('bearer', new Role('r', new Scope('s')))->save();
        $this->grantFactory->createGrantModelFromGrantEntity('bearer', new Permission('p', new Scope('s', 'v')))->save();
        $this->grantFactory->createGrantModelFromGrantEntity('bearer', new Permission('p', new Scope('s')))->save();

        /** @var User $u */
        $u = User::create(['bearer_id' => 'bearer', 'name' => 'User']);
        $u->dropRole('r', 's', 'v');
        $u->dropRole('r', 's');
        $u->revokePermission('p', 's', 'v');
        $u->revokePermission('p', 's');
        $u->syncGrants();

        $this->assertDatabaseCount(config('beermission.grants_table_name'), 0);
    }
}
