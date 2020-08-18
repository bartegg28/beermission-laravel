<?php

declare(strict_types=1);

namespace Yxvt\BeermissionLaravel\Test;

use Yxvt\Beermission\Entity\Permission;
use Yxvt\Beermission\Entity\Role;
use Yxvt\Beermission\Entity\Scope;
use Yxvt\Beermission\Service\BuildGrantIndexService;
use Yxvt\BeermissionLaravel\Exception\InvalidGrantKind;
use Yxvt\BeermissionLaravel\Exception\InvalidStringifiedGrant;
use Yxvt\BeermissionLaravel\Factory\GrantFactory;
use Yxvt\BeermissionLaravel\Model\Grant;

class GrantModelTest extends TestCase
{
    private GrantFactory $grantFactory;
    private BuildGrantIndexService $buildIndexService;

    protected function setUp(): void {
        parent::setUp();

        $this->grantFactory = app()->make(GrantFactory::class);
        $this->buildIndexService = app()->make(BuildGrantIndexService::class);
    }

    public function testGrantModelCanPersistValidGrants(): void {
        $this->grantFactory->createGrantModelFromGrantEntity(
            'BearerId',
            new Role('Role', new Scope('RoleScope', 'RoleScopeValue'))
        )->save();

        $this->grantFactory->createGrantModelFromGrantEntity(
            'BearerId',
            new Permission('Permission', new Scope('PermissionScope', 'PermissionScopeValue'))
        )->save();

        $this->assertDatabaseCount(config('beermission.grants_table_name'), 2);
    }

    public function testGrantModelThrowsExceptionWhenPersistingInvalidKind(): void {
        $this->expectException(InvalidGrantKind::class);

        Grant::create([
            'bearer_id' => 'BearerId',
            'kind' => 'unknwon_grant',
            'grant' => $this->buildIndexService->build(new Permission('Permission', new Scope('Scope', 'Value')))
        ]);
    }

    public function testGrantModelThrowsExceptionWhenPersistingGrantWithInvalidFormat(): void {
        $this->expectException(InvalidStringifiedGrant::class);

        Grant::create([
            'bearer_id' => 'BearerId',
            'kind' => Grant::KIND_PERMISSION,
            'grant' => 'invalid-format'
        ]);
    }
}
