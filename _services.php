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
     * Serve method to get number of pending posts for current blog.
     *
     * @param     core     <b>dcCore</b>     dcCore instance
     * @param     get     <b>array</b>     cleaned $_GET
     */
    public static function getPendingPostsCount($core, $get)
    {
        $count = $core->blog->getPosts(['post_status' => -2], true)->f(0);
        $str   = ($count ? sprintf(__('(%d pending post)', '(%d pending posts)', $count), $count) : '');

        $rsp      = new xmlTag('count');
        $rsp->ret = $str;
        $rsp->nb  = $count;

        return $rsp;
    }

    /**
     * Serve method to get number of pending comments for current blog.
     *
     * @param     core     <b>dcCore</b>     dcCore instance
     * @param     get     <b>array</b>     cleaned $_GET
     */
    public static function getPendingCommentsCount($core, $get)
    {
        $count = $core->blog->getComments(['comment_status' => -1], true)->f(0);
        $str   = ($count ? sprintf(__('(%d pending comment)', '(%d pending comments)', $count), $count) : '');

        $rsp      = new xmlTag('count');
        $rsp->ret = $str;
        $rsp->nb  = $count;

        return $rsp;
    }
}
