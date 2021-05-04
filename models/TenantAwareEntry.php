<?php namespace GromIT\TenancyTests\Models;

use GromIT\Tenancy\Behaviors\TenantAwareModel;
use October\Rain\Database\Model;

/**
 * Entry Model
 *
 * @property int                       $id
 * @property string                    $name
 * @property \October\Rain\Argon\Argon $created_at
 * @property \October\Rain\Argon\Argon $updated_at
 */
class TenantAwareEntry extends Model
{
    public $table = 'gromit_tenancytests_entries';

    public $implement = [
        TenantAwareModel::class,
    ];

    protected $fillable = [
        'name',
    ];
}
