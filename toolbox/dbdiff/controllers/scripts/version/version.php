<?php
namespace toolbox;
class version_controller {

	function __construct(){
		utils::vd(exec('pwd'));
		$git_log = array();
		exec("git log -1", $git_log);
		$git_log = implode("\n", $git_log);
		$parsed_log = utils::parseGitLog($git_log);
		$parsed_log = array_reverse($parsed_log);

		$version = array();
		exec("git describe --long", $version);
		$version = $version[0];



		foreach($parsed_log as $key => $log){
			db::query('insert ignore into `changelog` (
				`hash`,
				`version`,
				`author`,
				`message`,
				`date`,
				`commit_date`
			) VALUES (
				'.db::quote($log['hash']).',
				'.db::quote($version).',
				'.db::quote($log['author']).',
				'.db::quote($log['message']).',
				'.db::quote(date('Y-m-d H:i:s', filemtime('../.git/logs/refs/heads/master'))).',
				'.db::quote(date('Y-m-d H:i:s', strtotime($log['date']))).'
			)');
		}

		exit;

	}


}