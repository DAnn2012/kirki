<?php
/**
 * Adds the Webfont Loader to load fonts asyncronously.
 *
 * @package     Kirki
 * @category    Core
 * @author      Aristeides Stathopoulos
 * @copyright   Copyright (c) 2017, Aristeides Stathopoulos
 * @license     http://opensource.org/licenses/https://opensource.org/licenses/MIT
 * @since       3.0
 */

/**
 * Manages the way Google Fonts are enqueued.
 */
final class Kirki_Modules_Webfonts_Async {

	/**
	 * The config ID.
	 *
	 * @access protected
	 * @since 3.0.0
	 * @var string
	 */
	protected $config_id;

	/**
	 * The Kirki_Modules_Webfonts object.
	 *
	 * @access protected
	 * @since 3.0.0
	 * @var object
	 */
	protected $webfonts;

	/**
	 * The Kirki_Fonts_Google object.
	 *
	 * @access protected
	 * @since 3.0.0
	 * @var object
	 */
	protected $googlefonts;

	/**
	 * Constructor.
	 *
	 * @access public
	 * @since 3.0
	 * @param string $config_id   The config-ID.
	 * @param object $webfonts    The Kirki_Modules_Webfonts object.
	 * @param object $googlefonts The Kirki_Fonts_Google object.
	 * @param array  $args        Extra args we want to pass.
	 */
	public function __construct( $config_id, $webfonts, $googlefonts, $args = array() ) {

		$this->config_id   = $config_id;
		$this->webfonts    = $webfonts;
		$this->googlefonts = $googlefonts;

		add_action( 'wp_head', array( $this, 'webfont_loader' ) );
	}

	/**
	 * Webfont Loader for Google Fonts.
	 *
	 * @access public
	 * @since 3.0.0
	 */
	public function webfont_loader() {

		// Go through our fields and populate $this->fonts.
		$this->webfonts->loop_fields();

		$this->googlefonts->fonts = apply_filters( 'kirki/enqueue_google_fonts', $this->googlefonts->fonts );

		// Goes through $this->fonts and adds or removes things as needed.
		$this->googlefonts->process_fonts();

		$fonts_to_load = array();
		foreach ( $this->googlefonts->fonts as $font => $weights ) {
			$fonts_to_load[] = esc_attr( $font ) . ':' . esc_attr( join( ',', $weights ) );
		}
		wp_enqueue_script( 'webfont-loader', 'https://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js' );
		wp_add_inline_script(
			'webfont-loader',
			'WebFont.load({google:{families:[\'' . esc_attr( join( '\', \'', $fonts_to_load ) ) . '\']}});',
			'after'
		);
	}
}
