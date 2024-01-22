<?php
/**
 * unserialize
 *
 * @version 1.0.0
 */

namespace Jtsternberg\Unserialize;

use Symfony\Component\Yaml\Yaml;

class Unserializer {

	/**
	 * The data to unserialize.
	 *
	 * @var string
	 */
	public $input;

	/**
	 * The unserialized data.
	 *
	 * @var string
	 */
	public $unserialized;

	/**
	 * The method of unserialization.
	 *
	 * @var string
	 */
	public $method;

	/**
	 * The formatted data.
	 *
	 * @var string
	 */
	public $formatted;

	public function __construct( $input, $method ) {
		$this->input  = $input;
		$this->method = strtolower( $method ?: '' );
		if ( $this->input ) {
			$this->unserialized = self::maybeUnserialize( $this->input );
		}
	}

	public function wrapperEl() {
		$el = 'xmp';
		switch ( $this->method ) {
			case 'krumo':
			case 'dbug':
				$el = 'div';
				break;
			case 'var_dump':
				if ( false !== strpos( $this->getOutput(), 'xdebug' ) ) {
					$el = 'div';
				}
				break;
			case 'javascriptconsole':
				$el = 'blockquote';
				break;
		}

		return $el;
	}

	public function canUnserialize() {
		return ! empty( $this->unserialized );
	}

	public function hasOutput() {
		$val = $this->getOutput();

		return ! empty( $val );
	}

	public function getOutput() {
		if ( $this->canUnserialize() && null === $this->formatted ) {

			$this->formatted = '';

			switch ( $this->method ) {
				case 'print_r':
					$this->formatted = $this->printR();
					break;
				case 'var_dump':
					$this->formatted = $this->varDump();
					break;
				case 'var_export':
					$this->formatted = $this->varExport();
					break;
				case 'json':
					$this->formatted = $this->toJSON();
					break;
				case 'csv':
					$this->formatted = $this->toCSV();
					break;
				case 'urlencode':
					$this->formatted = $this->urlencode();
					break;
				case 'serialize':
					$this->formatted = $this->serialize();
					break;
				case 'krumo':
					$this->formatted = $this->krumo();
					break;
				case 'dbug':
					$this->formatted = $this->dbug();
					break;
				case 'base64':
					$this->formatted = $this->base64();
					break;
				case 'yaml':
					$this->formatted = $this->yaml();
					break;
				case 'javascriptconsole':
					$this->formatted = '
						<script>window.unserializedData = '. $this->toJSON() .';</script>
						<script>console.warn("window.unserializedData");</script>
						<script>console.log(window.unserializedData);</script>
						The unserialized data has been sent to your browser console.
					';
					break;
			}
		}

		return $this->formatted;
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

	public function toCSV() {
		$values = self::isMultiDimensional( $this->unserialized )
			? self::arrayFlatten( $this->unserialized )
			: $this->unserialized;

		$csv = fopen('php://temp/maxmemory:'. (5*1024*1024), 'r+');

		$count = is_countable( $values ) ? count( $values ) : 0;
		if ( $count && isset( $values[ $count - 1 ] ) ) {
			$keys = array_keys( $values[ $count - 1 ] );
			if ( ! empty( $keys ) ) {
				fputcsv( $csv, $keys );
			}
		}

		foreach ( array_values( $values ) as $row ) {
			fputcsv( $csv, $row );
		}
		rewind( $csv );

		return stream_get_contents( $csv );
	}

	public function urlencode() {
		return http_build_query( $this->unserialized );
	}

	public function serialize() {
		return @serialize( $this->unserialized );
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

	public function base64() {
		return base64_encode( $this->input );
	}

	public function yaml() {
		return Yaml::dump( $this->unserialized, 10, 3 );
	}

	/**
	 * Unserialize value only if it was serialized.
	 * Thank you WordPress.
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
	 * Thank you WordPress.
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

	/**
	 * Determine if array is multi-dimensional.
	 *
	 * @since  1.1.0
	 *
	 * @param  array $array Array to check.
	 *
	 * @return bool         Whether given array is multi-dimensional.
	 */
	public static function isMultiDimensional( $array ) {
		foreach ( (array) $array as $key => $value ) {
			foreach ( $value as $vkey => $vvalue ) {
				if ( $vvalue && ! is_scalar( $vvalue ) ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Flattens a multi-dimensional array.
	 *
	 * @since  1.1.0
	 *
	 * @param  array $array Array to flatten.
	 *
	 * @return array        Flattened array.
	 */
	public static function arrayFlatten( $array ) {
		$return = [];
		foreach ( $array as $key => $value ) {
			if ( is_array( $value ) ) {
				$return = array_merge( $return, self::arrayFlatten( $value ) );
			} else {
				$return[ $key ] = $value;
			}
		}

		return $return;
	}
}