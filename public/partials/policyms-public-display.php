<?php
/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://github.com/alexandrosraikos/policyms/
 * @since      1.0.0
 *
 * @package    PolicyMS
 * @subpackage PolicyMS/public/partials
 */

/**
 *
 * Prints an auto-disappearing error or notice box, with auto logout discovery for JS.
 * The close button is handled @see policyms-public.js
 *
 * @param string $message The message to be shown.
 * @param string $type The type of message, a 'notice' or an 'error'.
 * @param int    $http_status The code of the underlying HTTP status, if any.
 *
 * @since 1.0.0
 * @author Alexandros Raikos <alexandros@araikos.gr>
 */
function notice_html( string $message, string $type = 'error', int $http_status = null ) {
	$logout_attribute = ( ( 403 === ( $http_status ?? 0 ) ) ? 'logout' : '' );
	$message          = esc_html( $message );
	$type             = esc_attr( $type );

	return <<<HTML
        <div class="policyms-{$type}" {$logout_attribute}>
            <span>{$message}</span>
        </div>
    HTML;
}


/**
 * Get the plugin-specific menu items.
 *
 * @param bool   $authenticated Whether the user is authenticated.
 * @param string $authentication_url The authentication page URL.
 * @param string $registration_url The registration page URL.
 * @param string $account_url The account page URL.
 * @return string The menu item list HTML.
 *
 * @since 2.0.0
 * @author Alexandros Raikos <alexandros@araikos.gr>
 */
function menu_items_html(
	bool $authenticated,
	string $authentication_url,
	string $registration_url,
	string $account_url
	): string {

	$wrapper = function ( $element ) {
		$random_id = wp_rand( 1000, 10000 );
		return <<<HTML
			<li 
				id="menu-item-{$random_id}" 
				class="menu-item menu-item-type-post_type menu-item-object-page menu-item-{$random_id}">
				{$element}
			</li>
		HTML;
	};

	$links = '';
	if ( $authenticated ) {
		$links .= $wrapper( '<a href="' . $account_url . '">My Account</a>' );
		$links .= $wrapper( '<a class="policyms-logout">Log out</a>' );
	} else {
		$links .= $wrapper( '<a href="' . $authentication_url . '">Log In</a>' );
		$links .= $wrapper( '<a href="' . $registration_url . '">Register</a>' );
	}
	$links .= $wrapper(
		<<<HTML
			<div class="policyms menu-search">
				<button class="tactile" data-action="description-search">
					<span class="fas fa-search"></span>
				</button>
			</div>
		HTML
	);
	return $links;
}

/**
 * Print the locked content notification.
 *
 * @param   array $login_page The login page defined in the WordPress Settings.
 *
 * @since   1.0.0
 * @author  Alexandros Raikos <araikos@unipi.gr>
 * @author  Eleftheria Kouremenou <elkour@unipi.gr>
 */
function show_lock( $login_page ) {
	$lock_img_src = get_site_url( '', '/wp-content/plugins/policyms/public/assets/img/lock.svg' );
	$message      = sprintf( __( 'You need to be <a href="%s">logged in</a> in order to view this content.', 'policyms' ), $login_page );

	return <<<HTML
        <div class="policyms-locked-content">
            <img src="{$lock_img_src}"/>
            <p>{$message}</p>
        </div>
     HTML;
}

/**
 *
 * Prints a hidden modal with controls and a close button.
 * The visibility is handled @see policyms-public.js.
 *
 * @param callable $inner_html The modal content (return null if managed by jQuery).
 * @param bool     $controls Whether the modal has next/previous controls.
 *
 * @since 1.0.0
 * @author Alexandros Raikos <alexandros@araikos.gr>
 */
function show_modal( $inner_html, $controls = false ) {
	$navigation_buttons = array(
		'previous' => '',
		'next'     => '',
	);
	if ( $controls ) {
		$navigation_buttons['previous'] = <<<HTML
            <button class="previous tactile" disabled>
                <span class="fas fa-chevron-left"></span>
            </button>
        HTML;
		$navigation_buttons['next']     = <<<HTML
            <button class="next tactile" disabled>
                <span class="fas fa-chevron-right"></span>
            </button>
        HTML;
	}

	$content = $inner_html();
	return <<<HTML
        <div id="policyms-modal" class="hidden">
            <button class="close tactile">
                <span class="fas fa-times"></span>
            </button>
            {$navigation_buttons['previous']}
            <div class="container">
                {$content}
            </div>
            {$navigation_buttons['next']}
        </div>
    HTML;
}


/**
 *
 * Formats a datetime string to show time passed since.
 *
 * @param string $datetime The string depicting the date time information.
 * @param bool   $full Display the full elapsed time since the specified date.
 *
 * @since 1.0.0
 */
function time_elapsed_string( $datetime, $full = false ) {
	$now  = new DateTime();
	$ago  = new DateTime( $datetime );
	$diff = $now->diff( $ago );

	$diff->w  = floor( $diff->d / 7 );
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
	foreach ( $string as $k => &$v ) {
		if ( $diff->$k ) {
			$v = $diff->$k . ' ' . $v . ( $diff->$k > 1 ? 's' : '' );
		} else {
			unset( $string[ $k ] );
		}
	}

	if ( ! $full ) {
		$string = array_slice( $string, 0, 1 );
	}
	return $string ? implode( ', ', $string ) . ' ago' : 'just now';
}

/**
 * Display a list of assets with filtering, sorting and custom pagination.
 *
 * @param bool     $visitor Whether the requester is a visitor.
 * @param string   $content_type The class name of the presented content type.
 * @param array    $content The asset structure to be displayed, even if empty.
 * @param callable $inner_html The callback that prints the list item HTML.
 * @param int      $total_pages The available number of pages.
 * @param array    $total_categories The available categories for the content type in `[slug => label]` format.
 * @param int      $active_page The currently selected page, if any.
 * @param string   $active_category  The selected category, if any.
 * @param string   $sorting  The selected item sorting, if any.
 * @param int      $sizing  The selected size of the page, if any.
 * @param string   $creation_url The URL of the creation page of a given type.
 * @throws PolicyMSInvalidDataException On non-default page sorting, sizing selections or category mismatching.
 *
 * @since   1.0.0
 * @author  Alexandros Raikos <araikos@unipi.gr>
 */
function content_list_html(
	bool $visitor,
	string $content_type,
	array $content,
	callable $inner_html,
	int $total_pages,
	array $total_categories = null,
	int $active_page = 1,
	string $active_category = null,
	string $sorting = null,
	int $sizing = null,
	?string $creation_url = null,
): string {

	/**
	 * Preparing the list header.
	 * --------
	 */

	// Prepare selectors.
	$list_sorting_html = sorting_selector_html( $content_type, $sorting );
	$list_sizing_html  = sizing_selector_html( $sizing );

	// Add creation button.
	if ( ! empty( $creation_url ) && ! $visitor ) {
		$create_button_label = __( 'Create new', 'policyms' );
		$create_button_html  = <<<HTML
            <a 
                id="policyms-upload" 
                href="{$creation_url}" 
                title="Create new">
                    <span class="fas fa-plus"></span> {$create_button_label}
                </a>
        HTML;
	} else {
		$create_button_html = '';
	}

	// Create the content list header.
	$content_list_header = <<<HTML
        <header>
            {$list_sorting_html}
            {$list_sizing_html}
            {$create_button_html}
        </header>
    HTML;

	/**
	 * Preparing the list category filters.
	 * --------
	 */

	$content_list_filter_categories = '';
	if ( ! empty( $total_categories ) ) {
		if ( ! in_array( ( $active_category ?? $total_categories[0] ), $total_categories, true ) ) {
			throw new PolicyMSInvalidDataException( sprintf( 'The category %s does not exist.', $active_category ) );
		}
		foreach ( $total_categories as $category_slug => $category_label ) {
			$is_checked                      = ( ( $active_category ?? '' ) === $category_slug ) ? 'checked' : '';
			$content_list_filter_categories .= <<<HTML
            <label>
                <input type="checkbox" name="category" value="{$category_slug}" {$is_checked}>
                {$category_label}
            </label>
            HTML;
		}
		$content_list_filter_header_label = __( 'Filter', 'policyms' );
		$content_list_filters             = <<<HTML
        <form action="policyms-filter-content-list">
            <span>{$content_list_filter_header_label}:</span>
            {$content_list_filter_categories}
        </form>
        HTML;
	}

	/**
	 * Preparing the list itself.
	 * --------
	 */

	$content_list_items = '';
	if ( empty( $content ) ) {
		return notice_html( 'Nothing to display yet.', 'notice' );
	}
	foreach ( $content as $item ) {
		$content_list_items .= $inner_html( $content_type, $item );
	}

	/**
	 * Preparing pagination indicators.
	 * --------
	 */

	$content_list_pagination_buttons = '';
	for ( $page = 1; $page <= $total_pages; $page++ ) {
		$selected                         = ( $page === $active_page ) ? 'selected' : '';
		$content_list_pagination_buttons .= <<<HTML
            <button content-page="{$page}" class="{$selected}">{$page}</button>
        HTML;
	}
	$content_list_pagination = <<<HTML
        <nav>
            {$content_list_pagination_buttons}
        </nav>
    HTML;

	// Return the content list HTML.
	return <<<HTML
    <div class="policyms-content-list" content-type="{$content_type}">
        {$content_list_header}
        {$content_list_filters}
        <ul>
            {$content_list_items}
        </ul>
        {$content_list_pagination}
    </div>
    HTML;
}

/**
 * Get the HTML for content pagination.
 *
 * Handling via JS depends on the context.
 *
 * @param int $total_pages The total number of pages.
 * @param int $active_page The active page.
 * @return string The pagination HTML container.
 * @throws PolicyMSInvalidDataException When page doesn't exist.
 *
 * @since 2.0.0
 * @author Alexandros Raikos <alexandros@araikos.gr>
 */
function show_pagination_html( int $total_pages, $active_page = 1 ):string {
	if ( $active_page > $total_pages || $active_page < 1 ) {
		throw new PolicyMSInvalidDataException( 'The page number was invalid.' );
	}

	$content_list_pagination_buttons = '';
	for ( $page = 1; $page <= $total_pages; $page++ ) {
		$selected                         = ( $page === $active_page ) ? 'selected' : '';
		$content_list_pagination_buttons .= <<<HTML
            <button 
                content-page="{$page}" 
                class="{$selected}">
                {$page}
            </button>
        HTML;
	}

	return <<<HTML
        <nav>
            {$content_list_pagination_buttons}
        </nav>
    HTML;
}
