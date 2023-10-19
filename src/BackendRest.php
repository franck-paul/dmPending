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

use dcBlog;
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
        $count = App::blog()->getPosts(['post_status' => dcBlog::POST_PENDING], true)->f(0);
        $str   = ($count ? sprintf(__('(%d pending post)', '(%d pending posts)', (int) $count), $count) : '');

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
        $count = App::blog()->getComments(['comment_status' => dcBlog::COMMENT_PENDING], true)->f(0);
        $str   = ($count ? sprintf(__('(%d pending comment)', '(%d pending comments)', (int) $count), $count) : '');

        return [
            'ret' => true,
            'msg' => $str,
            'nb'  => $count,
        ];
    }
}
