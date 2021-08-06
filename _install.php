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

$new_version = $core->plugins->moduleInfo('dmPending', 'version');
$old_version = $core->getVersion('dmPending');

if (version_compare($old_version, $new_version, '>=')) {
    return;
}

try {
    $core->auth->user_prefs->addWorkspace('dmpending');

    // Default prefs for pending posts and comments
    $core->auth->user_prefs->dmpending->put('pending_posts', false, 'boolean', 'Display pending posts', false, true);
    $core->auth->user_prefs->dmpending->put('pending_posts_nb', 5, 'integer', 'Number of pending posts displayed', false, true);
    $core->auth->user_prefs->dmpending->put('pending_posts_large', true, 'boolean', 'Large display', false, true);
    $core->auth->user_prefs->dmpending->put('pending_comments', false, 'boolean', 'Display pending comments', false, true);
    $core->auth->user_prefs->dmpending->put('pending_comments_nb', 5, 'integer', 'Number of pending comments displayed', false, true);
    $core->auth->user_prefs->dmpending->put('pending_comments_large', true, 'boolean', 'Large display', false, true);

    $core->setVersion('dmPending', $new_version);

    return true;
} catch (Exception $e) {
    $core->error->add($e->getMessage());
}

return false;
