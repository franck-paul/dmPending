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

use ArrayObject;
use dcBlog;
use dcCore;
use dcPage;
use dcWorkspace;
use Dotclear\Helper\Date;
use Dotclear\Helper\Html\Form\Checkbox;
use Dotclear\Helper\Html\Form\Fieldset;
use Dotclear\Helper\Html\Form\Label;
use Dotclear\Helper\Html\Form\Legend;
use Dotclear\Helper\Html\Form\Number;
use Dotclear\Helper\Html\Form\Para;
use Exception;

class BackendBehaviors
{
    private static function getPendingPosts($core, $nb, $large)
    {
        // Get last $nb pending posts
        $params = ['post_status' => dcBlog::POST_PENDING];
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
                    $dt = '<time datetime="' . Date::iso8601(strtotime($rs->post_dt), dcCore::app()->auth->getInfo('user_tz')) . '">%s</time>';
                    $ret .= ' (' .
                    __('by') . ' ' . $rs->user_id . ' ' . sprintf($dt, __('on') . ' ' .
                        Date::dt2str(dcCore::app()->blog->settings->system->date_format, $rs->post_dt) . ' ' .
                        Date::dt2str(dcCore::app()->blog->settings->system->time_format, $rs->post_dt))
                     . ')';
                }
                $ret .= '</li>';
            }
            $ret .= '</ul>';
            $ret .= '<p><a href="posts.php?status=' . dcBlog::POST_PENDING . '">' . __('See all pending posts') . '</a></p>';

            return $ret;
        }

        return '<p>' . __('No pending post') . '</p>';
    }

    private static function countPendingPosts()
    {
        $count = dcCore::app()->blog->getPosts(['post_status' => dcBlog::POST_PENDING], true)->f(0);
        if ($count) {
            $str = sprintf(__('(%d pending post)', '(%d pending posts)', (int) $count), (int) $count);

            return '</span></a> <a href="posts.php?status=' . dcBlog::POST_PENDING . '"><span class="db-icon-title-dm-pending">' . sprintf($str, $count);
        }

        return '';
    }

    private static function getPendingComments($core, $nb, $large)
    {
        // Get last $nb pending comments
        $params = ['comment_status' => dcBlog::COMMENT_PENDING];
        if ((int) $nb > 0) {
            $params['limit'] = (int) $nb;
        }
        $rs = dcCore::app()->blog->getComments($params, false);
        if (!$rs->isEmpty()) {
            $ret = '<ul>';
            while ($rs->fetch()) {
                $ret .= '<li class="line" ' . ($rs->comment_status == dcBlog::COMMENT_JUNK ? ' class="sts-junk"' : '') .
                ' id="dmpc' . $rs->comment_id . '">';
                $ret .= '<a href="comment.php?id=' . $rs->comment_id . '">' . $rs->post_title . '</a>';
                if ($large) {
                    $dt = '<time datetime="' . Date::iso8601(strtotime($rs->comment_dt), dcCore::app()->auth->getInfo('user_tz')) . '">%s</time>';
                    $ret .= ' (' .
                    __('by') . ' ' . $rs->comment_author . ' ' . sprintf($dt, __('on') . ' ' .
                        Date::dt2str(dcCore::app()->blog->settings->system->date_format, $rs->comment_dt) . ' ' .
                        Date::dt2str(dcCore::app()->blog->settings->system->time_format, $rs->comment_dt)) .
                    ')';
                }
                $ret .= '</li>';
            }
            $ret .= '</ul>';
            $ret .= '<p><a href="comments.php?status=' . dcBlog::COMMENT_PENDING . '">' . __('See all pending comments') . '</a></p>';

            return $ret;
        }

        return '<p>' . __('No pending comment') . '</p>';
    }

    private static function countPendingComments()
    {
        $count = dcCore::app()->blog->getComments(['comment_status' => dcBlog::COMMENT_PENDING], true)->f(0);
        if ($count) {
            $str = sprintf(__('(%d pending comment)', '(%d pending comments)', (int) $count), (int) $count);

            return '</span></a> <a href="comments.php?status=' . dcBlog::COMMENT_PENDING . '"><span class="db-icon-title-dm-pending">' . sprintf($str, $count);
        }

        return '';
    }

    public static function adminDashboardHeaders()
    {
        $preferences = dcCore::app()->auth->user_prefs->get(My::id());

        return
        dcPage::jsJson('dm_pending', [
            'dmPendingPosts_Counter'    => $preferences->posts_count,
            'dmPendingComments_Counter' => $preferences->posts_count,
        ]) .
        dcPage::jsModuleLoad('dmPending/js/service.js', dcCore::app()->getVersion('dmPending'));
    }

    public static function adminDashboardFavsIcon($name, $icon)
    {
        $preferences = dcCore::app()->auth->user_prefs->get(My::id());
        if ($preferences->posts_count && $name == 'posts') {
            // Hack posts title if there is at least one pending post
            $str = BackendBehaviors::countPendingPosts();
            if ($str != '') {
                $icon[0] .= $str;
            }
        }
        if ($preferences->comments_count && $name == 'comments') {
            // Hack comments title if there is at least one comment
            $str = BackendBehaviors::countPendingComments();
            if ($str != '') {
                $icon[0] .= $str;
            }
        }
    }

    public static function adminDashboardContents($contents)
    {
        $preferences = dcCore::app()->auth->user_prefs->get(My::id());
        // Add large modules to the contents stack
        if ($preferences->posts) {
            $class = ($preferences->posts_large ? 'medium' : 'small');
            $ret   = '<div id="pending-posts" class="box ' . $class . '">' .
            '<h3>' . '<img src="' . urldecode(dcPage::getPF('dmPending/icon.png')) . '" alt="" />' . ' ' . __('Pending posts') . '</h3>';
            $ret .= BackendBehaviors::getPendingPosts(
                dcCore::app(),
                $preferences->posts_nb,
                $preferences->posts_large
            );
            $ret .= '</div>';
            $contents[] = new ArrayObject([$ret]);
        }
        if ($preferences->comments) {
            $class = ($preferences->comments_large ? 'medium' : 'small');
            $ret   = '<div id="pending-comments" class="box ' . $class . '">' .
            '<h3>' . '<img src="' . urldecode(dcPage::getPF('dmPending/icon.png')) . '" alt="" />' . ' ' . __('Pending comments') . '</h3>';
            $ret .= BackendBehaviors::getPendingComments(
                dcCore::app(),
                $preferences->comments_nb,
                $preferences->comments_large
            );
            $ret .= '</div>';
            $contents[] = new ArrayObject([$ret]);
        }
    }

    public static function adminAfterDashboardOptionsUpdate()
    {
        $preferences = dcCore::app()->auth->user_prefs->get(My::id());
        // Get and store user's prefs for plugin options
        try {
            // Pending posts
            $preferences->put('posts', !empty($_POST['dmpending_posts']), dcWorkspace::WS_BOOL);
            $preferences->put('posts_nb', (int) $_POST['dmpending_posts_nb'], dcWorkspace::WS_INT);
            $preferences->put('posts_large', empty($_POST['dmpending_posts_small']), dcWorkspace::WS_BOOL);
            $preferences->put('posts_count', !empty($_POST['dmpending_posts_count']), dcWorkspace::WS_BOOL);
            // Pending comments
            $preferences->put('comments', !empty($_POST['dmpending_comments']), dcWorkspace::WS_BOOL);
            $preferences->put('comments_nb', (int) $_POST['dmpending_comments_nb'], dcWorkspace::WS_INT);
            $preferences->put('comments_large', empty($_POST['dmpending_comments_small']), dcWorkspace::WS_BOOL);
            $preferences->put('comments_count', !empty($_POST['dmpending_comments_count']), dcWorkspace::WS_BOOL);
        } catch (Exception $e) {
            dcCore::app()->error->add($e->getMessage());
        }
    }

    public static function adminDashboardOptionsForm()
    {
        $preferences = dcCore::app()->auth->user_prefs->get(My::id());

        // Add fieldset for plugin options

        echo
        (new Fieldset('dmpending'))
        ->legend((new Legend(__('Pending posts on dashboard'))))
        ->fields([
            (new Para())->items([
                (new Checkbox('dmpending_posts_count', $preferences->posts_count))
                    ->value(1)
                    ->label((new Label(__('Display count of pending posts on posts dashboard icon'), Label::INSIDE_TEXT_AFTER))),
            ]),
            (new Para())->items([
                (new Checkbox('dmpending_posts', $preferences->posts))
                    ->value(1)
                    ->label((new Label(__('Display pending posts'), Label::INSIDE_TEXT_AFTER))),
            ]),
            (new Para())->items([
                (new Number('dmpending_posts_nb', 1, 999, $preferences->posts_nb))
                    ->label((new Label(__('Number of pending posts to display:'), Label::INSIDE_TEXT_BEFORE))),
            ]),
            (new Para())->items([
                (new Checkbox('dmpending_posts_small', $preferences->posts_large))
                    ->value(1)
                    ->label((new Label(__('Small screen'), Label::INSIDE_TEXT_AFTER))),
            ]),
        ])
        ->render();

        echo
        (new Fieldset('dmpending_bis'))
        ->legend((new Legend(__('Pending comments on dashboard'))))
        ->fields([
            (new Para())->items([
                (new Checkbox('dmpending_comments_count', $preferences->comments_count))
                    ->value(1)
                    ->label((new Label(__('Display count of pending comments on comments dashboard icon'), Label::INSIDE_TEXT_AFTER))),
            ]),
            (new Para())->items([
                (new Checkbox('dmpending_comments', $preferences->comments))
                    ->value(1)
                    ->label((new Label(__('Display pending comments'), Label::INSIDE_TEXT_AFTER))),
            ]),
            (new Para())->items([
                (new Number('dmpending_comments_nb', 1, 999, $preferences->comments_nb))
                    ->label((new Label(__('Number of pending comments to display:'), Label::INSIDE_TEXT_BEFORE))),
            ]),
            (new Para())->items([
                (new Checkbox('dmpending_comments_small', $preferences->comments_large))
                    ->value(1)
                    ->label((new Label(__('Small screen'), Label::INSIDE_TEXT_AFTER))),
            ]),
        ])
        ->render();
    }
}
