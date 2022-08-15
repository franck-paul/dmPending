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
if (!defined('DC_RC_PATH')) {
    return;
}

$this->registerModule(
    'Pending Dashboard Module',                        // Name
    'Display pending posts and comments on dashboard', // Description
    'Franck Paul',                                     // Author
    '0.8.1',
    [
        'requires'    => [['core', '2.23']],
        'permissions' => 'admin',                                    // Permissions
        'type'        => 'plugin',                                   // Type
        'settings'    => [                                           // Settings
            'pref' => '#user-favorites.dmpending',
        ],

        'details'    => 'https://open-time.net/?q=dmPending',       // Details URL
        'support'    => 'https://github.com/franck-paul/dmPending', // Support URL
        'repository' => 'https://raw.githubusercontent.com/franck-paul/dmPending/master/dcstore.xml',
    ]
);
