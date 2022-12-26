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
if (!defined('DC_CONTEXT_ADMIN')) {
    return;
}

if (!dcCore::app()->newVersion(basename(__DIR__), dcCore::app()->plugins->moduleInfo(basename(__DIR__), 'version'))) {
    return;
}

try {
    // Default prefs for pending posts and comments
    dcCore::app()->auth->user_prefs->dmpending->put('pending_posts', false, 'boolean', 'Display pending posts', false, true);
    dcCore::app()->auth->user_prefs->dmpending->put('pending_posts_nb', 5, 'integer', 'Number of pending posts displayed', false, true);
    dcCore::app()->auth->user_prefs->dmpending->put('pending_posts_large', true, 'boolean', 'Large display', false, true);
    dcCore::app()->auth->user_prefs->dmpending->put('pending_comments', false, 'boolean', 'Display pending comments', false, true);
    dcCore::app()->auth->user_prefs->dmpending->put('pending_comments_nb', 5, 'integer', 'Number of pending comments displayed', false, true);
    dcCore::app()->auth->user_prefs->dmpending->put('pending_comments_large', true, 'boolean', 'Large display', false, true);

    return true;
} catch (Exception $e) {
    dcCore::app()->error->add($e->getMessage());
}

return false;
