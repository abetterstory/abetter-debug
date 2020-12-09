<?php

namespace ABetter\Debug;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class BladeServiceProvider extends ServiceProvider {

    public function boot() {

        Blade::directive('console', function($expression){
			list($file,$vars,$link) = self::parseExpression($expression);
			return "<?php echo '<script>console.log(\"test\");</script>'; ?>";
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

}
