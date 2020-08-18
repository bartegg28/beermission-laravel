<?php

declare(strict_types=1);

namespace Yxvt\BeermissionLaravel\Exception;

class BearerIdTooLong extends \Exception
{
    public static function create(int $idLength, int $maxLength) {
        return new self("Bearer ID must not be longer than $maxLength chars, it was $idLength chars long.");
    }
}
