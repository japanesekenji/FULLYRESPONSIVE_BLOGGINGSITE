<?php
namespace ElementorExtras\Core\Kits\Documents;

use Elementor\Core\Kits\Documents\Kit as Elementor_Kit;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Kit extends Elementor_Kit {

	/**
	 * @var Tabs\Tab_Base[]
	 */
	private $tabs;

	public function __construct( array $data = [] ) {
		parent::__construct( $data );

		$this->tabs['settings-tooltips'] = new Tabs\Settings_Tooltips( $this );
	}
}
