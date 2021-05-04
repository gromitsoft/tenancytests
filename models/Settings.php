<?php

namespace GromIT\TenancyTests\Models;

use October\Rain\Database\Model;
use System\Behaviors\SettingsModel;

class Settings extends Model
{
    public $implement = [
        SettingsModel::class,
    ];

    public $settingsCode = 'gromit_tenancytests_settings';

    public $settingsFields = [
        'fields' => [
            'name'    => [
                'label' => 'Name'
            ],
            'surname' => [
                'label' => 'Surname'
            ]
        ]
    ];
}
