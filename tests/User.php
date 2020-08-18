<?php

declare(strict_types=1);

namespace Yxvt\BeermissionLaravel\Test;

use Illuminate\Database\Eloquent\Model;
use Yxvt\BeermissionLaravel\Contract\IsBearer;
use Yxvt\BeermissionLaravel\Traits\HasBeermission;

class User extends Model implements IsBearer
{
    use HasBeermission;

    public $timestamps = false;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'bearer_id'
    ];

    public function getBearerId(): string {
        return $this->bearer_id;
    }
}
