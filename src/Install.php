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

use Dotclear\App;
use Dotclear\Core\Process;
use Dotclear\Interface\Core\UserWorkspaceInterface;
use Exception;

class Install extends Process
{
    public static function init(): bool
    {
        return self::status(My::checkContext(My::INSTALL));
    }

    public static function process(): bool
    {
        if (!self::status()) {
            return false;
        }

        try {
            // Update
            $old_version = App::version()->getVersion(My::id());
            if (version_compare((string) $old_version, '2.0', '<')) {
                // Rename settings workspace
                if (App::auth()->prefs()->exists('dmpending')) {
                    App::auth()->prefs()->delWorkspace(My::id());
                    App::auth()->prefs()->renWorkspace('dmpending', My::id());
                }
                // Change settings names (remove pending_ prefix in them)
                $rename = function (string $name, UserWorkspaceInterface $preferences): void {
                    if ($preferences->prefExists('pending_' . $name, true)) {
                        $preferences->rename('pending_' . $name, $name);
                    }
                };

                $preferences = My::prefs();
                if ($preferences) {
                    foreach (['posts', 'posts_nb', 'posts_large', 'comments', 'comments_nb', 'comments_large'] as $pref) {
                        $rename($pref, $preferences);
                    }
                }
            }

            // Default prefs for pending posts and comments
            $preferences = My::prefs();
            if ($preferences) {
                $preferences->put('posts', false, App::userWorkspace()::WS_BOOL, 'Display pending posts', false, true);
                $preferences->put('posts_nb', 5, App::userWorkspace()::WS_INT, 'Number of pending posts displayed', false, true);
                $preferences->put('posts_large', true, App::userWorkspace()::WS_BOOL, 'Large display', false, true);
                $preferences->put('comments', false, App::userWorkspace()::WS_BOOL, 'Display pending comments', false, true);
                $preferences->put('comments_nb', 5, App::userWorkspace()::WS_INT, 'Number of pending comments displayed', false, true);
                $preferences->put('comments_large', true, App::userWorkspace()::WS_BOOL, 'Large display', false, true);
                $preferences->put('interval', 60, App::userWorkspace()::WS_INT, 'Interval between two refreshes', false, true);
            }
        } catch (Exception $e) {
            App::error()->add($e->getMessage());
        }

        return true;
    }
}
