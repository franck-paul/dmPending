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

if (!defined('DC_CONTEXT_ADMIN')) {return;}

// dead but useful code, in order to have translations
__('Pending Dashboard Module') . __('Display pending posts and comments on dashboard');

// Dashboard behaviours
$core->addBehavior('adminDashboardContents', ['dmPendingBehaviors', 'adminDashboardContents']);
$core->addBehavior('adminDashboardHeaders', ['dmPendingBehaviors', 'adminDashboardHeaders']);
$core->addBehavior('adminDashboardFavsIcon', ['dmPendingBehaviors', 'adminDashboardFavsIcon']);

$core->addBehavior('adminAfterDashboardOptionsUpdate', ['dmPendingBehaviors', 'adminAfterDashboardOptionsUpdate']);
$core->addBehavior('adminDashboardOptionsForm', ['dmPendingBehaviors', 'adminDashboardOptionsForm']);

# BEHAVIORS
class dmPendingBehaviors
{
    private static function getPendingPosts($core, $nb, $large)
    {
        // Get last $nb pending posts
        $params = ['post_status' => -2];
        if ((integer) $nb > 0) {
            $params['limit'] = (integer) $nb;
        }
        $rs = $core->blog->getPosts($params, false);
        if (!$rs->isEmpty()) {
            $ret = '<ul>';
            while ($rs->fetch()) {
                $ret .= '<li class="line" id="dmpp' . $rs->post_id . '">';
                $ret .= '<a href="post.php?id=' . $rs->post_id . '">' . $rs->post_title . '</a>';
                if ($large) {
                    $ret .= ' (' .
                    __('by') . ' ' . $rs->user_id . ' ' . __('on') . ' ' .
                    dt::dt2str($core->blog->settings->system->date_format, $rs->post_dt) . ' ' .
                    dt::dt2str($core->blog->settings->system->time_format, $rs->post_dt) . ')';
                }
                $ret .= '</li>';
            }
            $ret .= '</ul>';
            $ret .= '<p><a href="posts.php?status=-2">' . __('See all pending posts') . '</a></p>';
            return $ret;
        } else {
            return '<p>' . __('No pending post') . '</p>';
        }
    }

    private static function countPendingPosts($core)
    {
        $count = $core->blog->getPosts(['post_status' => -2], true)->f(0);
        if ($count) {
            $str = sprintf(__('(%d pending post)', '(%d pending posts)', $count), $count);
            return '</span></a> <a href="posts.php?status=-2"><span class="db-icon-title-dm-pending">' . sprintf($str, $count);
        } else {
            return '';
        }
    }

    private static function getPendingComments($core, $nb, $large)
    {
        // Get last $nb pending comments
        $params = ['comment_status' => -1];
        if ((integer) $nb > 0) {
            $params['limit'] = (integer) $nb;
        }
        $rs = $core->blog->getComments($params, false);
        if (!$rs->isEmpty()) {
            $ret = '<ul>';
            while ($rs->fetch()) {
                $ret .= '<li class="line" id="dmpc' . $rs->comment_id . '">';
                $ret .= '<a href="comment.php?id=' . $rs->comment_id . '">' . $rs->post_title . '</a>';
                if ($large) {
                    $ret .= ' (' .
                    __('by') . ' ' . $rs->comment_author . ' ' . __('on') . ' ' .
                    dt::dt2str($core->blog->settings->system->date_format, $rs->comment_dt) . ' ' .
                    dt::dt2str($core->blog->settings->system->time_format, $rs->comment_dt) . ')';
                }
                $ret .= '</li>';
            }
            $ret .= '</ul>';
            $ret .= '<p><a href="comments.php?status=-1">' . __('See all pending comments') . '</a></p>';
            return $ret;
        } else {
            return '<p>' . __('No pending comment') . '</p>';
        }
    }

    private static function countPendingComments($core)
    {
        $count = $core->blog->getComments(['comment_status' => -1], true)->f(0);
        if ($count) {
            $str = sprintf(__('(%d pending comment)', '(%d pending comments)', $count), $count);
            return '</span></a> <a href="comments.php?status=-1"><span class="db-icon-title-dm-pending">' . sprintf($str, $count);
        } else {
            return '';
        }
    }

    public static function adminDashboardHeaders()
    {
        global $core;

        $core->auth->user_prefs->addWorkspace('dmpending');

        return
        '<script type="text/javascript">' . "\n" .
        dcPage::jsVar('dotclear.dmPendingPosts_Counter', $core->auth->user_prefs->dmpending->pending_posts_count) .
        dcPage::jsVar('dotclear.dmPendingComments_Counter', $core->auth->user_prefs->dmpending->pending_posts_count) .
        "</script>\n" .
        dcPage::jsLoad(urldecode(dcPage::getPF('dmPending/js/service.js')), $core->getVersion('dmPending'));
    }

    public static function adminDashboardFavsIcon($core, $name, $icon)
    {
        $core->auth->user_prefs->addWorkspace('dmpending');
        if ($core->auth->user_prefs->dmpending->pending_posts_count && $name == 'posts') {
            // Hack posts title if there is at least one pending post
            $str = dmPendingBehaviors::countPendingPosts($core);
            if ($str != '') {
                $icon[0] .= $str;
            }
        }
        if ($core->auth->user_prefs->dmpending->pending_comments_count && $name == 'comments') {
            // Hack comments title if there is at least one comment
            $str = dmPendingBehaviors::countPendingComments($core);
            if ($str != '') {
                $icon[0] .= $str;
            }
        }
    }

    public static function adminDashboardContents($core, $contents)
    {
        // Add large modules to the contents stack
        $core->auth->user_prefs->addWorkspace('dmpending');
        if ($core->auth->user_prefs->dmpending->pending_posts) {
            $class = ($core->auth->user_prefs->dmpending->pending_posts_large ? 'medium' : 'small');
            $ret   = '<div id="pending-posts" class="box ' . $class . '">' .
            '<h3>' . '<img src="' . urldecode(dcPage::getPF('dmPending/icon.png')) . '" alt="" />' . ' ' . __('Pending posts') . '</h3>';
            $ret .= dmPendingBehaviors::getPendingPosts($core,
                $core->auth->user_prefs->dmpending->pending_posts_nb,
                $core->auth->user_prefs->dmpending->pending_posts_large);
            $ret .= '</div>';
            $contents[] = new ArrayObject([$ret]);
        }
        if ($core->auth->user_prefs->dmpending->pending_comments) {
            $class = ($core->auth->user_prefs->dmpending->pending_comments_large ? 'medium' : 'small');
            $ret   = '<div id="pending-comments" class="box ' . $class . '">' .
            '<h3>' . '<img src="' . urldecode(dcPage::getPF('dmPending/icon.png')) . '" alt="" />' . ' ' . __('Pending comments') . '</h3>';
            $ret .= dmPendingBehaviors::getPendingComments($core,
                $core->auth->user_prefs->dmpending->pending_comments_nb,
                $core->auth->user_prefs->dmpending->pending_comments_large);
            $ret .= '</div>';
            $contents[] = new ArrayObject([$ret]);
        }
    }

    public static function adminAfterDashboardOptionsUpdate($userID)
    {
        global $core;

        // Get and store user's prefs for plugin options
        $core->auth->user_prefs->addWorkspace('dmpending');
        try {
            // Pending posts
            $core->auth->user_prefs->dmpending->put('pending_posts', !empty($_POST['dmpending_posts']), 'boolean');
            $core->auth->user_prefs->dmpending->put('pending_posts_nb', (integer) $_POST['dmpending_posts_nb'], 'integer');
            $core->auth->user_prefs->dmpending->put('pending_posts_large', empty($_POST['dmpending_posts_small']), 'boolean');
            $core->auth->user_prefs->dmpending->put('pending_posts_count', !empty($_POST['dmpending_posts_count']), 'boolean');
            // Pending comments
            $core->auth->user_prefs->dmpending->put('pending_comments', !empty($_POST['dmpending_comments']), 'boolean');
            $core->auth->user_prefs->dmpending->put('pending_comments_nb', (integer) $_POST['dmpending_comments_nb'], 'integer');
            $core->auth->user_prefs->dmpending->put('pending_comments_large', empty($_POST['dmpending_comments_small']), 'boolean');
            $core->auth->user_prefs->dmpending->put('pending_comments_count', !empty($_POST['dmpending_comments_count']), 'boolean');
        } catch (Exception $e) {
            $core->error->add($e->getMessage());
        }
    }

    public static function adminDashboardOptionsForm($core)
    {
        // Add fieldset for plugin options
        $core->auth->user_prefs->addWorkspace('dmpending');

        echo '<div class="fieldset" id="dmpending"><h4>' . __('Pending posts on dashboard') . '</h4>' .

        '<p>' .
        form::checkbox('dmpending_posts_count', 1, $core->auth->user_prefs->dmpending->pending_posts_count) . ' ' .
        '<label for="dmpending_posts_count" class="classic">' . __('Display count of pending posts on posts dashboard icon') . '</label></p>' .

        '<p>' .
        form::checkbox('dmpending_posts', 1, $core->auth->user_prefs->dmpending->pending_posts) . ' ' .
        '<label for="dmpending_posts" class="classic">' . __('Display pending posts') . '</label></p>' .

        '<p><label for="dmpending_posts_nb" class="classic">' . __('Number of pending posts to display:') . '</label>' .
        form::field('dmpending_posts_nb', 2, 3, (integer) $core->auth->user_prefs->dmpending->pending_posts_nb) .
        '</p>' .

        '<p>' .
        form::checkbox('dmpending_posts_small', 1, !$core->auth->user_prefs->dmpending->pending_posts_large) . ' ' .
        '<label for="dmpending_posts_small" class="classic">' . __('Small screen') . '</label></p>' .

            '</div>';

        echo '<div class="fieldset"><h4>' . __('Pending comments on dashboard') . '</h4>' .

        '<p>' .
        form::checkbox('dmpending_comments_count', 1, $core->auth->user_prefs->dmpending->pending_comments_count) . ' ' .
        '<label for="dmpending_comments_count" class="classic">' . __('Display count of pending comments on comments dashboard icon') . '</label></p>' .

        '<p>' .
        form::checkbox('dmpending_comments', 1, $core->auth->user_prefs->dmpending->pending_comments) . ' ' .
        '<label for="dmpending_comments" class="classic">' . __('Display pending comments') . '</label></p>' .

        '<p><label for="dmpending_comments_nb" class="classic">' . __('Number of pending comments to display:') . '</label>' .
        form::field('dmpending_comments_nb', 2, 3, (integer) $core->auth->user_prefs->dmpending->pending_comments_nb) .
        '</p>' .

        '<p>' .
        form::checkbox('dmpending_comments_small', 1, !$core->auth->user_prefs->dmpending->pending_comments_large) . ' ' .
        '<label for="dmpending_comments_small" class="classic">' . __('Small screen') . '</label></p>' .

            '</div>';
    }
}
