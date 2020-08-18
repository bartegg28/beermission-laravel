<?php

declare(strict_types=1);

namespace Yxvt\BeermissionLaravel\Exception;

use Yxvt\BeermissionLaravel\Model\Grant;

class InvalidGrantKind extends \Exception
{
    public static function create(string $kind): self {
        $roleGrant = Grant::KIND_ROLE;
        $permissionGrant = Grant::KIND_PERMISSION;

        return new self("Invalid kind provided. Expected either $roleGrant or $permissionGrant, $kind given.");
    }
}
