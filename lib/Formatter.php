<?php
/**
 * unserialize
 *
 * @version 1.0.0
 */

namespace Jtsternberg\Unserialize;

use Symfony\Component\Yaml\Yaml;

class Formatter {
	public static function parse( $input, $type ) {
		if ( ! empty( $type ) ) {
			if ( self::isBase64Encoded( $input ) ) {
				$input = base64_decode( $input );
			}

			if ( $type ) {
				switch ( strtolower( $type ) ) {
					case 'json':
						$input = serialize( json_decode( $input, true ) );
						break;
					case 'urlencode':
						mb_parse_str( $input, $input );
						$input = serialize( $input );
						break;
					case 'yaml':
						$input = serialize( Yaml::parse( $input ) );
						break;
					case 'csv':
						$tabs  = strpos( $input, "\t" );
						$delim = false !== $tabs ? "\t" : ',';
						$fp    = fopen( 'php://temp', 'r+' );
						fputs( $fp, $input );
						rewind( $fp );
						$csv = [];
						while ( ( $data = fgetcsv( $fp, 0, $delim ) ) !== FALSE ) {
						    $csv[] = $data;
						}
						$input = $csv;
						break;
				}
			}
		}

		return $input;
	}

	/**
	 * Checks if a string is base64_encoded.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $string The string used to check.
	 * @return bool           Whether or not the string is base64_encoded.
	 */
	public static function isBase64Encoded( $string = '' ) {
		return base64_encode( trim( base64_decode( $string, true ) ) ) === trim( $string );
	}
}