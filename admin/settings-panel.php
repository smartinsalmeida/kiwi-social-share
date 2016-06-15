<?php

/**
 * @TODO: - sanitizare a tuturor functiilor de randare
 * @TODO: - rezolva cu ID-ul duplicat al field-urilor
 * 		o idee ar fi ca cheia array-ului, sa fie doar ID-ul field-ului; practic
 * 	'cheie' => array() => array() (fara cheie explicit)
 * @TODO: - re-organizare fisier si comentarii
 * @TODO: - re-organizare CSS si comentarii
 * @TODO: - creat front-end - logic + CSS
 * @TODO: - iconita de afisat in meniu
 * @TODO: - uninstall.php ( pt. dezinstalarea plugin-ului si stergerea datelor din DB atunci cand este dezinstalat )
 * @TODO: - vezi cum s-ar putea implementa mai bine faza cu default value
 * @TODO: - actions before/after & right/left side form
 * @TODO: - functie care sa parcurga std-ul si sa adauge valorile alea default in DB
 * 	https://github.com/leemason/NHP-Theme-Options-Framework/blob/master/options/options.php#L187
 * @TODO: - FB share button needs to add <og:> tags to each post head
 * @TODO: - CSS layout for front-end buttons (skin1-4)
 */

/**
 * The dashboard-specific functionality of the plugin.
 *
 *
 * @package    Uber_Recaptcha
 * @subpackage Uber_Recaptcha/admin/settings
 * @author     Cristian Raiber <hi@cristian.raiber.me>
 */
class kiwi_options_panel
{
	private static $instance;
	public $settings_field = 'kiwi_settings';
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
		
		// hook our meta tag generator function to the header
		add_action( 'wp_head', array( $this, 'og_tags') );

		// add the menu page
		add_action( 'admin_menu', array($this, 'register_menu_page'));

		// add hook for admin notices on save
		add_action( 'admin_notices', array($this, 'show_admin_notice'));

		// load scripts
		add_action( 'admin_enqueue_scripts', array($this, 'back_end_scripts'));
		add_action( 'admin_enqueue_scripts', array($this, 'back_end_styles'));

		// render on the front
		add_action( 'the_content', array( $this, 'render_share_bar' ) );
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
			'dashicons-shield-alt'  // icon
		);
	}

	public function back_end_scripts($hook)
	{

		if ($hook !== $this->page_hook_suffix) {
			return;
		}

		wp_enqueue_script('jquery-ui-sortable');

		wp_register_script('settings-panel-js', KIWI__PLUGINS_URL . 'admin/js/settings-panel.js', array('jquery', 'jquery-ui-sortable'), '1.0', true);
		wp_enqueue_script('settings-panel-js');
	}

	public function back_end_styles($hook)
	{

		if ($hook !== $this->page_hook_suffix)
			return;

		wp_enqueue_style('kiwi-wpadmin-style', KIWI__PLUGINS_URL . 'admin/css/wp-styles.css', false, '1.0.0');
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

			'kiwi_enable_digg' => array(
				'title' => __(' Digg', 'kiwi-social-share'),
				'type' => 'sortable-toggle',
				'std' => 0,  // disabled
				'id' => 'kiwi_enable_digg'
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
			'misc-settings-heading' => array(
				'title' => __('Misc Settings', 'kiwi-social-share'),
				'sub-title' => __('twitter username & share label', 'kiwi-social-share'),
				'type' => 'heading',
				'nice-title' => 1,
			),
			'misc-settings-share-label' => array(
				'title' => __('Share label', 'kiwi-social-share'),
				'placeholder' => __('Share on: ', 'kiwi-social-share'),
				'std' => __('Share on:', 'kiwi-social-share'),
				'type' => 'text',
				'id' => 'misc-settings-share-label'
			),
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
						'desc' => __('Lorem Ipsum', 'kiwi-social-share'),
						'img' => KIWI__PLUGINS_URL . 'admin/images/kiwi-share-square-style.jpg',
					),
					'kiwi-shift-style' => array(
						'title' => __('Shift Style', 'kiwi-social-share'),
						'desc' => __('Lorem Ipsum', 'kiwi-social-share'),
						'img' => KIWI__PLUGINS_URL . 'admin/images/kiwi-share-shift-style.jpg',
					),
					'kiwi-leaf-style' => array(
						'title' => __('Leaf Style', 'kiwi-social-share'),
						'desc' => __('Lorem Ipsum', 'kiwi-social-share'),
						'img' => KIWI__PLUGINS_URL . 'admin/images/kiwi-share-leaf-style.jpg',
					),
					'kiwi-pills-style' => array(
						'title' => __('Pill Style', 'kiwi-social-share'),
						'desc' => __('Lorem Ipsum', 'kiwi-social-share'),
						'img' => KIWI__PLUGINS_URL . 'admin/images/kiwi-share-pill-style.jpg',
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
				$order = 'kiwi_enable_linkedin,kiwi_enable_pinterest,kiwi_enable_twitter,kiwi_enable_facebook,kiwi_enable_email,kiwi_enable_google_plus,kiwi_enable_digg';
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
			<!-- <div class='kiwi-badge'><?php _e('Version: 1.0.0', 'kiwi-social-share'); ?></div> -->

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
	 * @param $page
	 */
	function do_settings_sections($page)
	{
		global $wp_settings_sections, $wp_settings_fields;

		if (!isset($wp_settings_sections[$page]))
			return;

		foreach ((array)$wp_settings_sections[$page] as $section) {
			if ($section['title'])
				echo "<h3>{$section['title']}</h3>\n";

			if ($section['callback'])
				call_user_func($section['callback'], $section);

			if (!isset($wp_settings_fields) || !isset($wp_settings_fields[$page]) || !isset($wp_settings_fields[$page][$section['id']]))
				continue;

			//MY WRAPPING NEEDS TO START HERE
			//echo '<table class="form-table">';
			$this->do_settings_fields($page, $section['id']);
			//echo '</table>';
			//AND END HERE!
		}
	}

	/**
	 * @param $page
	 * @param $section
	 */
	function do_settings_fields($page, $section)
	{
		global $wp_settings_fields;

		if (!isset($wp_settings_fields[$page][$section]))
			return;

		foreach ((array)$wp_settings_fields[$page][$section] as $field) {
			$class = '';

			if (!empty($field['args']['class'])) {
				$class = ' class="' . esc_attr($field['args']['class']) . '"';
			}


			//echo "<tr{$class}>";

			if (!empty($field['args']['label_for'])) {
				//echo '<th scope="row"><label for="' . esc_attr( $field['args']['label_for'] ) . '">' . $field['title'] . '</label></th>';
				echo '<label for="' . esc_attr($field['args']['label_for']) . '">' . $field['title'] . '</label>';
			} /* else if( $field['args']['type'] !== 'heading' ) { // added an extra check to make sure field type is not also a heading; because using 'title' argument causes it to be output twice
				//echo '<th scope="row">' . $field['title'] . '</th>';
				echo '<div class="row">' . $field['title'] . '</div>';
			} */

			//echo '<td>';
			call_user_func($field['callback'], $field['args']);
			//echo '</td>';
			//echo '</tr>';
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
	 * Function that is responsible for checking if an option has a value in it or not.
	 *
	 * Returns false if it doesn't
	 *
	 * @param $option_id
	 *
	 * @since   1.0.0
	 */
	public function get_option_value($option_id)
	{

		$options = get_option($this->settings_field);

		if (!empty($options[$option_id])) {
			return $options[$option_id];
		} else {
			return;
		}
	}

	/**
	 * Function that is responsible for generating text fields
	 *
	 * @param $args
	 * @return string
	 *
	 * @since   1.0.0
	 */
	public function render_text_field($args)
	{

		// check to see if there's a value saved to the db
		// otherwise apply default value specified
		if (!empty($this->get_option_value($args['id']))) {
			$checkAgainst = $this->get_option_value($args['id']);
		} else {
			$checkAgainst = $args['std'];
		}

		$output = '<fieldset class="field-type-text">';
		$output .= '<label title="' . $args['title'] . '">';
		$output .= '<div class="kiwi-form-label">' . esc_attr($args['title']) . '</div>';
		$output .= '<input class="widefat" placeholder="' . esc_attr($args['placeholder']) . '" type="text" id="' . $args['id'] . '" name="' . $this->settings_field . '[' . $args['id'] . ']' . '" value="' . sanitize_text_field($checkAgainst) . '">';
		$output .= '</label>';
		$output .= '</fieldset>';

		return $output;
	}

	/**
	 * Function that is responsible for generating radio fields
	 *
	 * @param $args
	 * @return string
	 *
	 * @since   1.0.0
	 */
	public function render_radio_field($args)
	{

		$output = '<fieldset>';

		foreach ($args['options'] as $name => $value) {

			$output .= '<label title="' . $args['title'] . '">';
			$output .= '<input type="radio" name="' . $this->settings_field . '[' . $args['id'] . ']' . '" value="' . esc_attr($name) . '"' . checked($this->get_option_value($args['id']), $name, false) . '>';
			$output .= '<span>' . $value . '</span>';
			$output .= '</label><br />';
		}

		$output .= '</fieldset>';

		return $output;

	}


	/**
	 * Function that is responsible for generating radio IMG type fields
	 *
	 * @param $args
	 * @return string
	 *
	 * @since   1.0.0
	 */
	public function render_radio_img_field($args)
	{

		// check to see if there's a value saved to the db
		// otherwise apply default value specified
		if (!empty($this->get_option_value($args['id']))) {
			$checkAgainst = $this->get_option_value($args['id']);
		} else {
			$checkAgainst = $args['std'];
		}

		$output = '<fieldset class="field-type-img-radio">';

		foreach ($args['options'] as $array_key => $array_value) {

			$output .= '<div class="kiwi-field-wrapper radio-img">';
			$output .= '<img data-click-to="' . esc_attr($array_key) . '" class="kiwi-field-helper background-image" src="' . $array_value['img'] . '" />';

			$output .= '<input class="kiwi-hidden-input" type="radio" id="' . esc_attr($array_key) . '" name="' . $this->settings_field . '[' . $args['id'] . ']' . '" value="' . esc_attr($array_key) . '"' . checked($checkAgainst, $array_key, false) . '>';
			$output .= '<label for="' . esc_attr($array_key) . '" class="kiwi-form-label radio-img">' . esc_html($array_value['title']) . '</label>';

			$output .= '<div class="kiwi-field-description img-radio">' . esc_html($array_value['desc']) . '</div>';
			$output .= '</diV>';

		}

		$output .= '</fieldset>';

		return $output;

	}

	/**
	 * Function that is responsible for generating checkbox fields
	 *
	 * @param $args
	 * @return string
	 *
	 * @since   1.0.0
	 */
	public function render_checkbox_field($args)
	{


		// Set default value to $args['std']
		if (!isset($args['std'])) {
			$args['std'] = 0;
		}

		$output = '<fieldset class="kiwi-field-wrapper checkbox">';

		// render
		$output .= '<label title="' . esc_attr($args['title']) . '" for="' . esc_attr($args['id']) . '">';

		// check we actually have a title
		if (!empty($args['title'])) {
			$output .= '<div class="kiwi-form-label">' . $args['title'] . '</div>';
		}

		$output .= '<input id="' . esc_attr($args['id']) . '" type="checkbox" name="' . esc_attr($this->settings_field) . '[' . esc_attr($args['id']) . ']' . '" value="1"' . checked($this->get_option_value($args['id']), 1, false) . '>';

		// check we actually have a description
		if (!empty($args['desc'])) {
			$output .= '<span class="kiwi-field-description">' . esc_html($args['desc']) . '</span>';
		}
		$output .= '</label>';

		$output .= '</fieldset>';

		return $output;

	}

	/**
	 * Function that is responsible for generating draggable & sortable checkbox combo fields
	 *
	 * @param $args
	 * @return string
	 *
	 * @since   1.0.0
	 *
	 * @TODO: aici ar trebui schimbata logica. sortable/draggable ar trebui sa vina sub forma unui singur array
	 *
	 *    array(
	 *        'id' => 'bla',
	 *        'type' => 'sortable-draggable',
	 *        'std'   => 'default_order_they_should_be_in',
	 *        'options' => array(
	 * array(
	 *                    'id' => 'kiwi_enable_facebook',
	 *
	 *                )
	 *            )
	 *   )
	 *
	 *
	 */
	public function render_sortable_draggable_field($args)
	{


		// Set default value to $args['std']
		if (!isset($args['std'])) {
			$args['std'] = 0;
		}

		// check to see if there's a value saved to the db
		// otherwise apply default value specified
		if (!empty($this->get_option_value($args['id']))) {
			$checkAgainst = $this->get_option_value($args['id']);
		} else {
			$checkAgainst = $args['std'];
		}

		// start render
		$output = '<fieldset class="kiwi-field-wrapper checkbox-sortable">';

		$output .= '<div class="kiwi-sortable-helper"></div>';
		$output .= '<div class="kiwi-form-label">' . $args['title'] . '</div>';

		$output .= '<label class="switch" for="' . esc_attr($args['id']) . '">';
		$output .= '<input id="' . esc_attr($args['id']) . '" class="switch-input" type="checkbox" name="' . esc_attr($this->settings_field) . '[' . esc_attr($args['id']) . ']' . '" value="1"' . checked($checkAgainst, 1, false) . '>';
		$output .= '<span class="switch-label" data-on="' . __('On', 'kiwi-social-share') . '" data-off="' . __('Off', 'kiwi-social-share') . '"></span>';
		$output .= '<span class="switch-handle"></span>';
		$output .= '</label>';

		$output .= '</fieldset>';

		return $output;
	}


	/**
	 * Function that is responsible for generating checkbox fields
	 *
	 * @param $args
	 * @return string
	 *
	 * @since   1.0.0
	 */
	public function render_toggle_field($args)
	{


		// Set default value to $args['std']
		if (!isset($args['std'])) {
			$args['std'] = 0;
		}

		// check to see if there's a value saved to the db
		// otherwise apply default value specified
		if (!empty($this->get_option_value($args['id']))) {
			$checkAgainst = $this->get_option_value($args['id']);
		} else {
			$checkAgainst = $args['std'];
		}

		// start render
		$output = '<fieldset class="kiwi-field-wrapper checkbox-toggle">';


		$output .= '<span class="kiwi-form-label">' . $args['title'] . '</span>';

		$output .= '<label class="switch" for="' . esc_attr($args['id']) . '">';
		$output .= '<input id="' . esc_attr($args['id']) . '" class="switch-input" type="checkbox" name="' . esc_attr($this->settings_field) . '[' . esc_attr($args['id']) . ']' . '" value="1"' . checked($checkAgainst, 1, false) . '>';
		$output .= '<span class="switch-label" data-on="' . __('On', 'kiwi-social-share') . '" data-off="' . __('Off', 'kiwi-social-share') . '"></span>';
		$output .= '<span class="switch-handle"></span>';
		$output .= '</label>';

		$output .= '</fieldset>';

		return $output;
	}


	public function render_select_field($args)
	{

		// check to see if there's a value saved to the db
		// otherwise apply default value specified
		if (!empty($this->get_option_value($args['id']))) {
			$checkAgainst = $this->get_option_value($args['id']);
		} else {
			$checkAgainst = $args['std'];
		}

		$output = '<fieldset class="field-type-select">';
		$output .= '<select name="' . $this->settings_field . '[' . $args['id'] . ']' . '">';

		foreach ($args['options'] as $key => $value) {
			$output .= '<option value="' . esc_attr($key) . '" ' . selected($checkAgainst, $key, false) . '>' . esc_attr($value) . '</option>';
		}

		$output .= '</select>';
		$output .= '</fieldset>';

		return $output;
	}

	public function render_heading_field($args)
	{

		if ($args['nice-title'] == 1) {
			// split the title into 2 pieces
			list($first_word, $second_word) = explode(' ', $args['title'], 2);
		}

		$output = '<h2 class="form-heading">';
		if ($args['nice-title'] == 1) {
			$output .= '<span class="first-word">' . esc_html($first_word) . '</span>' . ' ' . esc_html($second_word);
		} else {
			$output .= esc_html($args['title']);
		}

		if (!empty($args['sub-title'])) {
			$output .= '<span class="sub-title">';
			$output .= esc_html($args['sub-title']);
			$output .= '</span>';
		}

		$output .= '</h2>';

		return $output;

	}

	public function render_hidden_field($args)
	{
		$output = '<input type="hidden" class="widefat" name="' . esc_attr($this->settings_field) . '[' . esc_attr($args['id']) . ']' . '" id="' . esc_attr($args['id']) . '" value="' . $this->get_option_value($args['id']) . '">';
		return $output;
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

	public function render_share_bar( $content ) {

		if( is_singular() ) {

			// get enabled social networks
			$kiwi_display_fb = $this->get_option_value('kiwi_enable_facebook');
			$kiwi_display_twitter = $this->get_option_value('kiwi_enable_twitter');
			$kiwi_display_google_plus = $this->get_option_value('kiwi_enable_google_plus');
			$kiwi_display_linkedin = $this->get_option_value('kiwi_enable_linkedin');
			$kiwi_display_pinterest = $this->get_option_value('kiwi_enable_pinterest');
			$kiwi_display_digg = $this->get_option_value('kiwi_enable_digg');
			$kiwi_display_mail = $this->get_option_value('kiwi_enable_mail');

			// share label
			$kiwi_share_label = $this->get_option_value('misc-settings-share-label');

			// where to display
			$kiwi_display_on_posts = $this->get_option_value('kiwi-enable-on-posts');
			$kiwi_display_on_pages = $this->get_option_value('kiwi-enable-on-pages');

			// share position
			$kiwi_share_position = $this->get_option_value('kiwi-enable-share-position');

			if( ($kiwi_display_on_posts == 1 && is_single() ) || ( $kiwi_display_on_pages == 1 && is_page() ) ) {

				// start building the output
				$output = '<div class="clear"></div>';
				$output .= '<div class="kiwi-share-bar-wrapper">';

				// Facebook
				if ($kiwi_display_fb == 1) {
					$output .= PHP_EOL . '<div class="kiwi-fb-icon">';
					$output .= '<a rel="nofollow" target="_blank" href="//www.facebook.com/sharer/sharer.php?u=' . get_the_permalink() . '" />';
					//$output .= '<img src="' . KIWI__PLUGINS_URL . 'images/kiwi-fb-icon.png" alt="' . __('Facebook Share Icon', 'kiwi-social-share') . '" />';
					$output .= '</a>';
					$output .= '</div><!--/.kiwi-fb-icon-->';
				}

				// Twitter
				if ($kiwi_display_twitter == 1) {
					$output .= PHP_EOL . '<div class="kiwi-twitter-icon">';
					$output .= '<a rel="nofollow" target="_blank" href="//twitter.com/intent/tweet?text=' . rawurlencode(get_the_title()) . '&url=' . rawurlencode(get_the_permalink()) . '" />';
					//$output .= '<img src="' . KIWI__PLUGINS_URL . 'images/kiwi-twitter-icon.png" alt="' . __('Twitter Share Icon', 'kiwi-social-share') . '" />';
					$output .= '</a>';
					$output .= '</div><!--/.kiwi-twitter-icon-->';
				}

				// Google+
				if ($kiwi_display_google_plus == 1) {
					$output .= PHP_EOL . '<div class="kiwi-google-plus-icon">';
					$output .= '<a rel="nofollow" target="_blank" href="//plus.google.com/share?url=' . rawurlencode(get_the_permalink()) . '">';
					//$output .= '<img src="' . KIWI__PLUGINS_URL . 'images/kiwi-google-plus-icon.png"  alt="' . __('Google Plus Share Icon', 'kiwi-social-share') . '" />';
					$output .= '</a>';
					$output .= '</div><!--/.kiwi-google-plus-icon-->';
				}


				// LinkedIN
				if ($kiwi_display_linkedin == 1) {

					$output .= PHP_EOL . '<div class="kiwi-linkedin-icon">';
					$output .= '<a rel="nofollow" target="_blank" href="//linkedin.com/shareArticle?mini=true&url=' . rawurlencode(get_the_permalink()) . '&title=' . rawurlencode(get_the_title()) . '">';
					//$output .= '<img src="' . KIWI__PLUGINS_URL . 'images/kiwi-linkedin-icon.png"  alt="' . __('LinkedIN Share Icon', 'kiwi-social-share') . '" />';
					$output .= '</a>';
					$output .= '</div><!--/.kiwi-linkedin-icon-->';

				}

				// Pinterest
				if ($kiwi_display_pinterest == 1) {

					$output .= PHP_EOL . '<div class="kiwi-pinterest-icon">';
					$output .= '<a rel="nofollow" target="_blank" href="//pinterest.com/pin/create/button/?url=' . '&amp;description=' . rawurlencode($this->get_excerpt_by_id(absint(get_the_ID()))) . '&media=' . wp_get_attachment_url(get_post_thumbnail_id(get_the_ID())) . '">';
					//$output .= '<img src="' . KIWI__PLUGINS_URL . 'images/kiwi-pinterest-icon.png"  alt="' . __('Pinterest Share Icon', 'kiwi-social-share') . '" />';
					$output .= '</a>';
					$output .= '</div><!--/.kiwi-pinterest-icon-->';
				}

				// Digg
				if ($kiwi_display_digg == 1) {

					$output .= PHP_EOL . '<div class="kiwi-digg-icon">';
					$output .= '<a rel="nofollow" target="_blank" href="//digg.com/submit?phase=2&url="' . rawurlencode(the_permalink()) . '">';
					//$output .= '<img src="' . KIWI__PLUGINS_URL . 'images/kiwi-digg-icon.png"  alt="' . __('Digg Share Icon', 'kiwi-social-share') . '" />';
					$output .= '</a>';
					$output .= '</div><!--/.kiwi-digg-icon-->';
				}

				// Mail
				if ($kiwi_display_mail == 1) {

					$output .= PHP_EOL . '<div class="kiwi-email-icon">';
					$output .= '<a rel="nofollow" target="_blank" href="mailto:?subject=' . get_the_title() . '&body=' . get_the_permalink() . '">';
					//$output .= '<img src="' . KIWI__PLUGINS_URL . 'images/kiwi-email-icon.png"  alt="' . __('Email Share Icon', 'kiwi-social-share') . '" />';
					$output .= '</a>';
					$output .= '</div><!--/.kiwi-email-icon-->';
				}

				$output .= '</div><!--/.kiwi-share-bar-wrapper-->';

				if ($kiwi_share_position == 'before-posts') {
					$output .= $content;

					return $output;

				} elseif ($kiwi_share_position == 'after-posts') {
					$content .= $output;

					return $content;
				} else {

					$share_bar = $output;

					$output .= $content;
					$output .= $share_bar;

					return $output;
				}

				return;
			} // check if is_single || is_page
		} // is_singular()
	}

	/**
	 *
	 * We'll need to for the title of the posts
	 * Converts smart quotes
	 *
	 * @param $content
	 * @return mixed
	 */
	public function convert_smart_quotes( $content )
	{

		$content = str_replace('"', '\'', $content);
		$content = str_replace('&#8220;', '\'', $content);
		$content = str_replace('&#8221;', '\'', $content);
		$content = str_replace('&#8216;', '\'', $content);
		$content = str_replace('&#8217;', '\'', $content);


		return $content;
	}

	/**
	 *
	 * Filtering function for the_excerpt
	 * Strips shortcodes, extra tags and limits word count to 100
	 *
	 * @param $post_id
	 * @return mixed|string
	 */
	public function get_excerpt_by_id( $post_id )
	{

		// Check if the post has an excerpt
		if ( has_excerpt() ) {

			$the_post = get_post( $post_id ); //Gets post ID
			$the_excerpt = $the_post->post_excerpt; // Gets post excerpt

			// If not, let's create an excerpt
		} else {
			$the_post = get_post( $post_id ); //Gets post ID
			$the_excerpt = $the_post->post_content; //Gets post_content to be used as a basis for the excerpt
		}

		$excerpt_length = 100; //Sets excerpt length by word count
		$the_excerpt = strip_tags( strip_shortcodes( $the_excerpt ) ); //Strips tags, shortcodes and images

		$the_excerpt = str_replace(']]>', ']]&gt;', $the_excerpt);


		$excerpt_length = apply_filters('excerpt_length', 100);
		$excerpt_more = apply_filters('excerpt_more', ' ' . '[...]');

		$words = preg_split("/[\n\r\t ]+/", $the_excerpt, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY );

		if ( count($words) > $excerpt_length) {
			array_pop($words);
			$the_excerpt = implode(' ', $words);
		}

		$the_excerpt = preg_replace("/\r|\n/", "", $the_excerpt); // filter out carriage returns and new lines

		return $the_excerpt;
	}

	/**
	 *
	 * Function to get author ID by post ID
	 *
	 * @param int $post_id
	 * @return string
	 */
	public function get_author_id_by_post_id( $post_id = 0)
	{
		$post = get_post( $post_id );
		return $post->post_author;
	}


	public function og_tags() {


		// Create/check default values
		$info['postID'] = absint( get_the_ID() );
		$info['title'] = esc_html( get_the_title() );
		$info['imageURL'] = get_post_thumbnail_id( $info['postID'] );
		$info['description'] = esc_html( $this->get_excerpt_by_id( $info['postID'] ) );
		$info['user_twitter_handle'] = esc_attr( $this->get_option_value( 'misc-settings-twitter-handle' ) );
		$info['header_output'] = '';


		// We only modify the Open Graph tags on single blog post pages
		if ( is_singular() ) {

			if ( (isset($info['title']) && $info['title']) || (isset($info['description']) && $info['description']) || (isset($info['imageURL']) && $info['imageURL'])  ) {

				/*****************************************************************
				 *                                                                *
				 *     YOAST SEO: It rocks, so let's coordinate with it             *
				 *                                                                *
				 ******************************************************************/

				// Check if Yoast Exists so we can coordinate output with their plugin accordingly
				if ( !defined( 'WPSEO_VERSION' ) ) {


				// Add all our Open Graph Tags to the Return Header Output
				$info['header_output'] .= PHP_EOL . '<!-- Meta OG tags by Kiwi Social Sharing Plugin -->';
					$info['header_output'] .= PHP_EOL . '<meta property="og:type" content="article" /> ';


				/*****************************************************************
				 *                                                                *
				 *     OPEN GRAPH TITLE                                             *
				 *                                                                *
				 ******************************************************************/

				// Open Graph Title: Create an open graph title meta tag
				if ( $info['title'] ) {

					// If nothing else is defined, let's use the post title
					$info['header_output'] .= PHP_EOL . '<meta property="og:title" content="' . $this->convert_smart_quotes( htmlspecialchars_decode( get_the_title() ) ) . '" />';

				}

				/*****************************************************************
				 *                                                                *
				 *     OPEN GRAPH DESCRIPTION                                     *
				 *                                                                *
				 ******************************************************************/

				 if ( $info['description'] ) {

					 // If nothing else is defined, let's use the post excerpt
					 $info['header_output'] .= PHP_EOL . '<meta property="og:description" content="' . $this->convert_smart_quotes( htmlspecialchars_decode( $this->get_excerpt_by_id( $info['postID'] ) ) ) . '" />';

				 }

				/*****************************************************************
				 *                                                                *
				 *     OPEN GRAPH IMAGE                                             *
				 *                                                                *
				 ******************************************************************/

				if( has_post_thumbnail( $info['postID'] ) ) {

					// If nothing else is defined, let's use the post Thumbnail as long as we have the URL cached
					$og_image = get_post_thumbnail_id( $info['postID'] );

					if ( $og_image ) {
						$info['header_output'] .= PHP_EOL . '<meta property="og:image" content="' . $og_image . '" />';
					}

				} else {
					$og_image = KIWI__PLUGINS_URL . 'admin/images/placeholder-image.png';
					$info['header_output'] .= PHP_EOL . '<meta property="og:image" content="' . esc_url( $og_image ) . '" >';
				}

				/*****************************************************************
				 *                                                                *
				 *     OPEN GRAPH URL											  *
				 * 	   OPEN GRAPH Site Name                                 	  *
				 *     OPEN GRAPH Article Published Time                          *
				 *     OPEN GRAPH Article Modified Time                           *
				 *     OPEN GRAPH Article Updated Time                            *
				 *                                                                *
				 ******************************************************************/

				$info['header_output'] .= PHP_EOL . '<meta property="og:url" content="' . get_permalink() . '" />';
				$info['header_output'] .= PHP_EOL . '<meta property="og:site_name" content="' . get_bloginfo('name') . '" />';
				$info['header_output'] .= PHP_EOL . '<meta property="article:published_time" content="' . get_post_time('c') . '" />';
				$info['header_output'] .= PHP_EOL . '<meta property="article:modified_time" content="' . get_post_modified_time('c') . '" />';
				$info['header_output'] .= PHP_EOL . '<meta property="og:updated_time" content="' . get_post_modified_time('c') . '" />';


				// append the closing comment :)
				$info['header_output'] .= PHP_EOL . '<!--/end meta tags by Kiwi Social Sharing Plugin -->';

				// Return the variable containing our information for the meta tags
				echo $info['header_output'];

			}
		}
	}
}

} // class end