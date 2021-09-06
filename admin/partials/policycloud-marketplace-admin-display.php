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

    echo '<input type="text" name="policycloud_marketplace_plugin_settings[marketplace_host]" value="' . (($options != false) ? $options['marketplace_host'] : '') . '" /><p>The Marketplace server address endpoint.</p>';
}

function policycloud_marketplace_plugin_jwt_key()
{

    $options = get_option('policycloud_marketplace_plugin_settings');
    echo '<input type="text" name="policycloud_marketplace_plugin_settings[jwt_key]" value="' . (($options != false) ? $options['jwt_key'] : '') . '" /><p>The Marketplace server token decoding key.</p>';
}
