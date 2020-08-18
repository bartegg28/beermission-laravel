<?php

declare(strict_types=1);

namespace Yxvt\BeermissionLaravel\Exception;

use Exception;

class UnexpectedGrant extends Exception
{
    public static function create(string $expected, string $given): self {
        return new self("Expected $expected grant kind, but given $given");
    }
}
