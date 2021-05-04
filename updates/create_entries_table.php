<?php

/**
 * @noinspection AutoloadingIssuesInspection
 * @noinspection PhpUnused
 * @noinspection UnknownInspectionInspection
 */

namespace GromIT\TenancyTests\Updates;

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use October\Rain\Support\Facades\Schema;

class CreateEntriesTable extends Migration
{
    public function up(): void
    {
        Schema::create('gromit_tenancytests_entries', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->string('name');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gromit_tenancytests_entries');
    }
}
