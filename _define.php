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

if (!defined('DC_RC_PATH')) {return;}

$this->registerModule(
    "Pending Dashboard Module",                        // Name
    "Display pending posts and comments on dashboard", // Description
    "Franck Paul",                                     // Author
    '0.5',                                             // Version
    [
        'requires'    => [['core', '2.15']],
        'permissions' => 'admin',                                 // Permissions
        'type'        => 'plugin',                                // Type
        'support'     => 'https://open-time.net/?q=dmPending',    // Support URL
        'settings'    => ['pref' => '#user-favorites.dmpending'] // Settings
    ]
);
