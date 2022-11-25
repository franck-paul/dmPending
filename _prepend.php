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

// Public and Admin mode

if (!defined('DC_CONTEXT_ADMIN')) {
    return false;
}

// Admin mode

Clearbricks::lib()->autoload(['dmPendingRest' => __DIR__ . '/_services.php']);

// Register REST methods
dcCore::app()->rest->addFunction('dmPendingPostsCount', [dmPendingRest::class, 'getPendingPostsCount']);
dcCore::app()->rest->addFunction('dmPendingCommentsCount', [dmPendingRest::class, 'getPendingCommentsCount']);
