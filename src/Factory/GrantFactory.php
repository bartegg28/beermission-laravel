<?php

declare(strict_types=1);

namespace Yxvt\BeermissionLaravel\Factory;

use Yxvt\Beermission\Entity\Permission;
use Yxvt\Beermission\Entity\Role;
use Yxvt\Beermission\Service\BuildGrantIndexService;
use Yxvt\BeermissionLaravel\Exception\UnexpectedGrant;
use Yxvt\BeermissionLaravel\Exception\UnknownGrantClass;
use Yxvt\BeermissionLaravel\Model\Grant;

class GrantFactory
{
    private \Yxvt\Beermission\Factory\GrantFactory $grantFactory;
    private BuildGrantIndexService $buildGrantIndexService;

    public function __construct(
        \Yxvt\Beermission\Factory\GrantFactory $grantFactory,
        BuildGrantIndexService $buildGrantIndexService
    ) {
        $this->grantFactory = $grantFactory;
        $this->buildGrantIndexService = $buildGrantIndexService;
    }

    public function createRoleGrant(Grant $grantModel): Role {
        if ($grantModel->kind !== Grant::KIND_ROLE) {
            throw UnexpectedGrant::create(Grant::KIND_ROLE, $grantModel->kind);
        }

        return $this->grantFactory->roleFromString($grantModel->grant);
    }

    public function createPermissionGrant(Grant $grantModel): Permission {
        if ($grantModel->kind !== Grant::KIND_PERMISSION) {
            throw UnexpectedGrant::create(Grant::KIND_PERMISSION, $grantModel->kind);
        }

        return $this->grantFactory->permissionFromString($grantModel->grant);
    }

    /**
     * @param mixed $bearerId
     * @return Grant
     */
    public function createGrantModelFromGrantEntity($bearerId, \Yxvt\Beermission\Entity\Grant $grant): Grant {
        return new Grant([
            'bearer_id' => $bearerId,
            'kind' => $this->determineGrantKind($grant),
            'grant' => $this->buildGrantIndexService->build($grant),
        ]);
    }

    private function determineGrantKind(\Yxvt\Beermission\Entity\Grant $grant): string {
        if ($grant instanceof Role) {
            return Grant::KIND_ROLE;
        }

        if ($grant instanceof Permission) {
            return Grant::KIND_PERMISSION;
        }

        throw new UnknownGrantClass(get_class($grant));
    }
}
