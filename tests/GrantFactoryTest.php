<?php

declare(strict_types=1);

namespace Yxvt\BeermissionLaravel\Test;

use Yxvt\Beermission\Entity\Grant;
use Yxvt\Beermission\Entity\Permission;
use Yxvt\Beermission\Entity\Role;
use Yxvt\Beermission\Entity\Scope;
use Yxvt\Beermission\Service\BuildGrantIndexService;
use Yxvt\BeermissionLaravel\Exception\UnexpectedGrant;
use Yxvt\BeermissionLaravel\Factory\GrantFactory;
use Yxvt\BeermissionLaravel\Model\Grant as GrantModel;

class GrantFactoryTest extends TestCase
{
    private static GrantFactory $grantFactory;

    public static function setUpBeforeClass(): void {
        parent::setUpBeforeClass();

        static::$grantFactory = app()->make(GrantFactory::class);
    }

    public function testGrantFactoryCreatesPermissionFromTheGrantModel(): void {
        $permissionEntity = new Permission('p', new Scope('s', 'v'));

        $resolvedPermission = static::$grantFactory->createPermissionGrant(
            $this->createGrantModel(GrantModel::KIND_PERMISSION, 'p', 's', 'v')
        );

        $this->assertEquals($resolvedPermission->name(), $permissionEntity->name());
        $this->assertTrue($resolvedPermission->scope()->eq($permissionEntity->scope()));
    }

    public function testGrantFactoryCreatesRoleFromTheGrantModel(): void {
        $roleEntity = new Role('r', new Scope('s', 'v'));

        $resolvedRole = static::$grantFactory->createRoleGrant(
            $this->createGrantModel(GrantModel::KIND_ROLE, 'r', 's', 'v')
        );

        $this->assertEquals($resolvedRole->name(), $roleEntity->name());
        $this->assertTrue($resolvedRole->scope()->eq($roleEntity->scope()));
    }

    /** @dataProvider invalidRoleGrantProvider */
    public function testGrantFactoryThrowsExceptionWhenCreatingRoleFromInvalidGrantModel(GrantModel $grantModel): void {
        $this->expectException(UnexpectedGrant::class);
        static::$grantFactory->createRoleGrant($grantModel);
    }

    public function invalidRoleGrantProvider(): array {
        return [
            'Permission grant' => [$this->createGrantModel(GrantModel::KIND_PERMISSION, 'p', 's', 'v')],
            'Unknown grant' => [$this->createGrantModel('unknown', 'p', 's', 'v')],
        ];
    }

    /** @dataProvider invalidPermissionGrantProvider */
    public function testGrantFactoryThrowsExceptionWhenCreatingPermissionFromInvalidGrantModel(GrantModel $grantModel): void {
        $this->expectException(UnexpectedGrant::class);
        static::$grantFactory->createPermissionGrant($grantModel);
    }

    public function invalidPermissionGrantProvider(): array {
        return [
            'Role grant' => [$this->createGrantModel(GrantModel::KIND_ROLE, 'p', 's', 'v')],
            'Unknown grant' => [$this->createGrantModel('unknown', 'p', 's', 'v')],
        ];
    }

    private function createGrantModel(string $kind, string $name, string $scope, string $scopeValue): GrantModel {
        $buildIndexService = app()->make(BuildGrantIndexService::class);

        $grantEntity = $this->createMock(Grant::class);
        $grantEntity->method('name')->willReturn($name);
        $grantEntity->method('scope')->willReturn(new Scope($scope, $scopeValue));

        return new GrantModel([
            'bearer_id' => 'someBearerId',
            'kind' => $kind,
            'grant' => $buildIndexService->build($grantEntity)
        ]);
    }
}
