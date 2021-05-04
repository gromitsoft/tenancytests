<?php

/**
 * @noinspection AutoloadingIssuesInspection
 * @noinspection UnknownInspectionInspection
 * @noinspection PhpUnused
 */

namespace GromIT\TenancyTests\Updates;

use October\Rain\Database\Updates\Seeder;
use GromIT\Tenancy\Concerns\TenantAwareMigration;
use GromIT\TenancyTests\Models\Post;

class TestPostsSeeder extends Seeder
{
    use TenantAwareMigration;

    public function run(): void
    {
        $this->execute(function () {
            $post = new Post();
            $post->title = 'seeded title';
            $post->body = 'seeded body';
            $post->save();
        });
    }
}
