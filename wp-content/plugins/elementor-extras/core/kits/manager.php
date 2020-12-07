<?php
namespace ElementorExtras\Core\Kits;

use ElementorExtras\Core\Kits\Documents\Kit;
use Elementor\Core\Kits\Manager as Kits_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Manager extends Kits_Manager {

	/**
	 * @param Documents_Manager $documents_manager
	 */
	public function register_document( $documents_manager ) {
		$documents_manager->register_document_type( 'kit', Kit::get_class_full_name() );
	}

}
