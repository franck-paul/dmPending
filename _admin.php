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
		// Get and store user's prefs for plugin options
	}
	
	public static function adminPreferencesForm($core)
	{
		// Add fieldset for plugin options
	}
}
?>