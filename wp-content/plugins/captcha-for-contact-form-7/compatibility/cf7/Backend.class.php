<?php

namespace forge12\contactform7\CF7Captcha {
    if(!defined('ABSPATH')){
        exit;
    }
	/**
	 * Class Backend
	 * Responsible to handle the admin settings for the captcha
	 *
	 * @package forge12\contactform7\CF7Captcha
	 */
	class Backend {
		/**
		 * Admin constructor.
		 */
		public function __construct() {
			add_action( 'admin_init', array( $this, 'addHooks' ) );
		}

		/**
		 * Add the hooks responsible to handle wordpress functions
		 */
		public function addHooks() {
			$this->addCaptchaToCF7();
		}

		/**
		 *
		 */
		public function captchaCallback( $contact_form, $args = '' ) {
			$args = wp_parse_args( $args, array() );
			$type = 'f12_captcha';

			$description = __( "Generate a captcha to stop spam.", 'f12-cf7-captcha' );

			?>
			<div class="control-box">
				<fieldset>
					<legend><?php echo esc_html( $description ); ?></legend>

					<table class="form-table">
						<tbody>
						<tr>
							<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-name' ); ?>"><?php echo esc_html( __( 'Name', 'f12-cf7-captcha' ) ); ?></label></th>
							<td><input type="text" name="name" class="tg-name oneline" id="<?php echo esc_attr( $args['content'] . '-name' ); ?>" /></td>
						</tr>
						<tr>
							<th scope="row">
								<?php _e('Captcha Method', 'f12-cf7-captcha');?>
							</th>
							<td>
								<label><input type="radio" name="captcha" value="image" class="option" /> <?php echo esc_html( __( 'Image Captcha', 'f12-cf7-captcha' ) ); ?></label>
								<label><input type="radio" name="captcha" value="math" class="option" /> <?php echo esc_html( __( 'Arithmetical Captcha.', 'f12-cf7-captcha' ) ); ?></label>
								<label><input type="radio" name="captcha" value="honey" class="option" /> <?php echo esc_html( __( 'Honeypot Captcha.', 'f12-cf7-captcha' ) ); ?></label>
							</td>
						</tr>

						<tr>
							<th scope="row"><label
										for="<?php echo esc_attr( $args['content'] . '-id' ); ?>"><?php echo esc_html( __( 'Id attribute', 'f12-cf7-captcha' ) ); ?></label></th>
							<td><input type="text" name="id" class="idvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-id' ); ?>"/></td>
						</tr>

						<tr>
							<th scope="row"><label
										for="<?php echo esc_attr( $args['content'] . '-class' ); ?>"><?php echo esc_html( __( 'Class attribute', 'f12-cf7-captcha' ) ); ?></label>
							</th>
							<td><input type="text" name="class" class="classvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-class' ); ?>"/></td>
						</tr>
						</tbody>
					</table>
				</fieldset>
			</div>

			<div class="insert-box">
				<input type="text" name="<?php echo esc_attr($type); ?>" class="tag code" readonly="readonly" onfocus="this.select()"/>

				<div class="submitbox">
					<input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr( __( 'Insert Tag', 'f12-cf7-captcha' ) ); ?>"/>
				</div>

				<br class="clear"/>
			</div>
			<?php
		}

		/**
		 * Add the captcha button to the contact form 7 generator
		 */
		private function addCaptchaToCF7() {
			if ( class_exists( '\WPCF7_TagGenerator' ) ) {
				$tag_generator = \WPCF7_TagGenerator::get_instance();
				$tag_generator->add( 'f12_captcha', __( 'Captcha', 'f12-cf7-captcha' ),
					array( $this, 'captchaCallback' ) );
			}
		}
	}
}