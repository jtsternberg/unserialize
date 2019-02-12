<?php
/**
 * unserialize
 *
 * @version 1.0.0
 */

namespace Jtsternberg\Unserialize;

class Unserializer {


	/**
	 * The data to unserialize.
	 *
	 * @var string
	 */
	public $input;

	/**
	 * The method of unserialization.
	 *
	 * @var string
	 */
	public $output;

	public function __construct( $input, $output ) {
		$this->input  = $input;
		$this->output = strtolower( $output );
		$this->unserialized = self::maybeUnserialize( $this->input );
	}

	public function wrapperEl() {
		$el = 'xmp';
		switch ( $this->output ) {
			case 'krumo':
			case 'dbug':
				$el = 'div';
				break;
			case 'javascriptconsole':
				$el = 'blockquote';
				break;
		}

		return $el;
	}

	public function getOutput() {
		$output = '';

		switch ( $this->output ) {
			case 'print_r':
				$output = $this->printR();
				break;
			case 'var_dump':
				$output = $this->varDump();
				break;
			case 'var_export':
				$output = $this->varExport();
				break;
			case 'json':
				$output = $this->toJSON();
				break;
			case 'krumo':
				$output = $this->krumo();
				break;
			case 'dbug':
				$output = $this->dbug();
				break;
			case 'javascriptconsole':
				$output = '
					<script>window.unserializedData = '. $this->toJSON() .';</script>
					<script>console.warn("window.unserializedData");</script>
					<script>console.log(window.unserializedData);</script>
					The unserialized data has been sent to your browser console.
				';
				break;
		}

		return $output;
	}

	public function printR() {
		return print_r( $this->unserialized, true );
	}

	public function varDump() {
		ob_start();
		var_dump( $this->unserialized );
		return ob_get_clean();
	}

	public function varExport() {
		$output = var_export( $this->unserialized, true );

		$output = str_replace(
			[
				'stdClass::__set_state',
				'array (',
				"=> \n ",
				"=> \r ",
			],
			[
				'(object) ',
				'array(',
				'=>',
			],
			$output
		);

		$output = preg_replace(
			[
				"/  /",
				"/\s+=>\s+/",
			],
			[
				"\t",
				" => ",
			],
			$output
		);

		return "<?php\n{$output};";
	}

	public function toJSON() {
		return json_encode( $this->unserialized, JSON_PRETTY_PRINT );
	}

	public function krumo() {
		ob_start();
		krumo( $this->unserialized );
		return ob_get_clean();
	}

	public function dbug() {
		ob_start();
		new \dBug\dBug( $this->unserialized );
		return ob_get_clean();
	}

	/**
	 * Unserialize value only if it was serialized.
	 *
	 * @since 1.0.0
	 *
	 * @param string $original Maybe unserialized original, if is needed.
	 * @return mixed Unserialized data can be any type.
	 */
	public static function maybeUnserialize( $original ) {
		if ( self::isSerialized( $original ) ) { // don't attempt to unserialize data that wasn't serialized going in
			return @unserialize( $original );
		}
		return $original;
	}

	/**
	 * Check value to find if it was serialized.
	 *
	 * If $data is not an string, then returned value will always be false.
	 * Serialized data is always a string.
	 *
	 * @since 1.0.0
	 *
	 * @param string $data   Value to check to see if was serialized.
	 * @param bool   $strict Optional. Whether to be strict about the end of the string. Default true.
	 * @return bool False if not serialized and true if it was.
	 */
	public static function isSerialized( $data, $strict = true ) {
		// if it isn't a string, it isn't serialized.
		if ( ! is_string( $data ) ) {
			return false;
		}
		$data = trim( $data );
		if ( 'N;' == $data ) {
			return true;
		}
		if ( strlen( $data ) < 4 ) {
			return false;
		}
		if ( ':' !== $data[1] ) {
			return false;
		}
		if ( $strict ) {
			$lastc = substr( $data, -1 );
			if ( ';' !== $lastc && '}' !== $lastc ) {
				return false;
			}
		} else {
			$semicolon = strpos( $data, ';' );
			$brace     = strpos( $data, '}' );
			// Either ; or } must exist.
			if ( false === $semicolon && false === $brace ) {
				return false;
			}
			// But neither must be in the first X characters.
			if ( false !== $semicolon && $semicolon < 3 ) {
				return false;
			}
			if ( false !== $brace && $brace < 4 ) {
				return false;
			}
		}
		$token = $data[0];
		switch ( $token ) {
			case 's':
				if ( $strict ) {
					if ( '"' !== substr( $data, -2, 1 ) ) {
						return false;
					}
				} elseif ( false === strpos( $data, '"' ) ) {
					return false;
				}
				// or else fall through
			case 'a':
			case 'O':
				return (bool) preg_match( "/^{$token}:[0-9]+:/s", $data );
			case 'b':
			case 'i':
			case 'd':
				$end = $strict ? '$' : '';
				return (bool) preg_match( "/^{$token}:[0-9.E-]+;$end/", $data );
		}
		return false;
	}
}