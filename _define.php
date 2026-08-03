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
$this->registerModule(
    'Pending Dashboard Module',
    'Display pending posts and comments on dashboard',
    'Franck Paul',
    '10.0',
    [
        'date'     => '2026-08-03T09:53:28+0200',
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
