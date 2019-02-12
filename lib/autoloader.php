<?php
/**
 * unserialize
 *
 * @version 1.0.0
 */

namespace Jtsternberg\Unserialize;

define( 'JTSTERNBERG_UNSERIALIZE_DIR', dirname( __DIR__ ) );

/**
 * Attempts to load Composer's autoload.php as either a dependency or a
 * stand-alone package.
 *
 * @return bool
 */
function autoloader() {
	$files = [
	  dirname( dirname( JTSTERNBERG_UNSERIALIZE_DIR ) ) . '/autoload.php', // composer dependency
	  JTSTERNBERG_UNSERIALIZE_DIR . '/vendor/autoload.php', // stand-alone package
	];
	foreach ( $files as $file ) {
		if ( is_file( $file ) ) {
			return require_once $file;
		}
	}

	die( 'Autoloader is missing. Try running `composer install` in the `'. JTSTERNBERG_UNSERIALIZE_DIR . "` directory.\n\n" );
};
autoloader();