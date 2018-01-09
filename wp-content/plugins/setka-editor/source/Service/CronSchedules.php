<?php
namespace Setka\Editor\Service;

use Setka\Editor\Plugin;

class CronSchedules {

	public static function addSchedules($schedules) {
		$schedules[Plugin::_NAME_ . '_every_minute'] = array(
			'interval' 	=> MINUTE_IN_SECONDS,
			'display' 	=> __('Every minute (60 seconds)', Plugin::NAME)
		);

		return $schedules;
	}
}
