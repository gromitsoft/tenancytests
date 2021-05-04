<?php namespace GromIT\TenancyTests\Models;

use October\Rain\Database\Model;

/**
 * Entry Model
 *
 * @property int                       $id
 * @property string                    $name
 * @property \October\Rain\Argon\Argon $created_at
 * @property \October\Rain\Argon\Argon $updated_at
 */
class Entry extends Model
{
    public $table = 'gromit_tenancytests_entries';

    protected $fillable = [
        'name',
    ];
}
