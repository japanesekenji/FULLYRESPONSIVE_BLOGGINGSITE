<?php
namespace ElementorExtras\Core\Kits\Documents\Tabs;

use Elementor\Core\Kits\Documents\Tabs\Tab_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Settings_Tooltips extends Tab_Base {

	public function get_id() {
		return 'settings-tooltips';
	}

	public function get_title() {
		return __( 'Tooltips', 'elementor' );
	}

	protected function register_tab_controls() {
		$this->start_controls_section(
			'section_' . $this->get_id(),
			[
				'label' => $this->get_title(),
				'tab' => $this->get_id(),
			]
		);

		$this->end_controls_section();
	}
}
