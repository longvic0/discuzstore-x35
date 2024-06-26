<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: cron_publish_halfhourly.php 31463 2012-08-30 08:59:17Z zhangguosheng $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

require_once libfile('function/forum');
require_once libfile('function/post');

loadcache('cronpublish');

$dataChanged = false;
$cron_publish_ids = array();
$cron_publish_ids = getglobal('cache/cronpublish');
if (is_array($cron_publish_ids) && count($cron_publish_ids) > 0) {
	$threadall = C::t('forum_thread')->fetch_all_by_tid($cron_publish_ids);

	foreach ($cron_publish_ids as $tid) {
		if(!$threadall[$tid]) {
			unset($cron_publish_ids[$tid]);
			$dataChanged = true;
		}
	}

	foreach ($threadall as $stid=>$sdata) {
		if ($sdata['dateline'] <= getglobal('timestamp')) {
			threadpubsave($stid, true);
			unset($cron_publish_ids[$stid]);
			$dataChanged = true;
		}
	}

	if ($dataChanged === true) {
		savecache('cronpublish', $cron_publish_ids);
	}
}
if (!is_array($cron_publish_ids)) {
	$cron_publish_try = dunserialize($cron_publish_ids);
	if (!is_array($cron_publish_try)) {
		$cron_publish_try = array();
	}
	savecache('cronpublish', $cron_publish_try);
}

?>