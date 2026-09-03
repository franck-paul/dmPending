<?php

/**
 * @brief dmPending, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @author Franck Paul and contributors
 *
 * @copyright Franck Paul contact@open-time.net
 * @copyright GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
declare(strict_types=1);

namespace Dotclear\Plugin\dmPending;

use ArrayObject;
use Dotclear\App;
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
use Dotclear\Helper\Html\Form\Span;
use Dotclear\Helper\Html\Form\Text;
use Dotclear\Helper\Html\Form\Timestamp;
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
            $lines = function (MetaRecord $metaRecord, bool $large) {
                $date_format = App::blog()->settings()->get('system')->getStr('date_format', false) ?: '%F';
                $time_format = App::blog()->settings()->get('system')->getStr('time_format', false) ?: '%T';
                $user_tz     = is_string($user_tz = App::auth()->getInfo('user_tz')) ? $user_tz : 'UTC';

                while ($metaRecord->fetch()) {
                    $post_id    = $metaRecord->intField('post_id');
                    $post_dt    = $metaRecord->strField('post_dt');
                    $user_id    = $metaRecord->strField('user_id');
                    $post_title = $metaRecord->strField('post_title');

                    $infos = [];
                    if ($large) {
                        $details = __('on') . ' ' .
                            Date::dt2str($date_format, $post_dt) . ' ' .
                            Date::dt2str($time_format, $post_dt);
                        $infos[] = (new Text(null, __('by') . ' ' . $user_id));
                        $infos[] = (new Timestamp($details))
                            ->datetime(Date::iso8601((int) strtotime($post_dt), $user_tz));
                    }

                    yield (new Li('dmpp' . $post_id))
                        ->class('line')
                        ->separator(' ')
                        ->items([
                            (new Link())
                                ->href(App::backend()->url()->get('admin.post', ['id' => $post_id]))
                                ->text($post_title),
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
     * @return     string  Number of pending posts.
     */
    private static function countPendingPosts(): string
    {
        $count = App::blog()->getPosts(['post_status' => App::status()->post()::PENDING], true)->cardinal();
        if ($count > 0) {
            return (new Link())
                ->href(App::backend()->url()->get('admin.posts', ['status' => App::status()->post()::PENDING]))
                ->items([
                    (new Span(sprintf(__('(%d pending post)', '(%d pending posts)', $count), $count)))
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
            $lines = function (MetaRecord $metaRecord, bool $large) {
                $date_format = App::blog()->settings()->get('system')->getStr('date_format', false) ?: '%F';
                $time_format = App::blog()->settings()->get('system')->getStr('time_format', false) ?: '%T';
                $user_tz     = is_string($user_tz = App::auth()->getInfo('user_tz')) ? $user_tz : 'UTC';

                while ($metaRecord->fetch()) {
                    $status     = $metaRecord->intField('comment_status') === App::status()->comment()::JUNK ? 'sts-junk' : '';
                    $comment_id = $metaRecord->intField('comment_id');
                    $comment_dt = $metaRecord->strField('comment_dt');
                    $user_id    = $metaRecord->strField('user_id');
                    $post_title = $metaRecord->strField('post_title');

                    $infos = [];
                    if ($large) {
                        $details = __('on') . ' ' .
                            Date::dt2str($date_format, $comment_dt) . ' ' .
                            Date::dt2str($time_format, $comment_dt);
                        $infos[] = (new Text(null, __('by') . ' ' . $user_id));
                        $infos[] = (new Timestamp($details))
                            ->datetime(Date::iso8601((int) strtotime($comment_dt), $user_tz));
                    }

                    yield (new Li('dmpc' . $comment_id))
                        ->class(['line', $status])
                        ->separator(' ')
                        ->items([
                            (new Link())
                                ->href(App::backend()->url()->get('admin.comment', ['id' => $comment_id]))
                                ->text($post_title),
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
     * @return     string  Number of pending comments.
     */
    private static function countPendingComments(): string
    {
        $count = App::blog()->getComments(['comment_status' => App::status()->comment()::PENDING], true)->cardinal();
        if ($count > 0) {
            return (new Link())
                ->href(App::backend()->url()->get('admin.comments', ['status' => App::status()->comment()::PENDING]))
                ->items([
                    (new Span(sprintf(__('(%d pending comment)', '(%d pending comments)', $count), $count)))
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
        App::backend()->page()->jsJson('dm_pending', [
            'postsCounter'    => $preferences->getBool('posts_count'),
            'commentsCounter' => $preferences->getBool('comments_count'),
            'autoRefresh'     => $preferences->getBool('autorefresh'),
            'interval'        => $preferences->getInt('interval', false) ?: 60,
        ]) .
        My::jsLoad('service.js', defer: true);
    }

    /**
     * @param      string                       $name   The name
     * @param      ArrayObject<int, mixed>      $arrayObject   The icon
     */
    public static function adminDashboardFavsIcon(string $name, ArrayObject $arrayObject): string
    {
        $preferences = My::prefs();

        switch ($name) {
            case 'posts':
                if ($preferences->getBool('posts_count')) {
                    // Hack posts title if there is at least one pending post
                    $str = self::countPendingPosts();
                    if ($str !== '') {
                        $third          = is_string($third = $arrayObject[3] ?? '') ? $third : '';
                        $arrayObject[3] = $third . $str;
                    }
                }

                break;
            case 'comments':
                if ($preferences->getBool('comments_count')) {
                    // Hack comments title if there is at least one comment
                    $str = self::countPendingComments();
                    if ($str !== '') {
                        $third          = is_string($third = $arrayObject[3] ?? '') ? $third : '';
                        $arrayObject[3] = $third . $str;
                    }
                }

                break;
        }

        return '';
    }

    /**
     * @param      ArrayObject<int, ArrayObject<int, string>>  $arrayObject  The contents
     */
    public static function adminDashboardContents(ArrayObject $arrayObject): string
    {
        $preferences = My::prefs();

        // Add large modules to the contents stack
        if ($preferences->getBool('posts')) {
            $class = ($preferences->getBool('posts_large') ? 'medium' : 'small');

            $posts_nb = $preferences->getInt('posts_nb', false);

            $ret = (new Div('pending-posts'))
                ->class(['box', $class])
                ->items([
                    (new Text(
                        'h3',
                        (new Img(urldecode((string) App::backend()->page()->getPF(My::id() . '/icon.svg'))))
                            ->alt('')
                            ->class('icon-small')
                        ->render() . ' ' . __('Pending posts')
                    )),
                    (new Text(null, self::getPendingPosts(
                        $posts_nb,
                        $preferences->getBool('posts_large', false)
                    ))),
                ])
            ->render();

            $arrayObject->append(new ArrayObject([$ret]));
        }

        if ($preferences->getBool('comments')) {
            $class = ($preferences->getBool('comments_large') ? 'medium' : 'small');

            $comments_nb = $preferences->getInt('comments_nb', false);

            $ret = (new Div('pending-comments'))
                ->class(['box', $class])
                ->items([
                    (new Text(
                        'h3',
                        (new Img(urldecode((string) App::backend()->page()->getPF(My::id() . '/icon.svg'))))
                            ->alt('')
                            ->class('icon-small')
                        ->render() . ' ' . __('Pending comments')
                    )),
                    (new Text(null, self::getPendingComments(
                        $comments_nb,
                        $preferences->getBool('comments_large', false)
                    ))),
                ])
            ->render();

            $arrayObject->append(new ArrayObject([$ret]));
        }

        return '';
    }

    public static function adminAfterDashboardOptionsUpdate(): string
    {
        $preferences = My::prefs();

        // Get and store user's prefs for plugin options
        try {
            // Post data helpers
            $_Bool = fn (string $name): bool => !empty($_POST[$name]);
            $_Int  = fn (string $name, int $default = 0): int => isset($_POST[$name]) && is_numeric($val = $_POST[$name]) ? (int) $val : $default;

            // Pending posts
            $preferences->put('posts', $_Bool('dmpending_posts'), App::userWorkspace()::WS_BOOL);
            $preferences->put('posts_nb', $_Int('dmpending_posts_nb'), App::userWorkspace()::WS_INT);
            $preferences->put('posts_large', !$_Bool('dmpending_posts_small'), App::userWorkspace()::WS_BOOL);
            $preferences->put('posts_count', $_Bool('dmpending_posts_count'), App::userWorkspace()::WS_BOOL);

            // Pending comments
            $preferences->put('comments', $_Bool('dmpending_comments'), App::userWorkspace()::WS_BOOL);
            $preferences->put('comments_nb', $_Int('dmpending_comments_nb'), App::userWorkspace()::WS_INT);
            $preferences->put('comments_large', !$_Bool('dmpending_comments_small'), App::userWorkspace()::WS_BOOL);
            $preferences->put('comments_count', $_Bool('dmpending_comments_count'), App::userWorkspace()::WS_BOOL);

            // Interval
            $preferences->put('autorefresh', $_Bool('dmpending_autorefresh'), App::userWorkspace()::WS_BOOL);
            $preferences->put('interval', $_Int('dmpending_interval'), App::userWorkspace()::WS_INT);
        } catch (Exception $exception) {
            App::error()->add($exception->getMessage());
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
                (new Checkbox('dmpending_posts_count', $preferences->getBool('posts_count', false)))
                    ->value(1)
                    ->label((new Label(__('Display count of pending posts on posts dashboard icon'), Label::INSIDE_TEXT_AFTER))),
            ]),
            (new Para())->items([
                (new Checkbox('dmpending_posts', $preferences->getBool('posts', false)))
                    ->value(1)
                    ->label((new Label(__('Display pending posts'), Label::INSIDE_TEXT_AFTER))),
            ]),
            (new Para())->items([
                (new Number('dmpending_posts_nb', 1, 999, $preferences->getInt('posts_nb', false) ?: 5))
                    ->label((new Label(__('Number of pending posts to display:'), Label::INSIDE_TEXT_BEFORE))),
            ]),
            (new Para())->items([
                (new Checkbox('dmpending_posts_small', !$preferences->getBool('posts_large', false)))
                    ->value(1)
                    ->label((new Label(__('Small screen'), Label::INSIDE_TEXT_AFTER))),
            ]),
            (new Text(null, '<hr>')),
            (new Text('h5', __('Pending comments'))),
            (new Para())->items([
                (new Checkbox('dmpending_comments_count', $preferences->getBool('comments_count', false)))
                    ->value(1)
                    ->label((new Label(__('Display count of pending comments on comments dashboard icon'), Label::INSIDE_TEXT_AFTER))),
            ]),
            (new Para())->items([
                (new Checkbox('dmpending_comments', $preferences->getBool('comments', false)))
                    ->value(1)
                    ->label((new Label(__('Display pending comments'), Label::INSIDE_TEXT_AFTER))),
            ]),
            (new Para())->items([
                (new Number('dmpending_comments_nb', 1, 999, $preferences->getInt('comments_nb', false) ?: 5))
                    ->label((new Label(__('Number of pending comments to display:'), Label::INSIDE_TEXT_BEFORE))),
            ]),
            (new Para())->items([
                (new Checkbox('dmpending_comments_small', !$preferences->getBool('comments_large', false)))
                    ->value(1)
                    ->label((new Label(__('Small screen'), Label::INSIDE_TEXT_AFTER))),
            ]),
            (new Text(null, '<hr>')),
            (new Para())->items([
                (new Checkbox('dmpending_autorefresh', $preferences->getBool('autorefresh', false)))
                    ->value(1)
                    ->label((new Label(__('Auto refresh'), Label::INSIDE_TEXT_AFTER))),
            ]),
            (new Para())->items([
                (new Number('dmpending_interval', 0, 9_999_999, $preferences->getInt('interval', false)))
                    ->label((new Label(__('Interval in seconds between two refreshes:'), Label::INSIDE_TEXT_BEFORE))),
            ]),
        ])
        ->render();

        return '';
    }
}
