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
use dcCore;

class BackendRest
{
    /**
     * Gets the pending posts count.
     *
     * @return     array   The payload.
     */
    public static function getPendingPostsCount(): array
    {
        $count = dcCore::app()->blog->getPosts(['post_status' => dcBlog::POST_PENDING], true)->f(0);
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
     * @return     array   The payload.
     */
    public static function getPendingCommentsCount(): array
    {
        $count = dcCore::app()->blog->getComments(['comment_status' => dcBlog::COMMENT_PENDING], true)->f(0);
        $str   = ($count ? sprintf(__('(%d pending comment)', '(%d pending comments)', (int) $count), $count) : '');

        return [
            'ret' => true,
            'msg' => $str,
            'nb'  => $count,
        ];
    }
}
