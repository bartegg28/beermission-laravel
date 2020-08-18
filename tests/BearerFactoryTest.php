<?php

declare(strict_types=1);

namespace Yxvt\BeermissionLaravel\Test;

use Yxvt\Beermission\Entity\Bearer;
use Yxvt\Beermission\Entity\Permission;
use Yxvt\Beermission\Entity\Role;
use Yxvt\Beermission\Entity\Scope;
use Yxvt\BeermissionLaravel\Factory\BearerFactory;
use Yxvt\BeermissionLaravel\Factory\GrantFactory;
use Yxvt\BeermissionLaravel\Model\Grant;

class BearerFactoryTest extends TestCase
{
    private BearerFactory $bearerFactory;
    private GrantFactory $grantFactory;

    protected function setUp(): void {
        parent::setUp();

        $this->bearerFactory = app()->make(BearerFactory::class);
        $this->grantFactory = app()->make(GrantFactory::class);
    }

    public function testBearerFactoryCreatesBearerWithoutAnyGrants(): void {
        $bearer = $this->bearerFactory->create('bearerWithoutAnyGrants');

        $this->assertEquals('bearerWithoutAnyGrants', $bearer->getId());
        $this->assertCount(0, $bearer->getRoles()->toArray());
        $this->assertCount(0, $bearer->getPermissions()->toArray());
    }

    public function testBearerFactoryCreatesBearerWithSomeGrants(): void {
        $this->grantFactory
            ->createGrantModelFromGrantEntity(
                'bearerWithSomeGrants',
                new Role('r', new Scope('s', 'v'))
            )
            ->save();

        $this->grantFactory
            ->createGrantModelFromGrantEntity(
                'bearerWithSomeGrants',
                new Permission('p', new Scope('s', 'v'))
            )
            ->save();

        $bearer = $this->bearerFactory->create(
            'bearerWithSomeGrants',
            ...Grant::forBearer('bearerWithSomeGrants')
        );

        $this->assertEquals('bearerWithSomeGrants', $bearer->getId());
        $this->assertCount(1, $bearer->getRoles()->toArray());
        $this->assertCount(1, $bearer->getPermissions()->toArray());
    }
}
