<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://dac.ds.unipi.gr/policycloud-eu/
 * @since      1.0.0
 *
 * @package    PolicyCloud_Marketplace
 * @subpackage PolicyCloud_Marketplace/admin/partials
 */

function render_settings_page()
{
?>
    <h2>PolicyCloud Marketplace Settings</h2>
    <p>This is the options page for the PolicyCloud Marketplace API.</p>
    <form action="options.php" method="post">
        <?php
        settings_fields('policycloud_marketplace_plugin_settings');
        do_settings_sections('policycloud_marketplace_plugin');
        ?>
        <br />
        <input type="submit" name="submit" class="button button-primary" value="<?php esc_attr_e('Save'); ?>" />
    </form>
<?php
}

function policycloud_marketplace_plugin_section_one()
{
    echo '<p>Insert your credentials for the Marketplace API.</p>';
}

function policycloud_marketplace_plugin_host()
{
    $options = get_option('policycloud_marketplace_plugin_settings');
?>
    <input type="text" name="policycloud_marketplace_plugin_settings[marketplace_host]" value="<?php echo (($options != false) ? $options['marketplace_host'] : '') ?>" />
    <p>The Marketplace server address endpoint.</p>
<?php
}

function policycloud_marketplace_plugin_jwt_key()
{
    $options = get_option('policycloud_marketplace_plugin_settings');
?>
    <input type="text" name="policycloud_marketplace_plugin_settings[jwt_key]" value="<?php echo (($options != false) ? $options['jwt_key'] : '') ?>" />
    <p>The Marketplace server token decoding key.</p>
<?php
}

function policycloud_marketplace_plugin_section_two()
{
    echo '<p>Select your preferred operating settings.</p>';
}

function policycloud_marketplace_plugin_login_page_selector()
{
    $options = get_option('policycloud_marketplace_plugin_settings');
    $pages = get_pages([
        'post_status' => 'publish'
    ]);
?>
    <select name="policycloud_marketplace_plugin_settings[login_page]">
        <?php
        foreach ($pages as $page) {
            echo '<option value="' . get_page_link($page->ID) . '" '.($options['login_page']==get_page_link($page->ID) ? 'selected' : '').'>' . $page->post_title . '</option>';
        }
        ?>
    </select>
    <p>Select the log in page where the "Log In" menu item should redirect.</p>
<?php
}

function policycloud_marketplace_plugin_menu_selector()
{
    $options = get_option('policycloud_marketplace_plugin_settings');
    $menus = get_registered_nav_menus();
?>
    <select name="policycloud_marketplace_plugin_settings[selected_menu]">
        <?php
        foreach ($menus as $location => $description) {
            echo '<option value="' . $location . '" '.($options['selected_menu']==$location ? 'selected' : '').'>' . $description. '</option>';
        }
        ?>
    </select>
    <p>Select the menu where the Log In and Log Out buttons to appear.</p>
<?php
}

function policycloud_marketplace_plugin_section_three()
{
    echo '<p>Select your preferred operating settings.</p>';
}

function policycloud_marketplace_plugin_description_page_selector()
{
    $options = get_option('policycloud_marketplace_plugin_settings');
    $pages = get_pages([
        'post_status' => 'publish'
    ]);
?>
    <select name="policycloud_marketplace_plugin_settings[description_page]">
        <?php
        foreach ($pages as $page) {
            echo '<option value="' . get_page_link($page->ID) . '" '.($options['description_page']==get_page_link($page->ID) ? 'selected' : '').'>' . $page->post_title . '</option>';
        }
        ?>
    </select>
    <p>Select the page where you've inserted the <em>[policycloud-marketplace-read-single]</em> shortcode.</p>
<?php
}