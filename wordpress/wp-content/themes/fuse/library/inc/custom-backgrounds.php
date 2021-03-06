<?php
// Add theme support for Custom Background

function wpgrade_custom_backgrounds_suport(){

    $background_args = array(
        'default-color'          => '#345',
        'default-image'          => '',
        'wp-head-callback'       => '_custom_background_cb',
        'admin-head-callback'    => '',
        'admin-preview-callback' => '',
    );
    add_theme_support( 'custom-background', $background_args );
}

// Hook into the 'after_setup_theme' action
add_action( 'after_setup_theme', 'wpgrade_custom_backgrounds_suport' );
?>