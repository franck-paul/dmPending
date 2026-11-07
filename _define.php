<?php

/**
 * @brief dmPending, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @author Franck Paul and contributors
 *
 * @copyright Franck Paul contact@open-time.net
 * @copyright GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
declare(strict_types=1);

if (isset($this) && is_object($this) && method_exists($this, 'registerModule') && isset($this->id) && is_string($this->id)) {
    $this->registerModule(
        'Pending Dashboard Module',
        'Display pending posts and comments on dashboard',
        'Franck Paul',
        '10.1',
        [
            'date'     => '2026-11-07T16:46:31+0100',
            'requires' => [
                ['core', '2.39'],
                ['dmHelper', '5.0'],
            ],
            'permissions' => 'My',
            'type'        => 'plugin',
            'settings'    => [
                'pref' => '#user-favorites.dmpending',
            ],

            'details'    => 'https://open-time.net/?q=dmPending',
            'support'    => 'https://github.com/franck-paul/dmPending',
            'repository' => 'https://raw.githubusercontent.com/franck-paul/dmPending/main/dcstore.xml',
            'license'    => 'gpl2',
        ]
    );
}
