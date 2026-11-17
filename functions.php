<?php

function kanoa_fse_styles() {
    wp_enqueue_style(
        'kanoa-fse-style',
        get_stylesheet_uri(),
        array(),
        wp_get_theme()->get( 'Version' )
    );
}

add_action( 'wp_enqueue_scripts', 'kanoa_fse_styles' );

if ( ! function_exists( 'kanoa_fse_setup' ) ) {
    function kanoa_fse_setup() {
        add_theme_support( 'wp-block-styles' );
        add_editor_style( 'style.css' );
    }
}

add_action( 'after_setup_theme', 'kanoa_fse_setup' );

remove_theme_support( 'core-block-patterns' );

add_filter( 'should_load_remote_block_patterns', '__return_false' );


function kanoa_register_block_styles() {
    register_block_style( 'core/heading', array(
        'name'  	=> 'kanoa-text-wrap-balance',
        'label' 	=> esc_html__( 'Balance Wrap', 'kanoa' ),
    ) );

    register_block_style( 'core/paragraph', array(
        'name'  	=> 'kanoa-text-wrap-balance',
        'label' 	=> esc_html__( 'Balance Wrap', 'kanoa' ),
    ) );

	register_block_style( 'core/columns', array(
        'name'  	=> 'kanoa-reverse',
        'label' 	=> esc_html__( 'Mobile Reverse', 'kanoa' ),
    ) );
}

add_action( 'init', 'kanoa_register_block_styles' );

function kanoa_register_pattern_categories() {
    if ( function_exists( 'kanoa_register_pattern_categories' ) ) {
        register_block_pattern_category(
            'kanoa-patterns',
            array(
                'label' => __( 'Kanoa patterns', 'kanoa' ),
                'description' => __( 'Kanoa theme patterns', 'kanoa' ),
            )
        );
    }
}

add_action( 'init', 'kanoa_register_pattern_categories' );

function kanoa_setup_notice() {
$notice_option_name = 'kanoa_setup_notice_dismissed';
$is_dismissed = get_option( $notice_option_name );

if ( ! $is_dismissed && current_user_can( 'edit_theme_options' ) ) {
$image_url = get_template_directory_uri() . '/assets/images/me.png';
$notice_text = '<img src="' . esc_url( $image_url ) . '" style="max-width: 100%;" alt="" />
<div class="kanoa-notice-text" style="margin-top: 15px;">
    <h3 style="margin-top: 0px; font-size: 18px;">' . __('Hi, I\'m Roman Fink, the creator of the Kanoa theme!','kanoa')
        . ' 😊</h3>
    <p style="margin: 5px 0 0;"><b>' . __('Just close this notice to hide it. Thanks for using the Kanoa theme!','kanoa')
            . ' 🚀</b></p>
</div>';
echo '<div id="kanoa-notice" class="notice notice-info is-dismissible">' . wp_kses_post( $notice_text ) . '</div>';
}

}

add_action( 'admin_notices', 'kanoa_setup_notice' );


function kanoa_notice_script() {
if ( get_option( 'kanoa_setup_notice_dismissed' ) || ! current_user_can( 'edit_theme_options' ) ) {
	return;
}
if ( ! wp_script_is( 'jquery', 'done' ) ) {
wp_enqueue_script( 'jquery' );
}
wp_enqueue_script( 'kanoa-notice-script', get_template_directory_uri() . '/assets/js/admin-notice.js', array( 'jquery' ), wp_get_theme( get_template() )->get( 'Version' ), true );
wp_localize_script( 'kanoa-notice-script', 'kanoaNotice', array( 'nonce' => wp_create_nonce( 'kanoa_dismiss_notice' ) ) );
}

add_action( 'admin_enqueue_scripts', 'kanoa_notice_script' );


function kanoa_enqueue_custom_admin_styles() {
$notice_option_name = 'kanoa_setup_notice_dismissed';
$is_dismissed = get_option( $notice_option_name );
if ( ! $is_dismissed ) {
wp_enqueue_style( 'kanoa-admin-notice', get_template_directory_uri() . '/assets/css/admin-notice.css' );
}
}

add_action( 'admin_enqueue_scripts', 'kanoa_enqueue_custom_admin_styles' );


function kanoa_dismiss_notice() {
check_ajax_referer( 'kanoa_dismiss_notice', 'nonce' );
if ( ! current_user_can( 'edit_theme_options' ) ) {
	wp_die();
}
update_option( 'kanoa_setup_notice_dismissed', true );
wp_die();
}

add_action( 'wp_ajax_kanoa_dismiss_notice', 'kanoa_dismiss_notice' );