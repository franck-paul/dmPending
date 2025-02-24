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
use Dotclear\Database\MetaRecord;
use Dotclear\Helper\Date;
use Dotclear\Helper\Html\Form\Checkbox;
use Dotclear\Helper\Html\Form\Div;
use Dotclear\Helper\Html\Form\Fieldset;
use Dotclear\Helper\Html\Form\Img;
use Dotclear\Helper\Html\Form\Label;
use Dotclear\Helper\Html\Form\Legend;
use Dotclear\Helper\Html\Form\Li;
use Dotclear\Helper\Html\Form\Link;
use Dotclear\Helper\Html\Form\Note;
use Dotclear\Helper\Html\Form\Number;
use Dotclear\Helper\Html\Form\Para;
use Dotclear\Helper\Html\Form\Set;
use Dotclear\Helper\Html\Form\Text;
use Dotclear\Helper\Html\Form\Ul;
use Exception;

class BackendBehaviors
{
    private static function getPendingPosts(int $nb, bool $large): string
    {
        // Get last $nb pending posts
        $params = ['post_status' => App::status()->post()::PENDING];
        if ($nb > 0) {
            $params['limit'] = $nb;
        }

        $rs = App::blog()->getPosts($params, false);
        if (!$rs->isEmpty()) {
            $lines = function (MetaRecord $rs, bool $large) {
                while ($rs->fetch()) {
                    $infos = [];
                    if ($large) {
                        $details = __('on') . ' ' .
                            Date::dt2str(App::blog()->settings()->system->date_format, $rs->post_dt) . ' ' .
                            Date::dt2str(App::blog()->settings()->system->time_format, $rs->post_dt);
                        $infos[] = (new Text(null, __('by') . ' ' . $rs->user_id));
                        $infos[] = (new Text('time', $details))
                            ->extra('datetime="' . Date::iso8601((int) strtotime($rs->post_dt)) . '"');
                    }
                    yield (new Li('dmpp' . $rs->post_id))
                        ->class('line')
                        ->separator(' ')
                        ->items([
                            (new Link())
                                ->href(App::backend()->url()->get('admin.post', ['id' => $rs->post_id]))
                                ->text($rs->post_title),
                            ... $infos,
                        ]);
                }
            };

            return (new Set())
                ->items([
                    (new Ul())
                        ->items([
                            ... $lines($rs, $large),
                        ]),
                    (new Para())
                        ->items([
                            (new Link())
                                ->href(App::backend()->url()->get('admin.posts', ['status' => App::status()->post()::PENDING]))
                                ->text(__('See all pending posts')),
                        ]),
                ])
            ->render();
        }

        return (new Note())
            ->text(__('No pending post'))
        ->render();
    }

    /**
     * Counts the number of pending posts.
     *
     * @deprecated since 2.34
     *
     * @return     string  Number of pending posts.
     */
    private static function countPendingPostsOld(): string
    {
        $count = App::blog()->getPosts(['post_status' => App::status()->post()::PENDING], true)->f(0);
        if ($count) {
            $str = sprintf(__('(%d pending post)', '(%d pending posts)', (int) $count), (int) $count);

            return '</span></a> <a href="' . App::backend()->url()->get('admin.posts', ['status' => App::status()->post()::PENDING]) . '"><span class="db-icon-title-dm-pending">' . sprintf($str, $count);
        }

        return '';
    }

    /**
     * Counts the number of pending posts.
     *
     * @return     string  Number of pending posts.
     */
    private static function countPendingPosts(): string
    {
        $count = App::blog()->getPosts(['post_status' => App::status()->post()::PENDING], true)->f(0);
        if ($count) {
            $str = sprintf(__('(%d pending post)', '(%d pending posts)', (int) $count), (int) $count);

            return (new Link())
                ->href(App::backend()->url()->get('admin.posts', ['status' => App::status()->post()::PENDING]))
                ->items([
                    (new Text('span', sprintf($str, $count)))
                        ->class('db-icon-title-dm-pending'),
                ])
            ->render();
        }

        return '';
    }

    private static function getPendingComments(int $nb, bool $large): string
    {
        // Get last $nb pending comments
        $params = ['comment_status' => App::status()->comment()::PENDING];
        if ($nb > 0) {
            $params['limit'] = $nb;
        }

        $rs = App::blog()->getComments($params);
        if (!$rs->isEmpty()) {
            $lines = function (MetaRecord $rs, bool $large) {
                while ($rs->fetch()) {
                    $status = $rs->comment_status === App::status()->comment()::JUNK ? 'sts-junk' : '';
                    $infos  = [];
                    if ($large) {
                        $details = __('on') . ' ' .
                            Date::dt2str(App::blog()->settings()->system->date_format, $rs->comment_dt) . ' ' .
                            Date::dt2str(App::blog()->settings()->system->time_format, $rs->comment_dt);
                        $infos[] = (new Text(null, __('by') . ' ' . $rs->user_id));
                        $infos[] = (new Text('time', $details))
                            ->extra('datetime="' . Date::iso8601((int) strtotime($rs->comment_dt)) . '"');
                    }
                    yield (new Li('dmpc' . $rs->comment_id))
                        ->class(['line', $status])
                        ->separator(' ')
                        ->items([
                            (new Link())
                                ->href(App::backend()->url()->get('admin.comment', ['id' => $rs->comment_id]))
                                ->text($rs->post_title),
                            ... $infos,
                        ]);
                }
            };

            return (new Set())
                ->items([
                    (new Ul())
                        ->items([
                            ... $lines($rs, $large),
                        ]),
                    (new Para())
                        ->items([
                            (new Link())
                                ->href(App::backend()->url()->get('admin.comments', ['status' => App::status()->comment()::PENDING]))
                                ->text(__('See all pending comments')),
                        ]),
                ])
            ->render();
        }

        return (new Note())
            ->text(__('No pending comment'))
        ->render();
    }

    /**
     * Counts the number of pending comments.
     *
     * @deprecated since 2.34
     *
     * @return     string  Number of pending comments.
     */
    private static function countPendingCommentsOld(): string
    {
        $count = App::blog()->getComments(['comment_status' => App::status()->comment()::PENDING], true)->f(0);
        if ($count) {
            $str = sprintf(__('(%d pending comment)', '(%d pending comments)', (int) $count), (int) $count);

            return '</span></a> <a href="' . App::backend()->url()->get('admin.comments', ['status' => App::status()->comment()::PENDING]) . '"><span class="db-icon-title-dm-pending">' . sprintf($str, $count);
        }

        return '';
    }

    /**
     * Counts the number of pending comments.
     *
     * @return     string  Number of pending comments.
     */
    private static function countPendingComments(): string
    {
        $count = App::blog()->getComments(['comment_status' => App::status()->comment()::PENDING], true)->f(0);
        if ($count) {
            $str = sprintf(__('(%d pending comment)', '(%d pending comments)', (int) $count), (int) $count);

            return (new Link())
                ->href(App::backend()->url()->get('admin.comments', ['status' => App::status()->comment()::PENDING]))
                ->items([
                    (new Text('span', sprintf($str, $count)))
                        ->class('db-icon-title-dm-pending'),
                ])
            ->render();
        }

        return '';
    }

    public static function adminDashboardHeaders(): string
    {
        $preferences = My::prefs();

        return
        Page::jsJson('dm_pending', [
            'dmPendingPosts_Counter'    => $preferences->posts_count,
            'dmPendingComments_Counter' => $preferences->comments_count,
            'dmPending_AutoRefresh'     => $preferences->autorefresh,
            'dmPending_Interval'        => ($preferences->interval ?? 60),
        ]) .
        My::jsLoad('service.js');
    }

    /**
     * @param      string                       $name   The name
     * @param      ArrayObject<string, mixed>   $icon   The icon
     */
    public static function adminDashboardFavsIcon(string $name, ArrayObject $icon): string
    {
        $preferences = My::prefs();
        if ($preferences->posts_count && $name === 'posts') {
            // Hack posts title if there is at least one pending post
            if (version_compare(App::config()->dotclearVersion(), '2.34', '>=') || str_contains((string) App::config()->dotclearVersion(), 'dev')) {
                $str = self::countPendingPosts();
                if ($str !== '') {
                    $icon[3] = ($icon[3] ?? '') . $str;
                }
            } else {
                $str = self::countPendingPostsOld();
                if ($str !== '') {
                    $icon[0] .= $str;
                }
            }
        }

        if ($preferences->comments_count && $name === 'comments') {
            // Hack comments title if there is at least one comment
            if (version_compare(App::config()->dotclearVersion(), '2.34', '>=') || str_contains((string) App::config()->dotclearVersion(), 'dev')) {
                $str = self::countPendingComments();
                if ($str !== '') {
                    $icon[3] = ($icon[3] ?? '') . $str;
                }
            } else {
                $str = self::countPendingCommentsOld();
                if ($str !== '') {
                    $icon[0] .= $str;
                }
            }
        }

        return '';
    }

    /**
     * @param      ArrayObject<int, ArrayObject<int, string>>  $contents  The contents
     */
    public static function adminDashboardContents(ArrayObject $contents): string
    {
        $preferences = My::prefs();
        // Add large modules to the contents stack
        if ($preferences->posts) {
            $class = ($preferences->posts_large ? 'medium' : 'small');

            $ret = (new Div('pending-posts'))
                ->class(['box', $class])
                ->items([
                    (new Text(
                        'h3',
                        (new Img(urldecode(Page::getPF(My::id() . '/icon.svg'))))
                            ->class('icon-small')
                        ->render() . ' ' . __('Pending posts')
                    )),
                    (new Text(null, self::getPendingPosts(
                        $preferences->posts_nb,
                        $preferences->posts_large
                    ))),
                ])
            ->render();

            $contents->append(new ArrayObject([$ret]));
        }

        if ($preferences->comments) {
            $class = ($preferences->comments_large ? 'medium' : 'small');

            $ret = (new Div('pending-comments'))
                ->class(['box', $class])
                ->items([
                    (new Text(
                        'h3',
                        (new Img(urldecode(Page::getPF(My::id() . '/icon.svg'))))
                            ->class('icon-small')
                        ->render() . ' ' . __('Pending comments')
                    )),
                    (new Text(null, self::getPendingComments(
                        $preferences->posts_nb,
                        $preferences->posts_large
                    ))),
                ])
            ->render();

            $contents->append(new ArrayObject([$ret]));
        }

        return '';
    }

    public static function adminAfterDashboardOptionsUpdate(): string
    {
        $preferences = My::prefs();

        // Get and store user's prefs for plugin options
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
            $preferences->put('autorefresh', !empty($_POST['dmpending_autorefresh']), App::userWorkspace()::WS_BOOL);
            $preferences->put('interval', (int) $_POST['dmpending_interval'], App::userWorkspace()::WS_INT);
        } catch (Exception $e) {
            App::error()->add($e->getMessage());
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
                (new Checkbox('dmpending_posts_small', !$preferences->posts_large))
                    ->value(1)
                    ->label((new Label(__('Small screen'), Label::INSIDE_TEXT_AFTER))),
            ]),
            (new Text(null, '<hr>')),
            (new Text('h5', __('Pending comments'))),
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
                (new Checkbox('dmpending_comments_small', !$preferences->comments_large))
                    ->value(1)
                    ->label((new Label(__('Small screen'), Label::INSIDE_TEXT_AFTER))),
            ]),
            (new Text(null, '<hr>')),
            (new Para())->items([
                (new Checkbox('dmpending_autorefresh', $preferences->autorefresh))
                    ->value(1)
                    ->label((new Label(__('Auto refresh'), Label::INSIDE_TEXT_AFTER))),
            ]),
            (new Para())->items([
                (new Number('dmpending_interval', 0, 9_999_999, $preferences->interval))
                    ->label((new Label(__('Interval in seconds between two refreshes:'), Label::INSIDE_TEXT_BEFORE))),
            ]),
        ])
        ->render();

        return '';
    }
}
