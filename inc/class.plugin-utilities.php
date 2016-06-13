<?php

class Kiwi_Plugin_Utilities {

    public $settings_field = 'kiwi_settings';


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

        foreach ($args['options'] as $array_key => $array_value) {

            $output .= '<label for="'. esc_attr ( $array_key ).'">';
                $output .= '<input id="'.esc_attr( $array_key ).'" type="radio" name="' . $this->settings_field . '[' . $args['id'] . ']' . '" value="' . esc_attr($array_key) . '"' . checked($this->get_option_value($args['id']), $array_key, false) . '>';
            $output .= '<span>' . $array_value . '</span>';
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

        $output .= '<ul class="kiwi-field-wrapper kiwi-radio-img">';

        foreach ($args['options'] as $array_key => $array_value) {

            if( checked($checkAgainst, $array_key, false) ) {
                $field_class = ' kiwi-active-field';
            } else {
                $field_class = '';
            }

            $output .= '<li class="kiwi-radio-img-field '.esc_attr( $array_key ). esc_attr( $field_class ) .'">';
                $output .= '<div class="kiwi-field-helper-radio-img" data-click-to="' . esc_attr( $array_key ) . '" >';
                    $output .= '<img class="kiwi-background-image" src="' . $array_value['img'] . '" />';

                    $output .= '<input class="kiwi-hidden-input" type="radio" id="' . esc_attr($array_key) . '" name="' . $this->settings_field . '[' . $args['id'] . ']' . '" value="' . esc_attr($array_key) . '"' . checked($checkAgainst, $array_key, false) . '>';
                    $output .= '<label for="' . esc_attr($array_key) . '" class="kiwi-form-label radio-img">' . esc_html($array_value['title']) . '</label>';

                    $output .= '<div class="kiwi-field-description img-radio">' . esc_html($array_value['desc']) . '</div>';
                $output .= '</div>';
            $output .= '</li>';

        }

        $output .= '</ul>';
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
}

$kiwi_plugin_base = new Kiwi_Plugin_Utilities();