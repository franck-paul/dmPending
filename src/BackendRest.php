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

class BackendRest
{
    /**
     * Gets the pending posts count.
     *
     * @return     array<string, mixed>   The payload.
     */
    public static function getPendingPostsCount(): array
    {
        $count = (int) App::blog()->getPosts(['post_status' => App::status()->post()::PENDING], true)->f(0);
        $str   = ($count !== 0 ? sprintf(__('(%d pending post)', '(%d pending posts)', (int) $count), $count) : '');

        return [
            'ret' => true,
            'msg' => $str,
            'nb'  => $count,
        ];
    }

    /**
     * Gets the pending comments count.
     *
     * @return     array<string, mixed>   The payload.
     */
    public static function getPendingCommentsCount(): array
    {
        $count = (int) App::blog()->getComments(['comment_status' => App::status()->comment()::PENDING], true)->f(0);
        $str   = ($count !== 0 ? sprintf(__('(%d pending comment)', '(%d pending comments)', (int) $count), $count) : '');

        return [
            'ret' => true,
            'msg' => $str,
            'nb'  => $count,
        ];
    }
}
