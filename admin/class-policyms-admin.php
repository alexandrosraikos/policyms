<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://dac.ds.unipi.gr/policycloud-eu/
 * @since      1.0.0
 *
 * @package    PolicyMS
 * @subpackage PolicyMS/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    PolicyMS
 * @subpackage PolicyMS/admin
 * @author     Alexandros Raikos <araikos@unipi.gr>
 */
class PolicyMS_Admin
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/policyms-admin.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/policyms-admin.js', array('jquery'), $this->version, false);
    }

    function policyms_validate_plugin_settings($input)
    {
        if (!function_exists('force_protocol_prefix')) {
            function force_protocol_prefix($url)
            {
                if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
                    $url = "https://" . $url;
                }
                return $url;
            }
        }

        $output['marketplace_host'] = esc_url(force_protocol_prefix($input['marketplace_host']));
        $output['api_access_token'] = sanitize_text_field($input['api_access_token']);
        $output['jwt_key'] = sanitize_text_field($input['jwt_key']);
        $output['encryption_key'] = sanitize_text_field($input['encryption_key']);
        $output['login_page'] = esc_url($input['login_page']);
        $output['registration_page'] = esc_url($input['registration_page']);
        $output['password_reset_page'] = esc_url($input['password_reset_page']);
        $output['tos_url'] = esc_url($input['tos_url']);
        $output['account_page'] = esc_url($input['account_page']);
        $output['selected_menu'] = sanitize_text_field($input['selected_menu']);
        $output['description_page'] = esc_url($input['description_page']);
        $output['upload_page'] = esc_url($input['upload_page']);
        $output['archive_page'] = esc_url($input['archive_page']);
        $output['egi_redirection_page'] = esc_url($input['egi_redirection_page']);
        $output['egi_redirection_client_id'] = esc_url($input['egi_redirection_client_id']);
        $output['egi_redirection_client_secret'] = esc_url($input['egi_redirection_client_secret']);
        $output['egi_redirection_code_challenge'] = esc_url($input['egi_redirection_code_challenge']);
        $output['egi_redirection_code_verifier'] = esc_url($input['egi_redirection_code_verifier']);
        return $output;
    }

    function register_settings()
    {

        register_setting(
            'policyms_plugin_settings',
            'policyms_plugin_settings',
            'policyms_validate_plugin_settings'
        );

        add_settings_section(
            'section_one',
            'Access Credentials',
            'policyms_plugin_section_one',
            'policyms_plugin'
        );

        add_settings_field(
            'marketplace_host',
            'Marketplace Host',
            'policyms_plugin_host',
            'policyms_plugin',
            'section_one'
        );

        add_settings_field(
            'api_access_token',
            'Marketplace API Access Token',
            'policyms_plugin_api_access_token',
            'policyms_plugin',
            'section_one'
        );

        add_settings_field(
            'jwt_key',
            'Marketplace Key',
            'policyms_plugin_jwt_key',
            'policyms_plugin',
            'section_one'
        );

        add_settings_field(
            'encryption_key',
            'Encryption Key',
            'policyms_plugin_encryption_key',
            'policyms_plugin',
            'section_one'
        );


        add_settings_field(
            'egi_redirection_page',
            'EGI Redirection page',
            'policyms_plugin_egi_redirection_page',
            'policyms_plugin',
            'section_one'
        );

        add_settings_field(
            'egi_client_id',
            'EGI Client ID',
            'policyms_plugin_egi_client_id',
            'policyms_plugin',
            'section_one'
        );

        add_settings_field(
            'egi_client_secret',
            'EGI Client Secret',
            'policyms_plugin_egi_client_secret',
            'policyms_plugin',
            'section_one'
        );

        add_settings_field(
            'egi_code_challenge',
            'EGI Code Challenge',
            'policyms_plugin_egi_code_challenge',
            'policyms_plugin',
            'section_one'
        );

        add_settings_field(
            'egi_code_verifier',
            'EGI Code Verifier',
            'policyms_plugin_egi_code_verifier',
            'policyms_plugin',
            'section_one'
        );

        add_settings_section(
            'section_two',
            'Menu Settings',
            'policyms_plugin_section_two',
            'policyms_plugin'
        );

        add_settings_field(
            'selected_menu',
            'Selected Menu',
            'policyms_plugin_menu_selector',
            'policyms_plugin',
            'section_two'
        );

        add_settings_field(
            'login_page',
            'Log In page',
            'policyms_plugin_login_page_selector',
            'policyms_plugin',
            'section_two'
        );

        add_settings_field(
            'registration_page',
            'Registration page',
            'policyms_plugin_registration_page_selector',
            'policyms_plugin',
            'section_two'
        );

        add_settings_field(
            'account_page',
            'Redirect to My Account',
            'policyms_plugin_account_page_selector',
            'policyms_plugin',
            'section_two'
        );

        add_settings_section(
            'section_three',
            'Content Settings',
            'policyms_plugin_section_three',
            'policyms_plugin'
        );

        add_settings_field(
            'password_reset_page',
            'Password Reset Page',
            'policyms_plugin_password_reset_page_selector',
            'policyms_plugin',
            'section_three'
        );

        add_settings_field(
            'tos_url',
            'Terms of Service URL',
            'policyms_plugin_tos_url',
            'policyms_plugin',
            'section_three'
        );

        add_settings_field(
            'description_page',
            'Description page',
            'policyms_plugin_description_page_selector',
            'policyms_plugin',
            'section_three'
        );

        add_settings_field(
            'archive_page',
            'Description archive page',
            'policyms_plugin_archive_page_selector',
            'policyms_plugin',
            'section_three'
        );

        add_settings_field(
            'upload_page',
            'Description upload page',
            'policyms_plugin_upload_page_selector',
            'policyms_plugin',
            'section_three'
        );
    }

    public function add_settings_page()
    {
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/policyms-admin-display.php';

        add_options_page(
            'PolicyMS Settings',
            'PolicyMS',
            'manage_options',
            'policyms-plugin',
            'render_settings_page'
        );
    }
}
