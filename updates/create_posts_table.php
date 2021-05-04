<?php

/**
 * @noinspection AutoloadingIssuesInspection
 * @noinspection UnknownInspectionInspection
 * @noinspection PhpUnused
 */

namespace GromIT\TenancyTests\Updates;

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use October\Rain\Support\Facades\Schema;
use GromIT\Tenancy\Concerns\TenantAwareMigration;

class CreatePostsTable extends Migration
{
    use TenantAwareMigration;

    public function up(): void
    {
        $this->execute(function () {
            Schema::create('gromit_tenancytests_posts', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->increments('id');

                $table->string('title');
                $table->text('body');

                $table->timestamps();
            });
        });
    }

    public function down(): void
    {
        $this->execute(function () {
            Schema::dropIfExists('gromit_tenancytests_posts');
        });
    }
}
