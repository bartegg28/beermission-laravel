<?php

declare(strict_types=1);

namespace Yxvt\BeermissionLaravel\Factory;

use Generator;
use Yxvt\Beermission\Entity\Bearer;
use Yxvt\Beermission\Entity\GrantBag;
use Yxvt\Beermission\Service\BuildGrantIndexService;
use Yxvt\BeermissionLaravel\Exception\UnexpectedGrant;
use Yxvt\BeermissionLaravel\Model\Grant;

class BearerFactory
{
    private GrantFactory $grantFactory;

    public function __construct(GrantFactory $grantFactory) {
        $this->grantFactory = $grantFactory;
    }

    public function create(string $bearerId, Grant ...$grantModels): Bearer {
        return new Bearer(
            $bearerId,
            new GrantBag(new BuildGrantIndexService(), ...$this->createRoleGrants($grantModels)),
            new GrantBag(new BuildGrantIndexService(), ...$this->createPermissionGrants($grantModels)),
        );
    }

    /**
     * @param array|Grant[] $grants
     * @return Generator
     * @throws UnexpectedGrant
     */
    private function createRoleGrants(array $grants): Generator {
        foreach ($grants as $grantModel) {
            if ($grantModel->kind === Grant::KIND_ROLE) {
                yield $this->grantFactory->createRoleGrant($grantModel);
            }
        }
    }

    /**
     * @param array|Grant[] $grants
     * @return Generator
     * @throws UnexpectedGrant
     */
    private function createPermissionGrants(array $grants): Generator {
        foreach ($grants as $grantModel) {
            if ($grantModel->kind === Grant::KIND_PERMISSION) {
                yield $this->grantFactory->createPermissionGrant($grantModel);
            }
        }
    }
}
