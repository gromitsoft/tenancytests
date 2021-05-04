<?php namespace GromIT\TenancyTests\Models;

use October\Rain\Database\Model;
use October\Rain\Database\Traits\Revisionable;
use GromIT\Tenancy\Concerns\UsesTenantConnection;
use GromIT\Tenancy\Models\File;
use GromIT\Tenancy\Models\Revision;

/**
 * Post Model
 *
 * @property int                                          $id
 * @property string                                       $title
 * @property string                                       $body
 * @property \October\Rain\Argon\Argon                    $created_at
 * @property \October\Rain\Argon\Argon                    $updated_at
 *
 * @property File                                         $image
 * @property \October\Rain\Database\Collection|Revision[] $revision_history
 */
class Post extends Model
{
    public $table = 'gromit_tenancytests_posts';

    use UsesTenantConnection;

    use Revisionable;

    protected $revisionable = ['title', 'body'];

    public $morphMany = [
        'revision_history' => [
            Revision::class,
            'name' => 'revisionable'
        ]
    ];

    public $attachOne = [
        'image' => File::class,
    ];
}
