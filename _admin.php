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

// dead but useful code, in order to have translations
__('Pending Dashboard Module') . __('Display pending posts and comments on dashboard');

// Dashboard behaviours
dcCore::app()->addBehavior('adminDashboardContents', ['dmPendingBehaviors', 'adminDashboardContents']);
dcCore::app()->addBehavior('adminDashboardHeaders', ['dmPendingBehaviors', 'adminDashboardHeaders']);
dcCore::app()->addBehavior('adminDashboardFavsIcon', ['dmPendingBehaviors', 'adminDashboardFavsIcon']);

dcCore::app()->addBehavior('adminAfterDashboardOptionsUpdate', ['dmPendingBehaviors', 'adminAfterDashboardOptionsUpdate']);
dcCore::app()->addBehavior('adminDashboardOptionsForm', ['dmPendingBehaviors', 'adminDashboardOptionsForm']);

# BEHAVIORS
class dmPendingBehaviors
{
    private static function getPendingPosts($core, $nb, $large)
    {
        // Get last $nb pending posts
        $params = ['post_status' => -2];
        if ((int) $nb > 0) {
            $params['limit'] = (int) $nb;
        }
        $rs = dcCore::app()->blog->getPosts($params, false);
        if (!$rs->isEmpty()) {
            $ret = '<ul>';
            while ($rs->fetch()) {
                $ret .= '<li class="line" id="dmpp' . $rs->post_id . '">';
                $ret .= '<a href="post.php?id=' . $rs->post_id . '">' . $rs->post_title . '</a>';
                if ($large) {
                    $ret .= ' (' .
                    __('by') . ' ' . $rs->user_id . ' ' . __('on') . ' ' .
                    dt::dt2str(dcCore::app()->blog->settings->system->date_format, $rs->post_dt) . ' ' .
                    dt::dt2str(dcCore::app()->blog->settings->system->time_format, $rs->post_dt) . ')';
                }
                $ret .= '</li>';
            }
            $ret .= '</ul>';
            $ret .= '<p><a href="posts.php?status=-2">' . __('See all pending posts') . '</a></p>';

            return $ret;
        }

        return '<p>' . __('No pending post') . '</p>';
    }

    private static function countPendingPosts($core)
    {
        $count = dcCore::app()->blog->getPosts(['post_status' => -2], true)->f(0);
        if ($count) {
            $str = sprintf(__('(%d pending post)', '(%d pending posts)', $count), $count);

            return '</span></a> <a href="posts.php?status=-2"><span class="db-icon-title-dm-pending">' . sprintf($str, $count);
        }

        return '';
    }

    private static function getPendingComments($core, $nb, $large)
    {
        // Get last $nb pending comments
        $params = ['comment_status' => -1];
        if ((int) $nb > 0) {
            $params['limit'] = (int) $nb;
        }
        $rs = dcCore::app()->blog->getComments($params, false);
        if (!$rs->isEmpty()) {
            $ret = '<ul>';
            while ($rs->fetch()) {
                $ret .= '<li class="line" ' . ($rs->comment_status == -2 ? ' class="sts-junk"' : '') .
                ' id="dmpc' . $rs->comment_id . '">';
                $ret .= '<a href="comment.php?id=' . $rs->comment_id . '">' . $rs->post_title . '</a>';
                if ($large) {
                    $ret .= ' (' .
                    __('by') . ' ' . $rs->comment_author . ' ' . __('on') . ' ' .
                    dt::dt2str(dcCore::app()->blog->settings->system->date_format, $rs->comment_dt) . ' ' .
                    dt::dt2str(dcCore::app()->blog->settings->system->time_format, $rs->comment_dt) . ')';
                }
                $ret .= '</li>';
            }
            $ret .= '</ul>';
            $ret .= '<p><a href="comments.php?status=-1">' . __('See all pending comments') . '</a></p>';

            return $ret;
        }

        return '<p>' . __('No pending comment') . '</p>';
    }

    private static function countPendingComments($core)
    {
        $count = dcCore::app()->blog->getComments(['comment_status' => -1], true)->f(0);
        if ($count) {
            $str = sprintf(__('(%d pending comment)', '(%d pending comments)', $count), $count);

            return '</span></a> <a href="comments.php?status=-1"><span class="db-icon-title-dm-pending">' . sprintf($str, $count);
        }

        return '';
    }

    public static function adminDashboardHeaders()
    {
        dcCore::app()->auth->user_prefs->addWorkspace('dmpending');

        return
        dcPage::jsJson('dm_pending', [
            'dmPendingPosts_Counter'    => dcCore::app()->auth->user_prefs->dmpending->pending_posts_count,
            'dmPendingComments_Counter' => dcCore::app()->auth->user_prefs->dmpending->pending_posts_count,
        ]) .
        dcPage::jsModuleLoad('dmPending/js/service.js', dcCore::app()->getVersion('dmPending'));
    }

    public static function adminDashboardFavsIcon($core, $name, $icon)
    {
        dcCore::app()->auth->user_prefs->addWorkspace('dmpending');
        if (dcCore::app()->auth->user_prefs->dmpending->pending_posts_count && $name == 'posts') {
            // Hack posts title if there is at least one pending post
            $str = dmPendingBehaviors::countPendingPosts(dcCore::app());
            if ($str != '') {
                $icon[0] .= $str;
            }
        }
        if (dcCore::app()->auth->user_prefs->dmpending->pending_comments_count && $name == 'comments') {
            // Hack comments title if there is at least one comment
            $str = dmPendingBehaviors::countPendingComments(dcCore::app());
            if ($str != '') {
                $icon[0] .= $str;
            }
        }
    }

    public static function adminDashboardContents($core, $contents)
    {
        // Add large modules to the contents stack
        dcCore::app()->auth->user_prefs->addWorkspace('dmpending');
        if (dcCore::app()->auth->user_prefs->dmpending->pending_posts) {
            $class = (dcCore::app()->auth->user_prefs->dmpending->pending_posts_large ? 'medium' : 'small');
            $ret   = '<div id="pending-posts" class="box ' . $class . '">' .
            '<h3>' . '<img src="' . urldecode(dcPage::getPF('dmPending/icon.png')) . '" alt="" />' . ' ' . __('Pending posts') . '</h3>';
            $ret .= dmPendingBehaviors::getPendingPosts(
                dcCore::app(),
                dcCore::app()->auth->user_prefs->dmpending->pending_posts_nb,
                dcCore::app()->auth->user_prefs->dmpending->pending_posts_large
            );
            $ret .= '</div>';
            $contents[] = new ArrayObject([$ret]);
        }
        if (dcCore::app()->auth->user_prefs->dmpending->pending_comments) {
            $class = (dcCore::app()->auth->user_prefs->dmpending->pending_comments_large ? 'medium' : 'small');
            $ret   = '<div id="pending-comments" class="box ' . $class . '">' .
            '<h3>' . '<img src="' . urldecode(dcPage::getPF('dmPending/icon.png')) . '" alt="" />' . ' ' . __('Pending comments') . '</h3>';
            $ret .= dmPendingBehaviors::getPendingComments(
                dcCore::app(),
                dcCore::app()->auth->user_prefs->dmpending->pending_comments_nb,
                dcCore::app()->auth->user_prefs->dmpending->pending_comments_large
            );
            $ret .= '</div>';
            $contents[] = new ArrayObject([$ret]);
        }
    }

    public static function adminAfterDashboardOptionsUpdate($userID)
    {
        // Get and store user's prefs for plugin options
        dcCore::app()->auth->user_prefs->addWorkspace('dmpending');

        try {
            // Pending posts
            dcCore::app()->auth->user_prefs->dmpending->put('pending_posts', !empty($_POST['dmpending_posts']), 'boolean');
            dcCore::app()->auth->user_prefs->dmpending->put('pending_posts_nb', (int) $_POST['dmpending_posts_nb'], 'integer');
            dcCore::app()->auth->user_prefs->dmpending->put('pending_posts_large', empty($_POST['dmpending_posts_small']), 'boolean');
            dcCore::app()->auth->user_prefs->dmpending->put('pending_posts_count', !empty($_POST['dmpending_posts_count']), 'boolean');
            // Pending comments
            dcCore::app()->auth->user_prefs->dmpending->put('pending_comments', !empty($_POST['dmpending_comments']), 'boolean');
            dcCore::app()->auth->user_prefs->dmpending->put('pending_comments_nb', (int) $_POST['dmpending_comments_nb'], 'integer');
            dcCore::app()->auth->user_prefs->dmpending->put('pending_comments_large', empty($_POST['dmpending_comments_small']), 'boolean');
            dcCore::app()->auth->user_prefs->dmpending->put('pending_comments_count', !empty($_POST['dmpending_comments_count']), 'boolean');
        } catch (Exception $e) {
            dcCore::app()->error->add($e->getMessage());
        }
    }

    public static function adminDashboardOptionsForm($core)
    {
        // Add fieldset for plugin options
        dcCore::app()->auth->user_prefs->addWorkspace('dmpending');

        echo '<div class="fieldset" id="dmpending"><h4>' . __('Pending posts on dashboard') . '</h4>' .

        '<p>' .
        form::checkbox('dmpending_posts_count', 1, dcCore::app()->auth->user_prefs->dmpending->pending_posts_count) . ' ' .
        '<label for="dmpending_posts_count" class="classic">' . __('Display count of pending posts on posts dashboard icon') . '</label></p>' .

        '<p>' .
        form::checkbox('dmpending_posts', 1, dcCore::app()->auth->user_prefs->dmpending->pending_posts) . ' ' .
        '<label for="dmpending_posts" class="classic">' . __('Display pending posts') . '</label></p>' .

        '<p><label for="dmpending_posts_nb" class="classic">' . __('Number of pending posts to display:') . '</label>' .
        form::number('dmpending_posts_nb', 1, 999, dcCore::app()->auth->user_prefs->dmpending->pending_posts_nb) .
        '</p>' .

        '<p>' .
        form::checkbox('dmpending_posts_small', 1, !dcCore::app()->auth->user_prefs->dmpending->pending_posts_large) . ' ' .
        '<label for="dmpending_posts_small" class="classic">' . __('Small screen') . '</label></p>' .

            '</div>';

        echo '<div class="fieldset"><h4>' . __('Pending comments on dashboard') . '</h4>' .

        '<p>' .
        form::checkbox('dmpending_comments_count', 1, dcCore::app()->auth->user_prefs->dmpending->pending_comments_count) . ' ' .
        '<label for="dmpending_comments_count" class="classic">' . __('Display count of pending comments on comments dashboard icon') . '</label></p>' .

        '<p>' .
        form::checkbox('dmpending_comments', 1, dcCore::app()->auth->user_prefs->dmpending->pending_comments) . ' ' .
        '<label for="dmpending_comments" class="classic">' . __('Display pending comments') . '</label></p>' .

        '<p><label for="dmpending_comments_nb" class="classic">' . __('Number of pending comments to display:') . '</label>' .
        form::number('dmpending_comments_nb', 1, 999, dcCore::app()->auth->user_prefs->dmpending->pending_comments_nb) .
        '</p>' .

        '<p>' .
        form::checkbox('dmpending_comments_small', 1, !dcCore::app()->auth->user_prefs->dmpending->pending_comments_large) . ' ' .
        '<label for="dmpending_comments_small" class="classic">' . __('Small screen') . '</label></p>' .

            '</div>';
    }
}
