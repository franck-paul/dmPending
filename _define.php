<?php
/**
 * @brief dmPending, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @author Franck Paul and contributors
 *
 * @copyright Franck Paul carnet.franck.paul@gmail.com
 * @copyright GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
$this->registerModule(
    'Pending Dashboard Module',
    'Display pending posts and comments on dashboard',
    'Franck Paul',
    '2.3',
    [
        'requires'    => [['core', '2.26']],
        'permissions' => dcCore::app()->auth->makePermissions([
            dcAuth::PERMISSION_ADMIN,
        ]),
        'type'     => 'plugin',
        'settings' => [
            'pref' => '#user-favorites.dmpending',
        ],

        'details'    => 'https://open-time.net/?q=dmPending',
        'support'    => 'https://github.com/franck-paul/dmPending',
        'repository' => 'https://raw.githubusercontent.com/franck-paul/dmPending/master/dcstore.xml',
    ]
);
