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
use Dotclear\App;
use Dotclear\Core\Backend\Page;
use Dotclear\Helper\Date;
use Dotclear\Helper\Html\Form\Checkbox;
use Dotclear\Helper\Html\Form\Fieldset;
use Dotclear\Helper\Html\Form\Label;
use Dotclear\Helper\Html\Form\Legend;
use Dotclear\Helper\Html\Form\Number;
use Dotclear\Helper\Html\Form\Para;
use Dotclear\Helper\Html\Form\Text;
use Exception;

class BackendBehaviors
{
    private static function getPendingPosts(int $nb, bool $large): string
    {
        // Get last $nb pending posts
        $params = ['post_status' => App::blog()::POST_PENDING];
        if ((int) $nb > 0) {
            $params['limit'] = (int) $nb;
        }

        $rs = App::blog()->getPosts($params, false);
        if (!$rs->isEmpty()) {
            $ret = '<ul>';
            while ($rs->fetch()) {
                $ret .= '<li class="line" id="dmpp' . $rs->post_id . '">';
                $ret .= '<a href="' . App::backend()->url()->get('admin.post', ['id' => $rs->post_id]) . '">' . $rs->post_title . '</a>';
                if ($large) {
                    $dt = '<time datetime="' . Date::iso8601((int) strtotime($rs->post_dt), App::auth()->getInfo('user_tz')) . '">%s</time>';
                    $ret .= ' (' .
                    __('by') . ' ' . $rs->user_id . ' ' . sprintf($dt, __('on') . ' ' .
                        Date::dt2str(App::blog()->settings()->system->date_format, $rs->post_dt) . ' ' .
                        Date::dt2str(App::blog()->settings()->system->time_format, $rs->post_dt))
                     . ')';
                }

                $ret .= '</li>';
            }

            $ret .= '</ul>';

            return $ret . ('<p><a href="' . App::backend()->url()->get('admin.posts', ['status' => App::blog()::POST_PENDING]) . '">' . __('See all pending posts') . '</a></p>');
        }

        return '<p>' . __('No pending post') . '</p>';
    }

    private static function countPendingPosts(): string
    {
        $count = App::blog()->getPosts(['post_status' => App::blog()::POST_PENDING], true)->f(0);
        if ($count) {
            $str = sprintf(__('(%d pending post)', '(%d pending posts)', (int) $count), (int) $count);

            return '</span></a> <a href="' . App::backend()->url()->get('admin.posts', ['status' => App::blog()::POST_PENDING]) . '"><span class="db-icon-title-dm-pending">' . sprintf($str, $count);
        }

        return '';
    }

    private static function getPendingComments(int $nb, bool $large): string
    {
        // Get last $nb pending comments
        $params = ['comment_status' => App::blog()::COMMENT_PENDING];
        if ((int) $nb > 0) {
            $params['limit'] = (int) $nb;
        }

        $rs = App::blog()->getComments($params);
        if (!$rs->isEmpty()) {
            $ret = '<ul>';
            while ($rs->fetch()) {
                $ret .= '<li class="line" ' . ($rs->comment_status == App::blog()::COMMENT_JUNK ? ' class="sts-junk"' : '') .
                ' id="dmpc' . $rs->comment_id . '">';
                $ret .= '<a href="' . App::backend()->url()->get('admin.comment', ['id' => $rs->comment_id]) . '">' . $rs->post_title . '</a>';
                if ($large) {
                    $dt = '<time datetime="' . Date::iso8601((int) strtotime($rs->comment_dt), App::auth()->getInfo('user_tz')) . '">%s</time>';
                    $ret .= ' (' .
                    __('by') . ' ' . $rs->comment_author . ' ' . sprintf($dt, __('on') . ' ' .
                        Date::dt2str(App::blog()->settings()->system->date_format, $rs->comment_dt) . ' ' .
                        Date::dt2str(App::blog()->settings()->system->time_format, $rs->comment_dt)) .
                    ')';
                }

                $ret .= '</li>';
            }

            $ret .= '</ul>';

            return $ret . ('<p><a href="' . App::backend()->url()->get('admin.comments', ['status' => App::blog()::COMMENT_PENDING]) . '">' . __('See all pending comments') . '</a></p>');
        }

        return '<p>' . __('No pending comment') . '</p>';
    }

    private static function countPendingComments(): string
    {
        $count = App::blog()->getComments(['comment_status' => App::blog()::COMMENT_PENDING], true)->f(0);
        if ($count) {
            $str = sprintf(__('(%d pending comment)', '(%d pending comments)', (int) $count), (int) $count);

            return '</span></a> <a href="' . App::backend()->url()->get('admin.comments', ['status' => App::blog()::COMMENT_PENDING]) . '"><span class="db-icon-title-dm-pending">' . sprintf($str, $count);
        }

        return '';
    }

    public static function adminDashboardHeaders(): string
    {
        $preferences = My::prefs();

        return
        Page::jsJson('dm_pending', [
            'dmPendingPosts_Counter'    => $preferences?->posts_count,
            'dmPendingComments_Counter' => $preferences?->comments_count,
            'dmPending_Interval'        => ($preferences?->interval ?? 60),
        ]) .
        My::jsLoad('service.js');
    }

    /**
     * @param      string                       $name   The name
     * @param      ArrayObject<string, mixed>   $icon   The icon
     *
     * @return     string
     */
    public static function adminDashboardFavsIcon(string $name, ArrayObject $icon): string
    {
        $preferences = My::prefs();
        if ($preferences?->posts_count && $name == 'posts') {
            // Hack posts title if there is at least one pending post
            $str = self::countPendingPosts();
            if ($str != '') {
                $icon[0] .= $str;
            }
        }

        if ($preferences?->comments_count && $name == 'comments') {
            // Hack comments title if there is at least one comment
            $str = self::countPendingComments();
            if ($str != '') {
                $icon[0] .= $str;
            }
        }

        return '';
    }

    /**
     * @param      ArrayObject<int, ArrayObject<int, string>>  $contents  The contents
     *
     * @return     string
     */
    public static function adminDashboardContents(ArrayObject $contents): string
    {
        $preferences = My::prefs();
        // Add large modules to the contents stack
        if ($preferences?->posts) {
            $class = ($preferences->posts_large ? 'medium' : 'small');
            $ret   = '<div id="pending-posts" class="box ' . $class . '">' .
            '<h3>' . '<img src="' . urldecode(Page::getPF('dmPending/icon.svg')) . '" alt="" class="icon-small" />' . ' ' . __('Pending posts') . '</h3>';
            $ret .= self::getPendingPosts(
                $preferences->posts_nb,
                $preferences->posts_large
            );
            $ret .= '</div>';
            $contents[] = new ArrayObject([$ret]);
        }

        if ($preferences?->comments) {
            $class = ($preferences->comments_large ? 'medium' : 'small');
            $ret   = '<div id="pending-comments" class="box ' . $class . '">' .
            '<h3>' . '<img src="' . urldecode(Page::getPF('dmPending/icon.svg')) . '" alt="" class="icon-small" />' . ' ' . __('Pending comments') . '</h3>';
            $ret .= self::getPendingComments(
                $preferences->comments_nb,
                $preferences->comments_large
            );
            $ret .= '</div>';
            $contents[] = new ArrayObject([$ret]);
        }

        return '';
    }

    public static function adminAfterDashboardOptionsUpdate(): string
    {
        $preferences = My::prefs();

        // Get and store user's prefs for plugin options
        if ($preferences) {
            try {
                // Pending posts
                $preferences->put('posts', !empty($_POST['dmpending_posts']), App::userWorkspace()::WS_BOOL);
                $preferences->put('posts_nb', (int) $_POST['dmpending_posts_nb'], App::userWorkspace()::WS_INT);
                $preferences->put('posts_large', empty($_POST['dmpending_posts_small']), App::userWorkspace()::WS_BOOL);
                $preferences->put('posts_count', !empty($_POST['dmpending_posts_count']), App::userWorkspace()::WS_BOOL);
                // Pending comments
                $preferences->put('comments', !empty($_POST['dmpending_comments']), App::userWorkspace()::WS_BOOL);
                $preferences->put('comments_nb', (int) $_POST['dmpending_comments_nb'], App::userWorkspace()::WS_INT);
                $preferences->put('comments_large', empty($_POST['dmpending_comments_small']), App::userWorkspace()::WS_BOOL);
                $preferences->put('comments_count', !empty($_POST['dmpending_comments_count']), App::userWorkspace()::WS_BOOL);
                // Interval
                $preferences->put('interval', (int) $_POST['dmpending_interval'], App::userWorkspace()::WS_INT);
            } catch (Exception $e) {
                App::error()->add($e->getMessage());
            }
        }

        return '';
    }

    public static function adminDashboardOptionsForm(): string
    {
        $preferences = My::prefs();

        // Add fieldset for plugin options

        echo
        (new Fieldset('dmpending'))
        ->legend((new Legend(__('Pending posts and comments on dashboard'))))
        ->fields([
            (new Text('h5', __('Pending posts'))),
            (new Para())->items([
                (new Checkbox('dmpending_posts_count', $preferences?->posts_count))
                    ->value(1)
                    ->label((new Label(__('Display count of pending posts on posts dashboard icon'), Label::INSIDE_TEXT_AFTER))),
            ]),
            (new Para())->items([
                (new Checkbox('dmpending_posts', $preferences?->posts))
                    ->value(1)
                    ->label((new Label(__('Display pending posts'), Label::INSIDE_TEXT_AFTER))),
            ]),
            (new Para())->items([
                (new Number('dmpending_posts_nb', 1, 999, $preferences?->posts_nb))
                    ->label((new Label(__('Number of pending posts to display:'), Label::INSIDE_TEXT_BEFORE))),
            ]),
            (new Para())->items([
                (new Checkbox('dmpending_posts_small', !$preferences?->posts_large))
                    ->value(1)
                    ->label((new Label(__('Small screen'), Label::INSIDE_TEXT_AFTER))),
            ]),
            (new Text(null, '<hr />')),
            (new Text('h5', __('Pending comments'))),
            (new Para())->items([
                (new Checkbox('dmpending_comments_count', $preferences?->comments_count))
                    ->value(1)
                    ->label((new Label(__('Display count of pending comments on comments dashboard icon'), Label::INSIDE_TEXT_AFTER))),
            ]),
            (new Para())->items([
                (new Checkbox('dmpending_comments', $preferences?->comments))
                    ->value(1)
                    ->label((new Label(__('Display pending comments'), Label::INSIDE_TEXT_AFTER))),
            ]),
            (new Para())->items([
                (new Number('dmpending_comments_nb', 1, 999, $preferences?->comments_nb))
                    ->label((new Label(__('Number of pending comments to display:'), Label::INSIDE_TEXT_BEFORE))),
            ]),
            (new Para())->items([
                (new Checkbox('dmpending_comments_small', !$preferences?->comments_large))
                    ->value(1)
                    ->label((new Label(__('Small screen'), Label::INSIDE_TEXT_AFTER))),
            ]),
            (new Text(null, '<hr />')),
            (new Para())->items([
                (new Number('dmpending_interval', 0, 9_999_999, $preferences?->interval))
                    ->label((new Label(__('Interval in seconds between two refreshes:'), Label::INSIDE_TEXT_BEFORE))),
            ]),
        ])
        ->render();

        return '';
    }
}
