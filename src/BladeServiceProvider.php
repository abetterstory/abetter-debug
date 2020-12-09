<?php

namespace ABetter\Debug;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class BladeServiceProvider extends ServiceProvider {

    public function boot() {

        Blade::directive('console', function($expression){
			if (in_array(env('APP_ENV'),['production','stage'])) return "";
			list($first,$second) = self::parseExpression($expression);
			return self::parsePrint($first,$second);
        });

    }

    public function register() {
        //
    }

	// ---

	public static function parseExpression($parse,$return=[]) {
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

	public static function parseParam($parse,$return="") {
		$parse = trim($parse);
		if (preg_match('/^(\"|\')/',$parse)) {
			$return = preg_replace('/\"/',"'",$parse);
		} else if (preg_match('/^(\$|\[)/',$parse)) {
			$return = "{$parse}";
		}
		return $return;
	}

	public static function parsePrint($first,$second="") {
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
