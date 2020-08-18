<?php

declare(strict_types=1);

namespace Yxvt\BeermissionLaravel\Contract;

interface IsBearer
{
    public function getBearerId(): string;
}
