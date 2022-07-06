<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://dac.ds.unipi.gr/policycloud-eu/
 * @since      1.0.0
 *
 * @package    PolicyMS
 * @subpackage PolicyMS/public/partials
 */
?>

<?php


/**
 *
 * Prints an auto-disappearing error or notice box.
 * The close button is handled @see policyms-public.js
 *
 * @param string $message The message to be shown.
 * @param bool $dismissable Whether the alert is dismissable or not.
 * @param string $type The type of message, a 'notice' or an 'error'.
 *
 * @since 1.0.0
 */
function show_alert(string $message, string $type = 'error', int $http_status = null)
{
    echo  '<div class="policyms-' . $type . ' " '. ((($http_status ?? 0) == 403) ? 'logout' : '' ).'><span>' . $message . '</span></div>';
}

/**
 *
 * Prints a hidden modal with controls and a close button.
 * The visibility is handled @see policyms-public.js.
 *
 * @param callable $inner_html The modal content (return null if managed by jQuery).
 * @param bool $controls Whether the modal has next/previous controls.
 *
 * @since 1.0.0
 */
function show_modal($inner_html, $controls = false)
{
    ?>
    <div id="policyms-modal" class="hidden">
        <button class="close tactile"><span class="fas fa-times"></span></button>
        <div class="container">
            <?php
            if ($controls) {
                ?>
                <button class="previous tactile" disabled><span class="fas fa-chevron-left"></span></button>
                <?php
            }
            ?>
            <div class="content">
                <?php $inner_html() ?>
            </div>
            <?php
            if ($controls) {
                ?>
                <button class="next tactile"><span class="fas fa-chevron-right"></span></button>
                <?php
            }
            ?>
        </div>
    </div>
    <?php
}


/**
 *
 * Formats a datetime string to show time passed since.
 *
 * @param string $datetime The string depicting the date time information.
 * @param bool $full Display the full elapsed time since the specified date.
 *
 * @since 1.0.0
 */
function time_elapsed_string($datetime, $full = false)
{
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) {
        $string = array_slice($string, 0, 1);
    }
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}

/**
 * Print the locked content notification.
 *
 * @param   array $login_page The login page defined in the WordPress Settings.
 * @param   array $message The lowercase message indicating the desired action.
 *
 * @since   1.0.0
 * @author  Alexandros Raikos <araikos@unipi.gr>
 * @author  Eleftheria Kouremenou <elkour@unipi.gr>
 */
function show_lock($login_page, $message)
{
    echo '<div class="lock"><img src="' . get_site_url('', '/wp-content/plugins/policyms/public/assets/img/lock.svg') . '" /><p>Please <a href="' . $login_page . '">log in</a> to ' . $message . '.</p></div>';
}
