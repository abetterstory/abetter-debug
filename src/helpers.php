<?php

if (!function_exists('_console')) {
	function _console($first,$second=NULL) {
		if (!env('APP_DEBUG')) return;
		if (in_array(strtolower(env('APP_ENV')),['stage','production'])) return;
		if ($second && is_string($second) && !is_string($first)) {
			$move = $first; $first = $second; $second = $move;
		}
		$echo = "<script>console.log(";
		$echo .= (is_string($first)) ? "'{$first}'" : json_encode($first);
		if ($second) {
			$echo .= ",";
			$echo .= (is_string($second)) ? "'{$second}'" : json_encode($second);
		}
		$echo .= ")</script>";
		echo $echo;
	}
}

if (!function_exists('_debug')) {
	function _debug($message="",$type='html') {
		if (!env('APP_DEBUG')) return;
		if (in_array(strtolower(env('APP_ENV')),['stage','production'])) return;
		$prefix = ""; $suffix = "";
		if ($type == 'txt') $prefix = "# ";
		if ($type == 'html') { $prefix = "<!-- "; $suffix = " -->"; };
		if ($message) { echo "{$prefix}{$message}{$suffix}"; return; }
	 	$trace = debug_backtrace(NULL,1);
	 	$file = preg_replace('/.*\/abetter\/(.*)\.(.*)$/',"$1",$trace[0]['file']);
	 	echo "{$prefix}include:{$file}{$suffix}";
	}
}
