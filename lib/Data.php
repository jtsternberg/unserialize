<?php
/**
 * unserialize
 *
 * @version 1.0.0
 */

namespace Jtsternberg\Unserialize;

use Symfony\Component\Yaml\Yaml;

class Data {

	/**
	 * The form data.
	 *
	 * @var array
	 */
	public $data;

	public function __construct( $data ) {
		$this->data  = $data;
	}

	public function has( string $key, $checkEmpty = false ) {
		return $checkEmpty
			? ! empty( $this->data[ $key ] )
			: isset( $this->data[ $key ] );
	}

	public function get( string $key, $fallback = null, $checkEmpty = false ) {
		return $this->has( $key, $checkEmpty )
			? $this->data[ $key ]
			: $fallback;
	}

}