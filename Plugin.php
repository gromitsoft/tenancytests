<?php namespace GromIT\TenancyTests;

use System\Classes\PluginBase;

/**
 * TenancyTests Plugin Information File
 */
class Plugin extends PluginBase
{
    public $require = ['GromIT.Tenancy'];

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails(): array
    {
        return [
            'name'        => 'TenancyTests',
            'description' => 'Test plugin for GromIT.Tenancy',
            'author'      => 'GromIT',
            'icon'        => 'icon-leaf'
        ];
    }
}
