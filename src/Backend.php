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
declare(strict_types=1);

namespace Dotclear\Plugin\dmPending;

use dcCore;
use dcNsProcess;

class Backend extends dcNsProcess
{
    protected static $init = false; /** @deprecated since 2.27 */
    public static function init(): bool
    {
        static::$init = My::checkContext(My::BACKEND);

        // dead but useful code, in order to have translations
        __('Pending Dashboard Module') . __('Display pending posts and comments on dashboard');

        return static::$init;
    }

    public static function process(): bool
    {
        if (!static::$init) {
            return false;
        }

        // Dashboard behaviours
        dcCore::app()->addBehaviors([
            'adminDashboardContentsV2' => [BackendBehaviors::class, 'adminDashboardContents'],
            'adminDashboardHeaders'    => [BackendBehaviors::class, 'adminDashboardHeaders'],
            'adminDashboardFavsIconV2' => [BackendBehaviors::class, 'adminDashboardFavsIcon'],

            'adminAfterDashboardOptionsUpdate' => [BackendBehaviors::class, 'adminAfterDashboardOptionsUpdate'],
            'adminDashboardOptionsFormV2'      => [BackendBehaviors::class, 'adminDashboardOptionsForm'],
        ]);

        // Register REST methods
        dcCore::app()->rest->addFunction('dmPendingPostsCount', [BackendRest::class, 'getPendingPostsCount']);
        dcCore::app()->rest->addFunction('dmPendingCommentsCount', [BackendRest::class, 'getPendingCommentsCount']);

        return true;
    }
}
