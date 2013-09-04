<?php
# -- BEGIN LICENSE BLOCK ---------------------------------------
#
# This file is part of Dotclear 2.
#
# Copyright (c) 2011-2012 Franck Paul
# Licensed under the GPL version 2.0 license.
# See LICENSE file or
# http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
#
# -- END LICENSE BLOCK -----------------------------------------
if (!defined('DC_CONTEXT_ADMIN')) { return; }

// Dashboard behaviours
$core->addBehavior('adminDashboardItems',array('dmPendingBehaviors','adminDashboardItems'));
$core->addBehavior('adminDashboardContents',array('dmPendingBehaviors','adminDashboardContents'));
$core->addBehavior('adminDashboardFavsIcon',array('dmPendingBehaviors','adminDashboardFavsIcon'));

// User-preferecences behaviours
if (version_compare(DC_VERSION,'2.4.4','<=')) {
	$core->addBehavior('adminBeforeUserUpdate',array('dmPendingBehaviors','adminBeforeUserUpdate'));
}
$core->addBehavior('adminBeforeUserOptionsUpdate',array('dmPendingBehaviors','adminBeforeUserUpdate'));
$core->addBehavior('adminPreferencesForm',array('dmPendingBehaviors','adminPreferencesForm'));

# BEHAVIORS
class dmPendingBehaviors
{
	private static function getPendingPosts($core,$nb,$large)
	{
		// Get last $nb pending posts
		$params = array('post_status' => -2);
		if ((integer) $nb > 0) {
			$params['limit'] = (integer) $nb;
		}
		$rs = $core->blog->getPosts($params,false);
		if (!$rs->isEmpty()) {
			$ret = '<ul>';
			while ($rs->fetch()) {
				$ret .= '<li>';
				$ret .= '<a href="post.php?id='.$rs->post_id.'">'.$rs->post_title.'</a>';
				if ($large) {
					$ret .= ' ('.
						__('by').' '.$rs->user_id.' '.__('on').' '.
						dt::dt2str($core->blog->settings->system->date_format,$rs->post_dt).' '.
						dt::dt2str($core->blog->settings->system->time_format,$rs->post_dt).')';
				}
				$ret .= '</li>';
			}
			$ret .= '</ul>';
			$ret .= '<p><a href="posts.php?status=-2">'.__('See all pending posts').'</a></p>';
			return $ret;
		} else {
			return '<p>'.__('No pending post').'</p>';
		}
	}

	private static function countPendingPosts($core)
	{
		$count = $core->blog->getPosts(array('post_status'=>-2),true)->f(0);
		if ($count) {
			$str = ($count > 1) ? __('(%d pending posts)') : __('(%d pending post)');
			return '</span></a> <br /><a href="posts.php?status=-2"><span>'.sprintf($str,$count);
		} else {
			return '';
		}
	}
		
	private static function getPendingComments($core,$nb,$large)
	{
		// Get last $nb pending comments
		$params = array('comment_status' => -1);
		if ((integer) $nb > 0) {
			$params['limit'] = (integer) $nb;
		}
		$rs = $core->blog->getComments($params,false);
		if (!$rs->isEmpty()) {
			$ret = '<ul>';
			while ($rs->fetch()) {
				$ret .= '<li>';
				$ret .= '<a href="comment.php?id='.$rs->comment_id.'">'.$rs->post_title.'</a>';
				if ($large) {
					$ret .= ' ('.
						__('by').' '.$rs->comment_author.' '.__('on').' '.
						dt::dt2str($core->blog->settings->system->date_format,$rs->comment_dt).' '.
						dt::dt2str($core->blog->settings->system->time_format,$rs->comment_dt).')';
				}
				$ret .= '</li>';
			}
			$ret .= '</ul>';
			$ret .= '<p><a href="comments.php?status=-1">'.__('See all pending comments').'</a></p>';
			return $ret;
		} else {
			return '<p>'.__('No pending comment').'</p>';
		}
	}

	private static function countPendingComments($core)
	{
		$count = $core->blog->getComments(array('comment_status'=>-1),true)->f(0);
		if ($count) {
			$str = ($count > 1) ? __('(%d pending comments)') : __('(%d pending comment)');
			return '</span></a> <br /><a href="comments.php?status=-1"><span>'.sprintf($str,$count);
		} else {
			return '';
		}
	}

	public static function adminDashboardFavsIcon($core,$name,$icon)
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
	
	public static function adminDashboardItems($core,$items)
	{
		// Add small modules to the items stack
		$core->auth->user_prefs->addWorkspace('dmpending');
		if ($core->auth->user_prefs->dmpending->pending_posts && !$core->auth->user_prefs->dmpending->pending_posts_large) {
			$ret = '<div id="pending-posts">'.'<h3>'.'<img src="index.php?pf=dmPending/icon.png" alt="" />'.' '.__('Pending posts').'</h3>';
			$ret .= dmPendingBehaviors::getPendingPosts($core,$core->auth->user_prefs->dmpending->pending_posts_nb,false);
			$ret .= '</div>';
			$items[] = new ArrayObject(array($ret));
		}
		if ($core->auth->user_prefs->dmpending->pending_comments && !$core->auth->user_prefs->dmpending->pending_comments_large) {
			$ret = '<div id="pending-comments">'.'<h3>'.'<img src="index.php?pf=dmPending/icon.png" alt="" />'.' '.__('Pending comments').'</h3>';
			$ret .= dmPendingBehaviors::getPendingComments($core,$core->auth->user_prefs->dmpending->pending_comments_nb,false);
			$ret .= '</div>';
			$items[] = new ArrayObject(array($ret));
		}
	}

	public static function adminDashboardContents($core,$contents)
	{
		// Add large modules to the contents stack
		$core->auth->user_prefs->addWorkspace('dmpending');
		if ($core->auth->user_prefs->dmpending->pending_posts && $core->auth->user_prefs->dmpending->pending_posts_large) {
			$ret = '<div id="pending-posts">'.'<h3>'.'<img src="index.php?pf=dmPending/icon.png" alt="" />'.' '.__('Pending posts').'</h3>';
			$ret .= dmPendingBehaviors::getPendingPosts($core,$core->auth->user_prefs->dmpending->pending_posts_nb,true);
			$ret .= '</div>';
			$contents[] = new ArrayObject(array($ret));
		}
		if ($core->auth->user_prefs->dmpending->pending_comments && $core->auth->user_prefs->dmpending->pending_comments_large) {
			$ret = '<div id="pending-comments">'.'<h3>'.'<img src="index.php?pf=dmPending/icon.png" alt="" />'.' '.__('Pending comments').'</h3>';
			$ret .= dmPendingBehaviors::getPendingComments($core,$core->auth->user_prefs->dmpending->pending_comments_nb,true);
			$ret .= '</div>';
			$contents[] = new ArrayObject(array($ret));
		}
	}

	public static function adminBeforeUserUpdate($cur,$userID)
	{
		global $core;

		// Get and store user's prefs for plugin options
		$core->auth->user_prefs->addWorkspace('dmpending');
		try {
			// Pending posts
			$core->auth->user_prefs->dmpending->put('pending_posts',!empty($_POST['dmpending_posts']),'boolean');
			$core->auth->user_prefs->dmpending->put('pending_posts_nb',(integer)$_POST['dmpending_posts_nb'],'integer');
			$core->auth->user_prefs->dmpending->put('pending_posts_large',!empty($_POST['dmpending_posts_large']),'boolean');
			$core->auth->user_prefs->dmpending->put('pending_posts_count',!empty($_POST['dmpending_posts_count']),'boolean');
			// Pending comments
			$core->auth->user_prefs->dmpending->put('pending_comments',!empty($_POST['dmpending_comments']),'boolean');
			$core->auth->user_prefs->dmpending->put('pending_comments_nb',(integer)$_POST['dmpending_comments_nb'],'integer');
			$core->auth->user_prefs->dmpending->put('pending_comments_large',!empty($_POST['dmpending_comments_large']),'boolean');
			$core->auth->user_prefs->dmpending->put('pending_comments_count',!empty($_POST['dmpending_comments_count']),'boolean');
		} 
		catch (Exception $e)
		{
			$core->error->add($e->getMessage());
		}
	}
	
	public static function adminPreferencesForm($core)
	{
		// Add fieldset for plugin options
		$core->auth->user_prefs->addWorkspace('dmpending');

		echo '<div class="two-cols">';
		echo '<div class="col">';

		echo '<fieldset><legend>'.__('Pending posts on dashboard').'</legend>'.
		
		'<p>'.
		form::checkbox('dmpending_posts_count',1,$core->auth->user_prefs->dmpending->pending_posts_count).' '.
		'<label for"dmpending_posts_count" class="classic">'.__('Display count of pending posts on posts dashboard icon').'</label></p>'.
		
		'<p>'.
		form::checkbox('dmpending_posts',1,$core->auth->user_prefs->dmpending->pending_posts).' '.
		'<label for="dmpending_posts" class="classic">'.__('Display pending posts').'</label></p>'.

		'<p><label for="dmpending_posts_nb">'.__('Number of pending posts to display:').'</label>'.
		form::field('dmpending_posts_nb',2,3,(integer) $core->auth->user_prefs->dmpending->pending_posts_nb).
		'</p>'.

		'<p>'.
		form::checkbox('dmpending_posts_large',1,$core->auth->user_prefs->dmpending->pending_posts_large).' '.
		'<label for="dmpending_posts_large" class="classic">'.__('Display pending posts in large section (under favorites)').'</label></p>'.

		'<br class="clear" />'. //Opera sucks
		'</fieldset>';

		echo '</div>';
		echo '<div class="col">';

		echo '<fieldset><legend>'.__('Pending comments on dashboard').'</legend>'.
		
		'<p>'.
		form::checkbox('dmpending_comments_count',1,$core->auth->user_prefs->dmpending->pending_comments_count).' '.
		'<label for"dmpending_comments_count" class="classic">'.__('Display count of pending comments on comments dashboard icon').'</label></p>'.

		'<p>'.
		form::checkbox('dmpending_comments',1,$core->auth->user_prefs->dmpending->pending_comments).' '.
		'<label for="dmpending_comments" class="classic">'.__('Display pending comments').'</label></p>'.

		'<p><label for="dmpending_comments_nb">'.__('Number of pending comments to display:').'</label>'.
		form::field('dmpending_comments_nb',2,3,(integer) $core->auth->user_prefs->dmpending->pending_comments_nb).
		'</p>'.

		'<p>'.
		form::checkbox('dmpending_comments_large',1,$core->auth->user_prefs->dmpending->pending_comments_large).' '.
		'<label for="dmpending_comments_large" class="classic">'.__('Display pending comments in large section (under favorites)').'</label></p>'.

		'<br class="clear" />'. //Opera sucks
		'</fieldset>';

		echo '</div>';
		echo '</div>';
	}
}
?>