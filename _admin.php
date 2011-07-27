<?php
# -- BEGIN LICENSE BLOCK ---------------------------------------
#
# This file is part of Dotclear 2.
#
# Copyright (c) 2003-2011 Olivier Meunier & Association Dotclear
# Licensed under the GPL version 2.0 license.
# See LICENSE file or
# http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
#
# -- END LICENSE BLOCK -----------------------------------------
if (!defined('DC_CONTEXT_ADMIN')) { return; }

// Dashboard behaviours
$core->addBehavior('adminDashboardItems',array('dmPendingBehaviors','adminDashboardItems'));
$core->addBehavior('adminDashboardContents',array('dmPendingBehaviors','adminDashboardContents'));

// User-preferecences behaviours
$core->addBehavior('adminBeforeUserUpdate',array('dmPendingBehaviors','adminBeforeUserUpdate'));
$core->addBehavior('adminPreferencesForm',array('dmPendingBehaviors','adminPreferencesForm'));

# BEHAVIORS
class dmPendingBehaviors
{
	public static function adminDashboardItems($core,$items)
	{
		// Add small modules to be displayed
		$core->auth->user_prefs->addWorkspace('dmpending');
		if ($core->auth->user_prefs->dmpending->pending_posts && !$core->auth->user_prefs->dmpending->pending_posts_large) {
			$ret = '<div id="">'.'<h3>'.__('Pending posts').'</h3>';
			$ret .= '</div>';
			$items[] = new ArrayObject(array($ret));
		}
		if ($core->auth->user_prefs->dmpending->pending_comments && !$core->auth->user_prefs->dmpending->pending_comments_large) {
			$ret = '<div id="">'.'<h3>'.__('Pending comments').'</h3>';
			$ret .= '</div>';
			$items[] = new ArrayObject(array($ret));
		}
	}

	public static function adminDashboardContents($core,$contents)
	{
		// Add large modules to be displayed
		$core->auth->user_prefs->addWorkspace('dmpending');
		if ($core->auth->user_prefs->dmpending->pending_posts && $core->auth->user_prefs->dmpending->pending_posts_large) {
			$ret = '<div id="">'.'<h3>'.__('Pending posts').'</h3>';
			$ret .= '</div>';
			$contents[] = new ArrayObject(array($ret));
		}
		if ($core->auth->user_prefs->dmpending->pending_comments && $core->auth->user_prefs->dmpending->pending_comments_large) {
			$ret = '<div id="">'.'<h3>'.__('Pending comments').'</h3>';
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
			// Pending comments
			$core->auth->user_prefs->dmpending->put('pending_comments',!empty($_POST['dmpending_comments']),'boolean');
			$core->auth->user_prefs->dmpending->put('pending_comments_nb',(integer)$_POST['dmpending_comments_nb'],'integer');
			$core->auth->user_prefs->dmpending->put('pending_comments_large',!empty($_POST['dmpending_comments_large']),'boolean');
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
		
		'<p><label for"dmpending_posts" class="classic">'.
		form::checkbox('dmpending_posts',1,$core->auth->user_prefs->dmpending->pending_posts).' '.
		__('Display pending posts').'</label></p>'.

		'<p><label for"dmpending_posts_nb">'.__('Number of pending posts to display:').
		form::field('dmpending_posts_nb',2,3,(integer) $core->auth->user_prefs->dmpending->pending_posts_nb).
		'</label></p>'.

		'<p><label for"dmpending_posts_large" class="classic">'.
		form::checkbox('dmpending_posts_large',1,$core->auth->user_prefs->dmpending->pending_posts_large).' '.
		__('Display pending posts in large section (under favorites)').'</label></p>'.

		'<br class="clear" />'. //Opera sucks
		'</fieldset>';

		echo '</div>';
		echo '<div class="col">';

		echo '<fieldset><legend>'.__('Pending comments on dashboard').'</legend>'.
		
		'<p><label for"dmpending_comments" class="classic">'.
		form::checkbox('dmpending_comments',1,$core->auth->user_prefs->dmpending->pending_comments).' '.
		__('Display pending comments').'</label></p>'.

		'<p><label for"dmpending_comments_nb">'.__('Number of pending comments to display:').
		form::field('dmpending_comments_nb',2,3,(integer) $core->auth->user_prefs->dmpending->pending_comments_nb).
		'</label></p>'.

		'<p><label for"dmpending_comments_large" class="classic">'.
		form::checkbox('dmpending_comments_large',1,$core->auth->user_prefs->dmpending->pending_comments_large).' '.
		__('Display pending comments in large section (under favorites)').'</label></p>'.

		'<br class="clear" />'. //Opera sucks
		'</fieldset>';

		echo '</div>';
		echo '</div>';
	}
}
?>