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

class dmPendingRest
{
    /**
     * Gets the pending posts count.
     *
     * @param      array   $get    The get
     *
     * @return     xmlTag  The pending posts count.
     */
    public static function getPendingPostsCount($get)
    {
        $count = dcCore::app()->blog->getPosts(['post_status' => -2], true)->f(0);
        $str   = ($count ? sprintf(__('(%d pending post)', '(%d pending posts)', $count), $count) : '');

        return [
            'ret' => true,
            'msg' => $str,
            'nb'  => $count,
        ];
    }

    /**
     * Gets the pending comments count.
     *
     * @param      array   $get    The get
     *
     * @return     xmlTag  The pending comments count.
     */
    public static function getPendingCommentsCount($get)
    {
        $count = dcCore::app()->blog->getComments(['comment_status' => -1], true)->f(0);
        $str   = ($count ? sprintf(__('(%d pending comment)', '(%d pending comments)', $count), $count) : '');

        return [
            'ret' => true,
            'msg' => $str,
            'nb'  => $count,
        ];
    }
}
