<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBermissionGrantsTable extends Migration
{
    private string $tableName;
    private int $bearerIdLength;

    public function __construct() {
        $this->tableName = config('beermission.grants_table_name');
        $this->bearerIdLength = config('beermission.bearer_id_length');
    }

    public function up(): void {
        Schema::create($this->tableName, function (Blueprint $blueprint): void {
            $blueprint->string('bearer_id', $this->bearerIdLength)->index();
            $blueprint->string('kind', 4)->index();
            $blueprint->string('grant');
        });
    }

    public function down(): void {
        Schema::drop($this->tableName);
    }
}
