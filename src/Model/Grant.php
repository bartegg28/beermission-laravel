<?php

declare(strict_types=1);

namespace Yxvt\BeermissionLaravel\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Yxvt\Beermission\Service\ValidateStringifiedGrantService;
use Yxvt\BeermissionLaravel\Exception\BearerIdTooLong;
use Yxvt\BeermissionLaravel\Exception\InvalidGrantKind;
use Yxvt\BeermissionLaravel\Exception\InvalidStringifiedGrant;

class Grant extends Model
{
    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'string';

    public const KIND_PERMISSION = 'perm';
    public const KIND_ROLE = 'role';

    protected $fillable = [
        'bearer_id',
        'kind',
        'grant',
    ];

    /**
     * @param string $bearerId
     * @return Collection
     */
    public static function forBearer(string $bearerId): Collection {
        return Grant::where('bearer_id', $bearerId)->get();
    }

    protected static function boot() {
        parent::boot();

        self::saving(static function (Grant $grant): void {
            if (in_array($grant->kind, [self::KIND_ROLE, self::KIND_PERMISSION], true) === false) {
                throw InvalidGrantKind::create($grant->kind);
            }

            $validationService = app()->make(ValidateStringifiedGrantService::class);

            if ($validationService->isValid($grant->grant) === false) {
                throw new InvalidStringifiedGrant($grant->grant);
            }

            $currentBearerIdLength = strlen($grant->bearer_id);
            $maximumBearerIdLength = config('beermission.bearer_id_length');

            if ($currentBearerIdLength > $maximumBearerIdLength) {
                throw BearerIdTooLong::create(
                    $currentBearerIdLength,
                    $maximumBearerIdLength
                );
            }
        });
    }

    public function getTable() {
        return config('beermission.grants_table_name');
    }

    public function getPrimaryKey(): string {
        return 'bearer_id';
    }
}
