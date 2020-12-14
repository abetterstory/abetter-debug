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
		global $view_data; $view = \Arr::last($view_data??[]);
		$file = ($view['path']??'').'/'.($view['file']??'');
		$file = preg_replace('/(.*)resources\/views\/(.*)/',"$2",$file);
	 	echo "{$prefix}view:{$file}{$suffix}";
	}
}
