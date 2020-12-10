<?php

namespace ABetter\Debug;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class BladeServiceProvider extends ServiceProvider {

    public function boot() {

		// Console
        Blade::directive('console', function($expression){
			if (in_array(env('APP_ENV'),['production','stage'])) return "";
			list($first,$second) = self::parseMessage($expression);
			return self::parsePrint($first,$second);
        });

		// Debug
        Blade::directive('debug', function($expression){
			if (!env('APP_DEBUG')) return;
			list($message,$opt) = self::parseExpression($expression);
			if ($opt != '[]') return "<?php echo _debug('{$message}',{$opt}); ?>";
			return "<?php echo _debug('{$message}'); ?>";
        });

    }

    public function register() {
        //
    }

	// ---

	protected static function parseExpression($parse) {
		$id = trim(strtok($parse,','));
		$vars = trim(str_replace($id,'',$parse),',');
		$vars = preg_replace('/(\'|") ?(=&gt;|=) ?(\'|")/',"$1 => $3",$vars);
		$end = trim(preg_match('/, ?(end|true|1)$/i',$parse));
		if ($end) $vars = trim(substr($vars,0,strrpos($vars,',')));
		$exp = array();
		$exp[0] = trim($id,'\'');
		$exp[1] = ($vars) ? $vars : '[]';
		$exp[2] = ($end) ? TRUE : FALSE;
		return $exp;
	}

	protected static function parseMessage($parse,$return=[]) {
		$split = preg_split('/,/',$parse);
		$first = self::parseParam($split[0]??NULL);
		$second = self::parseParam($split[1]??NULL);
		if ($second && preg_match('/^\'/',$second) && !preg_match('/^\'/',$first)) {
			$move = $first; $first = $second; $second = $move;
		}
		$return[0] = $first;
		$return[1] = $second;
		return $return;
	}

	protected static function parseParam($parse,$return="") {
		$parse = trim($parse);
		if (preg_match('/^(\"|\')/',$parse)) {
			$return = preg_replace('/\"/',"'",$parse);
		} else if (preg_match('/^(\$|\[)/',$parse)) {
			$return = "{$parse}";
		}
		return $return;
	}

	protected static function parsePrint($first,$second="") {
		$echo = "<?php echo \"<script>console.log(";
		$echo .= (preg_match('/^(\"|\')/',$first)) ? "{$first}" : "\".json_encode({$first}).\"";
		if ($second) {
			$echo .= ",";
			$echo .= (preg_match('/^(\"|\')/',$second)) ? "{$second}" : "\".json_encode({$second}).\"";
		}
		$echo .= ")</script>\"; ?>";
		return $echo;
	}

}
