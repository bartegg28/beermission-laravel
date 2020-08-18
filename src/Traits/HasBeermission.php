<?php

declare(strict_types=1);

namespace Yxvt\BeermissionLaravel\Traits;

use Yxvt\Beermission\Entity\Bearer;
use Yxvt\Beermission\Entity\Permission;
use Yxvt\Beermission\Entity\Role;
use Yxvt\Beermission\Entity\Scope;
use Yxvt\BeermissionLaravel\Contract\GrantSyncStrategy;
use Yxvt\BeermissionLaravel\Contract\IsBearer;
use Yxvt\BeermissionLaravel\Exception\ClassMustImplementIsBearerContract;
use Yxvt\BeermissionLaravel\Factory\BearerFactory;
use Yxvt\BeermissionLaravel\Model\Grant;

trait HasBeermission
{
    private Bearer $bearer;

    public function bearer(): Bearer {
        if ($this instanceof IsBearer === false) {
            throw new ClassMustImplementIsBearerContract();
        }

        if (isset($this->bearer) === false) {
            $factory = app()->make(BearerFactory::class);

            $this->bearer = $factory->create($this->getBearerId(), ...Grant::forBearer($this->getBearerId()));
        }

        return $this->bearer;
    }

    public function assignRole(string $role, string $scope, ?string $scopeValue = null): void {
        $this->bearer()->getRoles()->add(new Role($role, new Scope($scope, $scopeValue)));
    }

    public function dropRole(string $role, string $scope, ?string $scopeValue = null): void {
        $this->bearer()->getRoles()->drop(new Role($role, new Scope($scope, $scopeValue)));
    }

    public function grantPermission(string $permission, string $scope, ?string $scopeValue = null): void {
        $this->bearer()->getPermissions()->add(new Permission($permission, new Scope($scope, $scopeValue)));
    }

    public function revokePermission(string $permission, string $scope, ?string $scopeValue = null): void {
        $this->bearer()->getPermissions()->drop(new Permission($permission, new Scope($scope, $scopeValue)));
    }

    public function syncGrants(): void {
        $strategy = app()->make(GrantSyncStrategy::class);
        $strategy->sync($this->getBearerId(), $this->bearer()->getRoles(), $this->bearer->getPermissions());
    }
}
