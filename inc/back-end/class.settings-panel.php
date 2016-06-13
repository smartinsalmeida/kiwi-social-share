<?php



/**
 * The dashboard-specific functionality of the plugin.
 *
 *
 * @package    Uber_Recaptcha
 * @subpackage Uber_Recaptcha/admin/settings
 * @author     Cristian Raiber <hi@cristian.raiber.me>
 */
class Kiwi_Settings_Page extends Kiwi_Plugin_Utilities
{
	private static $instance;
	public $page_hook_suffix = '';

	/**
	 *
	 * The singleton method
	 *
	 * @return static Class instance
	 */
	public static function singleton()
	{
		if (!isset(self::$instance)) {
			self::$instance = new static;
		}

		return self::$instance;
	}

	/**
	 * Private clone method to prevent cloning of the instance of the
	 * *Singleton* instance.
	 *
	 * @return void
	 */
	private function __clone()
	{
	}

	/**
	 * Private unserialize method to prevent unserializing of the *Singleton*
	 * instance.
	 *
	 * @return void
	 */
	private function __wakeup()
	{
	}


	/**
	 * Initialize the class and set its properties.
	 *
	 */
	public function __construct()
	{
		
		// add the menu page
		add_action( 'admin_menu', array($this, 'register_menu_page'));

		// add hook for admin notices on save
		add_action( 'admin_notices', array($this, 'show_admin_notice'));

		// load scripts
		add_action( 'admin_enqueue_scripts', array($this, 'back_end_scripts'));
		add_action( 'admin_enqueue_scripts', array($this, 'back_end_styles'));

	}

	/**
	 * Function that handles the creation of a new menu page for the plugin
	 *
	 * @since   1.0.0
	 */
	public function register_menu_page()
	{

		$this->page_hook_suffix = add_menu_page(
			'Kiwi Social',          // page title
			'Kiwi Social',          // menu title
			'manage_options',       // capability
			'kiwi-social-share',    // menu-slug
			array(                  // callback function to render the options
				$this,
				'render_settings',
			),
			KIWI__PLUGINS_URL . 'assets/back-end/images/kiwi-menu-icon.png'  // icon
		);
	}

	public function back_end_scripts($hook)
	{

		if ($hook !== $this->page_hook_suffix) {
			return;
		}

		wp_enqueue_script('jquery-ui-sortable');
		
		wp_register_script('settings-panel-js', KIWI__PLUGINS_URL . 'assets/back-end/js/settings-panel.js', array('jquery', 'jquery-ui-sortable'), KIWI__PLUGIN_VERSION, true);
		wp_enqueue_script('settings-panel-js');
	}

	public function back_end_styles($hook)
	{

		if ($hook !== $this->page_hook_suffix)
			return;

		wp_register_style('kiwi-wpadmin-style', KIWI__PLUGINS_URL . 'assets/back-end/css/back-end-styles.css', false, KIWI__PLUGIN_VERSION );
		wp_enqueue_style('kiwi-wpadmin-style');
	}


	/**
	 * Function that holds the required back-end field mark-up.
	 *
	 * @since   1.0.0
	 *
	 * @return  array   $settings   Holds all the mark-up required for the field rendering engine to render the fields
	 */
	public function settings_fields()
	{

		$settings['left-side'] = array(

			'general_settings_heading' => array(
				'title' => __('General Settings', 'kiwi-social-share'),
				'sub-title' => __('drag & drop to reorder', 'kiwi-social-share'),
				'type' => 'heading',
				'nice-title' => 1,
				'id' => 'general_settings_heading'
			),

			'general_settings_order' => array(
				'title' => __('Order', 'kiwi-social-share'),
				'type' => 'hidden',
				'id' => 'general_settings_order'
			),

			'kiwi_enable_facebook' => array(
				'title' => __(' Facebook', 'kiwi-social-share'),
				'type' => 'sortable-toggle',
				'std' => 1,
				'id' => 'kiwi_enable_facebook'
			),

			'kiwi_enable_twitter' => array(
				'title' => __(' Twitter', 'kiwi-social-share'),
				'type' => 'sortable-toggle',
				'std' => 1,
				'id' => 'kiwi_enable_twitter'
			),

			'kiwi_enable_pinterest' => array(
				'title' => __(' Pinterest', 'kiwi-social-share'),
				'type' => 'sortable-toggle',
				'std' => 1,
				'id' => 'kiwi_enable_pinterest'
			),

			'kiwi_enable_linkedin' => array(
				'title' => __(' LinkedIN', 'kiwi-social-share'),
				'type' => 'sortable-toggle',
				'std' => 1,
				'id' => 'kiwi_enable_linkedin'
			),

			'kiwi_enable_reddit' => array(
				'title' => __('Reddit', 'kiwi-social-share'),
				'type' => 'sortable-toggle',
				'std' => 0,  // disabled
				'id' => 'kiwi_enable_reddit'
			),

			'kiwi_enable_google_plus' => array(
				'title' => __(' Google Plus', 'kiwi-social-share'),
				'type' => 'sortable-toggle',
				'std' => 0,  // disabled
				'id' => 'kiwi_enable_google_plus'
			),

			'kiwi_enable_email' => array(
				'title' => __(' Email', 'kiwi-social-share'),
				'type' => 'sortable-toggle',
				'std' => 0, // disabled
				'id' => 'kiwi_enable_email'
			),
		);

		$settings['right-side'] = array(


			'display-settings-heading' => array(
				'title' => __('Display Settings', 'kiwi-social-share'),
				'sub-title' => __('Post & Page', 'kiwi-social-share'),
				'type' => 'heading',
				'nice-title' => 1,
			),
			'kiwi-enable-on-posts' => array(
				'title' => __(' Posts', 'kiwi-social-share'),
				'type' => 'toggle',
				'std' => 1, // disabled
				'id' => 'kiwi-enable-on-posts'
			),
			'kiwi-enable-on-pages' => array(
				'title' => __(' Pages', 'kiwi-social-share'),
				'type' => 'toggle',
				'std' => 0, // disabled
				'id' => 'kiwi-enable-on-pages'
			),

			'kiwi-display-where' => array(
				'title' => __('Display Position', 'kiwi-social-share'),
				'sub-title' => __('select button position', 'kiwi-social-share'),
				'type' => 'heading',
				'nice-title' => 1,
			),

			'kiwi-enable-share-position' => array(
				'title' => __('Enable on:', 'kiwi-social-share'),
				'type' => 'select',
				'id' => 'kiwi-enable-share-position',
				'std' => 'before-posts',
				'options' => array(
					'after-posts' => __('After content', 'kiwi-social-share'),
					'before-posts' => __('Before content', 'kiwi-social-share'),
					'before_and_after_posts' => __('Before & after content', 'kiwi-social-share')
				)
			)
		);

		$settings['full-width'] = array(
			'kiwi-design-settings-heading' => array(
				'id' => 'kiwi-design-settings-heading',
				'title' => __('Design Settings', 'kiwi-social-share'),
				'sub-title' => __('choose prefered layout', 'kiwi-social-share'),
				'type' => 'heading',
				'nice-title' => 1,
			),
			'kiwi-design-choose-layout' => array(
				'id' => 'kiwi-design-choose-layout',
				'type' => 'radio-img',
				'std' => 'kiwi-default-style',
				'options' => array(
					'kiwi-default-style' => array(
						'title' => __('Default Style', 'kiwi-social-share'),
						'desc' => __('Square, classic style.', 'kiwi-social-share'),
						'img' => KIWI__PLUGINS_URL . 'assets/back-end/images/kiwi-share-square-style.jpg',
					),
					'kiwi-shift-style' => array(
						'title' => __('Shift Style', 'kiwi-social-share'),
						'desc' => __('Simple. Futuristic style.', 'kiwi-social-share'),
						'img' => KIWI__PLUGINS_URL . 'assets/back-end/images/kiwi-share-shift-style.jpg',
					),
					'kiwi-leaf-style' => array(
						'title' => __('Leaf Style', 'kiwi-social-share'),
						'desc' => __('Like a leaf in the wind.', 'kiwi-social-share'),
						'img' => KIWI__PLUGINS_URL . 'assets/back-end/images/kiwi-share-leaf-style.jpg',
					),
					'kiwi-pills-style' => array(
						'title' => __('Pill Style', 'kiwi-social-share'),
						'desc' => __('Them curves though.', 'kiwi-social-share'),
						'img' => KIWI__PLUGINS_URL . 'assets/back-end/images/kiwi-share-pill-style.jpg',
					),
				)
			)
		);


		return $settings;

	}

	/**
	 * Function that registers the settings sections
	 */
	public function render_settings()
	{

		// Check that the user is allowed to update options
		if (!current_user_can('manage_options')) {
			wp_die(__('You do not have sufficient permissions to access this page.', 'kiwi-social-share'));
		}

		// save options
		$this->save_settings();

		// left side settings
		register_setting('kiwi_settings_left_side', $this->settings_field, array($this, 'validate_fields'));
		add_settings_section('kiwi_settings_section_left', NULL, NULL, 'kiwi_settings_section_left_call');

		// right side settings
		register_setting('kiwi_settings_right_side', $this->settings_field, array($this, 'validate_fields'));
		add_settings_section('kiwi_settings_section_right', NULL, NULL, 'kiwi_settings_section_right_call');

		// full width settings
		register_setting('kiwi_settings_fullwidth', $this->settings_field, array($this, 'validate_fields'));
		add_settings_section('kiwi_settings_section_fullwidth', NULL, NULL, 'kiwi_settings_section_fullwidth_call');

		// get settings fields
		$settings_fields = $this->settings_fields();

		if (!empty($settings_fields['left-side'])) {

			if (!empty($this->get_option_value($settings_fields['left-side']['general_settings_order']['id']))) {
				$order = $this->get_option_value($settings_fields['left-side']['general_settings_order']['id']);
			} else {
				$order = 'kiwi_enable_linkedin,kiwi_enable_pinterest,kiwi_enable_twitter,kiwi_enable_facebook,kiwi_enable_email,kiwi_enable_google_plus,kiwi_enable_reddit';
			}

			$order_preload = explode(',', str_replace(' ', '', $order));

			// Hidden
			add_settings_field(
				$settings_fields['left-side']['general_settings_order']['id'],              // unique ID for the field
				$settings_fields['left-side']['general_settings_order']['title'],           // title of the field
				array($this, 'kiwi_render_field'),                                        // function callback
				'kiwi_settings_section_left_call',                                            // page name, should be the same as the last argument used in add_settings_section
				'kiwi_settings_section_left',                                                // same as first argument passed to add_settings_section
				$settings_fields['left-side']['general_settings_order']                     // $args, passed as array; defined in kiwi_settings_field()
			);

			// Heading
			add_settings_field(
				$settings_fields['left-side']['general_settings_heading']['id'],            // unique ID for the field
				$settings_fields['left-side']['general_settings_heading']['title'],         // title of the field
				array($this, 'kiwi_render_field'),                                        // function callback
				'kiwi_settings_section_left_call',                                            // page name, should be the same as the last argument used in add_settings_section
				'kiwi_settings_section_left',                                                // same as first argument passed to add_settings_section
				$settings_fields['left-side']['general_settings_heading']                   // $args, passed as array; defined in kiwi_settings_field()
			);


			foreach ($order_preload as $order_key => $order_value) {
				add_settings_field(
					$settings_fields['left-side'][$order_value]['id'],                      // unique ID for the field
					$settings_fields['left-side'][$order_value]['title'],                    // title of the field
					array($this, 'kiwi_render_field'),                                    // function callback
					'kiwi_settings_section_left_call',                                        // page name, should be the same as the last argument used in add_settings_section
					'kiwi_settings_section_left',                                            // same as first argument passed to add_settings_section
					$settings_fields['left-side'][$order_value]                            // $args, passed as array; defined in kiwi_settings_field()
				);
			}
		}


		if (!empty($settings_fields['right-side'])) {
			foreach ($settings_fields['right-side'] as $settings_id => $settings_arguments) {
				add_settings_field(
					$settings_id,                        // unique ID for the field
					$settings_arguments['title'],        // title of the field
					array($this, 'kiwi_render_field'),   // function callback
					'kiwi_settings_section_right_call',        // page name, should be the same as the last argument used in add_settings_section
					'kiwi_settings_section_right',             // same as first argument passed to add_settings_section
					$settings_arguments                  // $args, passed as array; defined in kiwi_settings_field()
				);
			}
		}


		if (!empty($settings_fields['full-width'])) {
			foreach ($settings_fields['full-width'] as $settings_id => $settings_arguments) {
				add_settings_field(
					$settings_id,                        // unique ID for the field
					NULL,       // title of the field
					array($this, 'kiwi_render_field'),   // function callback
					'kiwi_settings_section_fullwidth_call',        // page name, should be the same as the last argument used in add_settings_section
					'kiwi_settings_section_fullwidth',             // same as first argument passed to add_settings_section
					$settings_arguments                  // $args, passed as array; defined in kiwi_settings_field()
				);
			}
		}


		?>


		<!-- Create a header in the default WordPress 'wrap' container -->
		<div class='wrap kiwi-wrap'>

			<h1><?php _e('Kiwi Social Sharing', 'kiwi-social-share'); ?></h1>
			<p class="kiwi-about-text"><?php echo __('Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet. ', 'kiwi-social-share'); ?>.</p>
			<div class='kiwi-badge'><span><?php echo __('Version: ', 'kiwi-social-share') .KIWI__PLUGIN_VERSION; ?></span></div>

			<?php settings_errors(); ?>

			<div class='kiwi-form-wrapper'>
				<form method="post" action="">

					<div class="kiwi-form-wrapper-right">
						<?php settings_fields('kiwi_settings_right_side'); ?>
						<?php $this->do_settings_sections('kiwi_settings_section_right_call'); ?>
					</div>


					<div class="kiwi-form-wrapper-left">
						<?php settings_fields('kiwi_settings_left_side');               //settings group, defined as first argument in register_setting
						?>
						<?php $this->do_settings_sections('kiwi_settings_section_left_call');   //same as last argument used in add_settings_section
						?>
					</div>
					<div class="clear"></div>
					<div class="kiwi-form-wrapper-full">
						<?php settings_fields('kiwi_settings_fullwidth');               //settings group, defined as first argument in register_setting
						?>
						<?php $this->do_settings_sections('kiwi_settings_section_fullwidth_call');   //same as last argument used in add_settings_section
						?>
					</div>

					<div class="clear"></div>

					<?php submit_button(); ?>
					<?php wp_nonce_field('kiwi_settings_nonce'); ?>
				</form>
			</div>

		</div><!-- /.wrap -->


	<?php }

	/**
	 * Function that calls the rendering engine
	 *
	 * @param   array $args Each array entry defiend in the kiwi_settings_fields() is passed as a parameter to this function
	 *
	 * @since   1.0.0
	 */

	public function kiwi_render_field($args)
	{

		switch ($args['type']) {

			case 'text':
				echo $this->render_text_field($args);
				break;
			case 'radio':
				echo $this->render_radio_field($args);
				break;
			case 'checkbox':
				echo $this->render_checkbox_field($args);
				break;
			case 'sortable-toggle':
				echo $this->render_sortable_draggable_field($args);
				break;
			case 'toggle':
				echo $this->render_toggle_field($args);
				break;
			case 'select':
				echo $this->render_select_field($args);
				break;
			case 'heading':
				echo $this->render_heading_field($args);
				break;
			case 'hidden':
				echo $this->render_hidden_field($args);
				break;
			case 'radio-img':
				echo $this->render_radio_img_field($args);
				break;
		}
	}

	


	/**
	 * Function that saves the plugin options to the database
	 *
	 * @since   1.0.0
	 */
	public function save_settings()
	{

		if (isset($_POST[$this->settings_field]) && check_admin_referer('kiwi_settings_nonce', '_wpnonce')) {

			update_option($this->settings_field, $_POST[$this->settings_field]);

		}
	}

	public function validate_fields($input)
	{

		// Create our array for storing the validated options
		$output = array();

		// Loop through each of the incoming options
		foreach ($input as $key => $value) {

			// Check to see if the current option has a value. If so, process it.
			if (isset($input[$key])) {

				// Strip all HTML and PHP tags and properly handle quoted strings
				$output[$key] = strip_tags(stripslashes($input[$key]));

			} // end if

		} // end foreach

		// Return the array processing any additional functions filtered by this action
		return apply_filters('kiwi_plugin_validate_settings', $output, $input);
	}


	/**
	 * Helper function for creating admin messages
	 *
	 * @param (string) $message The message to echo
	 * @param (string) $msgclass The message class
	 * @return the message
	 *
	 * $msgclass possible values: info / error
	 *
	 */
	public function show_admin_notice()
	{

		if (isset($_POST[$this->settings_field]) && check_admin_referer('kiwi_settings_nonce', '_wpnonce')) {
			echo '<div class="notice updated is-dismissible">' . __('Settings updated successfully!', 'kiwi-social-share') . '</div>';
		}
	}
} // class end
$kiwi_settings_panel = Kiwi_Settings_Page::singleton();