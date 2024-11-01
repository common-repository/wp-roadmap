<?php

class RMPF_Api {
    public $module = 'test';
	public $nameSpace;

	function __construct() {

		$this->nameSpace = 'wp/v2/block-renderer/feedback';

		add_action( 'rest_api_init', function () {
			register_rest_route( $this->nameSpace . '/api/v1/' . $this->module, '/render_shortcode', array(
				'methods'             => WP_REST_Server::ALLMETHODS,
				'callback'            => [ $this, 'render_shortcode' ],
				'permission_callback' => '__return_true'
			));
		});
    }

    public function render_shortcode(){
		ob_start();
			$data = require_once(RMPF_PATH.'includes/block-widget.php');
       ob_end_clean();
        return wp_send_json([
			'data' => $data,
			'status' => true
		]);
	}
}