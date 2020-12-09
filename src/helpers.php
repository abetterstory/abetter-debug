<?php

if (!function_exists('_console')) {
	function _console($first,$second=NULL) {
		if (in_array(env('APP_ENV'),['production','stage'])) return;
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
