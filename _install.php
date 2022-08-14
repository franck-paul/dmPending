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

$new_version = dcCore::app()->plugins->moduleInfo('dmPending', 'version');
$old_version = dcCore::app()->getVersion('dmPending');

if (version_compare($old_version, $new_version, '>=')) {
    return;
}

try {
    dcCore::app()->auth->user_prefs->addWorkspace('dmpending');

    // Default prefs for pending posts and comments
    dcCore::app()->auth->user_prefs->dmpending->put('pending_posts', false, 'boolean', 'Display pending posts', false, true);
    dcCore::app()->auth->user_prefs->dmpending->put('pending_posts_nb', 5, 'integer', 'Number of pending posts displayed', false, true);
    dcCore::app()->auth->user_prefs->dmpending->put('pending_posts_large', true, 'boolean', 'Large display', false, true);
    dcCore::app()->auth->user_prefs->dmpending->put('pending_comments', false, 'boolean', 'Display pending comments', false, true);
    dcCore::app()->auth->user_prefs->dmpending->put('pending_comments_nb', 5, 'integer', 'Number of pending comments displayed', false, true);
    dcCore::app()->auth->user_prefs->dmpending->put('pending_comments_large', true, 'boolean', 'Large display', false, true);

    dcCore::app()->setVersion('dmPending', $new_version);

    return true;
} catch (Exception $e) {
    dcCore::app()->error->add($e->getMessage());
}

return false;
