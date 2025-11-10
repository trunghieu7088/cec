<?php
function mytheme_customize_register($wp_customize) {
    $wp_customize->add_section('mytheme_colors', array(
        'title'    => __('Theme Colors', 'mytheme'),
        'priority' => 30,
    ));

    $colors = array(
        'primary_color' => array(
            'label'   => __('Primary Color', 'mytheme'),
            'default' => '#2c5f7c',
        ),
        'secondary_color' => array(
            'label'   => __('Secondary Color', 'mytheme'),
            'default' => '#4a90af',
        ),
        'accent_color' => array(
            'label'   => __('Accent Color', 'mytheme'),
            'default' => '#f8b739',
        ),
        'dark_bg' => array(
            'label'   => __('Dark Background', 'mytheme'),
            'default' => '#1a3a4d',
        ),
        'light_bg' => array(
            'label'   => __('Light Background', 'mytheme'),
            'default' => '#f8f9fa',
        ),
        'text_dark' => array(
            'label'   => __('Dark Text', 'mytheme'),
            'default' => '#333',
        ),
        'text_light' => array(
            'label'   => __('Light Text', 'mytheme'),
            'default' => '#666',
        ),
    );

    foreach ($colors as $key => $color) {
        $wp_customize->add_setting($key, array(
            'default'           => $color['default'],
            'sanitize_callback' => 'sanitize_hex_color',
        ));

        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, $key, array(
            'label'    => $color['label'],
            'section'  => 'mytheme_colors',
            'settings' => $key,
        )));
    }
}
add_action('customize_register', 'mytheme_customize_register');
