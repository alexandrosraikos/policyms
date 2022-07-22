<?php
/**
 * The HTML generating functions for user-related content.
 *
 * @link       https://github.com/alexandrosraikos/policyms/
 * @since      1.0.0
 *
 * @package    PolicyMS
 * @subpackage PolicyMS/public/partials
 */

/**
 * Print the account registration form.
 *
 * @param   bool                      $authenticated Whether the user is authenticated.
 * @param   string                    $nonce The account registration nonce.
 * @param   string                    $authentication_url The url that redirects to the log in page.
 * @param   string                    $account_page_url The url that redirects to the account page.
 * @param   string                    $tos_url The url that redirects to the terms of service page.
 * @param   PolicyMS_OAuth_Controller $oauth_controller The OAuth controller instance.
 *
 * @since   1.0.0
 * @author  Alexandros Raikos <alexandros@araikos.gr>
 */
function user_registration_html(
	bool $authenticated,
	string $nonce,
	string $authentication_url,
	string $account_page_url,
	string $tos_url,
	PolicyMS_OAuth_Controller $oauth_controller

) {
	if ( $authenticated ) {
		return notice_html( "You're already logged in.", 'notice' );
	} else {
		$sso_buttons = $oauth_controller->get_all_html( true );
		return <<<HTML
            <div class="policyms policyms-user-registration">
                    <h2>Welcome</h2>
                    <p>Create an account and start working with policy materials today.</p>
                    {$sso_buttons}
                    <div class="sso-divider">
                        <hr/>
                        <span>or</span>
                        <hr/>
                    </div>
                <form 
                    data-action="policyms-user-registration"
                    data-redirect="{$account_page_url}"
                    data-nonce="{$nonce}"
                    action="">
                    <label for="name">First name *</label>
                    <input required name="name" placeholder="Enter your first name" type="text" />
                    <label for="surname">Last name *</label>
                    <input required name="surname" placeholder="Enter your last name" type="text" />
                    <label for="email">E-mail address *</label>
                    <input type="email" name="email" placeholder="e.g. name@example.com" required />
                    <label for="password">Password *</label>
                    <input required name="password" placeholder="Must have at least 8 characters" type="password" />
                    <label for="password-confirm">Confirm password *</label>
                    <input required name="password-confirm" placeholder="Enter your password again" type="password" />
                    <div class="tos-agree">
                        <input type="checkbox" id="tos-agree" name="tos-agree" required />
                        <label for="tos-agreee">
                            I have read and I agree to the <a class="underline" href="{$tos_url}">Terms of Service</a>.
                        </label>
                    </div>
                    <div class="actions center">
                        <button type="submit" class="action ">Create account</button>
                    </div>
                </form>
                <hr />
                <p>Already have an account? Please <a class="underline" href="{$authentication_url}">sign in</a>.</p>
            </div>
        HTML;
	}
}

/**
 * Get the user authentication container HTML.
 *
 * @param string                    $nonce The user authentication nonce.
 * @param string                    $home_url The URL of the home page.
 * @param string                    $registration_url The URL of the registration page.
 * @param string                    $reset_password_page_url  The URL of the password reset page.
 * @param PolicyMS_OAuth_Controller $oauth_controller The OAuth controller instance.
 * @param bool                      $authenticated Whether the user is authenticated.
 * @return string The user authetnication container HTML.
 *
 * @since 1.0.0
 * @author Alexandros Raikos <alexandros@araikos.gr>
 */
function user_authentication_html(
	string $nonce,
	string $home_url,
	string $registration_url,
	string $reset_password_page_url,
	PolicyMS_OAuth_Controller $oauth_controller,
	bool $authenticated
	) {
	if ( $authenticated ) {
		return notice_html( "You're already logged in.", 'notice' );
	} else {
		$sso_buttons = $oauth_controller->get_all_html( false );
		return <<<HTML
            <div class="policyms policyms-user-authentication">
                <form 
                    data-action="policyms-user-authentication"
                    data-redirect="{$home_url}"
                    data-nonce="{$nonce}"
                    action="">
                    <h2>Welcome back</h2>
                    <p>Please enter your details or sign in using one of the services below.</p>
                    <label for="email">E-mail address *</label>
                    <input required name="email" placeholder="e.g. name@example.com" type="email" />
                    <label for="password">Password *</label>
                    <input required name="password" placeholder="*************" type="password" />
                    <div class="actions center">
                        <button type="submit" class="action">Sign in</button>
                    </div>
                </form>
                <div class="sso-divider">
                    <hr/>
                    <span>or</span>
                    <hr/>
                </div>
                {$sso_buttons}
                <p>Don't have an account yet? You can <a class="underline" href="{$registration_url}">register</a> for free now. If you have forgotten your credentials, you can <a class="underline" href="{$reset_password_page_url}">reset your password.</a></p>
            </div>
        HTML;
	}
}

/**
 * Get the password reset form HTML.
 *
 * @param   bool   $authenticated Whether the requeter is authenticated.
 * @param   string $nonce The password reset form's nonce.
 * @return string The password reset form HTML.
 *
 * @since 1.4.0
 * @author Alexandros Raikos <alexandros@araikos.gr>
 */
function user_password_reset_html( $authenticated, $nonce ) {
	if ( $authenticated ) {
		return show_lock( 'You are already logged in.', 'notice' );
	} else {
		return <<<HTML
            <div class="policyms policyms-password-reset">
                        <h2>Reset your password</h2>
                        <p>Insert your e-mail address below and we will contact you with instructions to reset your password.</p>
                <form 
                    data-action="policyms-user-password-reset"
                    data-nonce="{$nonce}"
                    >
                        <label for="email">E-mail address *</label>
                        <input 
                            name="email" 
                            placeholder="e.g. name@example.com" 
                            type="email" 
                            required />
                        <button type="submit" class="action">
                            Reset password
                        </button>
                </form>
            </div>
        HTML;
	}
}

/**
 * Get the user overview HTML container.
 *
 * @param array $information The information property of a given PolicyMS_User.
 * @param array $statistics The statistics property of a given PolicyMS_User.
 * @return string The user overview HTML container.
 *
 * @since 2.0.0
 * @author Alexandros Raikos <alexandros@araikos.gr>
 */
function user_overview_html( array $information, array $statistics ): string {
	$about = $information['about'] ?? '';
	$links = '';
	if ( ! empty( $information['social'][0] ) ) {
		$links = '<ul>';
		foreach ( $information['social'] as $link ) {
			$link_title = explode( ':', $link, 2 )[0];
			$link_url   = explode( ':', $link, 2 )[1];
			$links     .= <<<HTML
            <li>
                <a href="{$link_url}" class="action pill" target="blank">
                    {$link_title}
                </a>
            </li>
            HTML;
		}
		$links .= '</ul>';
	}

	$statistics_html = '';
	if ( ! empty( $statistics ) ) {
		$statistics_html .= <<<HTML
        <div class="section-title">
            <h3>Statistics</h3>
            <hr/>
        </div>
        <table class="statistics">
            <tr>
                <td>
                    <div class="large-figure"><span class="fas fa-list"></span> {$statistics['total_descriptions']}</div>
                    <div class="assets-caption">Total descriptions</div>
                </td>
                <td>
                    <div class="large-figure"><span class="fas fa-check"></span> {$statistics['approved_descriptions']}</div>
                    <div class="assets-caption">Approved descriptions</div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="large-figure"><span class="fas fa-file"></span> {$statistics['assets_uploaded']}</div>
                    <div>Assets uploaded</div>
                </td>
                <td>
                    <div class="large-figure"><span class="fas fa-share"></span> {$statistics['total_links_provided']}</div>
                    <div>Total links provided</div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="large-figure"><span class="fas fa-eye"></span> {$statistics['total_views']}</div>
                    <div class="assets-caption">Total views</div>
                </td>
                <td>
                    <div class="large-figure"><span class="fas fa-download"></span> {$statistics['total_downloads']}</div>
                    <div class="assets-caption">Total downloads</div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="large-figure"><span class="fas fa-comment"></span> {$statistics['total_reviews']}</div>
                    <div class="assets-caption">Total received reviews</div>
                </td>
                <td>
                    <div class="large-figure"><span class="fas fa-star"></span> {$statistics['average_rating']}</div>
                    <div class="assets-caption">Average rating</div>
                </td>
            </tr>
        </table>
        HTML;
	} else {
		$statistics_html = notice_html( 'Statistics for this user are currently unavailable.', 'notice' );
	}

	$about_html = '';
	if ( ! empty( $about ) ) {
		$about_html = <<<HTML
            <div class="section-title">
                <h3>About</h3>
                <hr/>
            </div>
            <p>{$about}</p>
        HTML;
	}

	return <<<HTML
        <section class="policyms policyms-user-overview">
            <header>
                <h2>Overview</h2>
            </header>
            <div>
                {$about_html}
                {$links}
            </div>
            {$statistics_html}
        </section>
    HTML;
}

/**
 * Get the user description list HTML container.
 *
 * @param PolicyMS_Description_Collection $descriptions The list of the user's PolicyMS_Description instances.
 * @param bool                            $visitor Whether the requester is a visitor.
 * @param bool                            $administrator Whether the requester is aan administrator.
 * @param string                          $description_url_base The base url for the single description page.
 * @param string                          $description_archive_url The base url for the description archive.
 * @param ?string                         $creation_url The URL of the creation page of a given type.
 * @param int                             $active_page The currently selected page, if any.
 * @param ?string                         $active_category  The selected category, if any.
 * @param ?string                         $sorting  The selected item sorting, if any.
 * @param ?int                            $sizing  The selected size of the page, if any.
 * @return string The user description list HTML container.
 *
 * @since 2.0.0
 * @author Alexandros Raikos <alexandros@araikos.gr>
 */
function user_descriptions_list_html(
	PolicyMS_Description_Collection $descriptions,
	bool $visitor,
	bool $administrator,
	string $description_url_base,
	string $description_archive_url,
	?string $creation_url = null,
	int $active_page = 1,
	?string $active_category = null,
	string $sorting = 'newest',
	int $sizing = 12
): string {
	$description_list = content_list_html(
		$visitor,
		'PolicyMS_Description',
		$descriptions->get_page( $active_page ),
		function ( $description ) use ( $description_url_base, $description_archive_url, $visitor, $administrator ) {
			$updated_date_unix      = strtotime( $description->metadata['uploadDate'] );
			$updated_date_formatted = time_elapsed_string(
				gmdate( 'Y-m-d H:i:s', strtotime( $description->metadata['updateDate'] ) )
			);
			$status_label           = '';
			if ( ! $visitor || $administrator ) {
				$status_label_class = ( 1 === $description->metadata['approved'] ) ? 'success' : 'notice';
				$status_label_text  = ( 1 === $description->metadata['approved'] ) ? 'Approved' : 'Pending';
				$status_label       = <<<HTML
                <span class="label {$status_label_class}">
                    {$status_label_text}
                </span>
                HTML;
			}
			$category_label = PolicyMS_Description::$categories[ $description->type ];
			// TODO @alexandrosraikos: Add 'Keywords' (#128).
			return <<<HTML
            <li 
                class="policyms-user-description"
                data-type-filter="{$description->type}" 
                data-date-updated="{$updated_date_unix}" 
                data-rating="{$description->metadata['reviews']['average_rating']}" 
                data-total-views="{$description->metadata['views']}" 
                class="visible">
                <div>
                    <a href="{$description_url_base}?did={$description->id}">
                        <h4>{$description->information['title']}</h4>
                    </a>
                    <p>{$description->information['short_desc']}</p>
                    <div class="metadata">
                        <a href="{$description_archive_url}?type={$description->type}"  class="pill">{$category_label}</a>
                        <span>
                            <span class="fas fa-star"></span> {$description->metadata['reviews']['average_rating']} ({$description->metadata['reviews']['no_reviews']} reviews)
                        </span>
                        <span>
                            <span class="fas fa-eye"></span> {$description->metadata['views']} views
                        </span>
                        <span>Last updated {$updated_date_formatted}</span>
                        {$status_label}
                    </div>
                </div>
            </li>
            HTML;
		},
		$descriptions->total_pages,
		PolicyMS_Description::$categories,
		$active_page,
		$active_category,
		$sorting,
		$sizing,
		$creation_url
	);

	return <<<HTML
        <section class="policyms policyms-user-descriptions">
            <h2>Descriptions</h2>
            {$description_list}
        </section>
    HTML;
}

/**
 * Get the user review list HTML container.
 *
 * @param array   $reviews The list of the user's PolicyMS_Reviews instances.
 * @param bool    $visitor Whether the requester is a visitor.
 * @param string  $single_url The base url for the single description page.
 * @param int     $active_page The currently selected page, if any.
 * @param ?string $sorting  The selected item sorting, if any.
 * @param ?int    $sizing  The selected size of the page, if any.
 * @return string The user review list HTML container.
 *
 * @since 2.0.0
 * @author Alexandros Raikos <alexandros@araikos.gr>
 */
function user_reviews_list_html(
	array $reviews,
	bool $visitor,
	string $single_url,
	int $active_page = 1,
	string $sorting = 'newest',
	int $sizing = 12
): string {
	$review_list = content_list_html(
		$visitor,
		'PolicyMS_Review',
		$reviews['content'][0] ?? array(),
		function ( $type, $review ) use ( $single_url ) {
			$updated_date_unix      = strtotime( $review->update_date );
			$updated_date_formatted = time_elapsed_string(
				gmdate( 'Y-m-d H:i:s', strtotime( $review->update_date ) )
			);
			return <<<HTML
                <li 
                    class="policyms-user-review-listed"
                    data-type-filter="{$review->description_collection}" 
                    data-date-updated="{$updated_date_unix}" 
                    data-rating="{$review->rating}" class="visible">
                    <div>
                        <div class="rating">
                            <span>
                                <span class="fas fa-star"></span> {$review->rating}
                            </span>
                            <span>
                                Posted {$updated_date_formatted}</span>
                        </div>
                        <p><em>"{$review->comment}"</em></p>
                        <a href="{$single_url}?did={$review->description_id}#reviews">
                             {$review->description_title}
                        </a>
                        <div class="metadata">
                            <a class="pill small">{$review->description_collection}</a>
                        </div>
                    </div>
                </li>
            HTML;
		},
		$reviews['pages'] ?? 1,
		null,
		$active_page,
		'newest',
		$sorting,
		$sizing
	);

	return <<<HTML
        <section class="policyms policyms-user-reviews">
            <h2>Reviews</h2>
            {$review_list}
        </section>
    HTML;
}


/**
 * Get the user description approval list HTML container.
 *
 * @param PolicyMS_Description_Collection $approvals The list of the user's PolicyMS_Description instances to be approved.
 * @param string                          $description_url_base The base url for the single description page.
 * @param string                          $description_archive_url The base url for the description archive.
 * @param int                             $active_page The currently selected page, if any.
 * @param ?string                         $active_category  The selected category, if any.
 * @param ?string                         $sorting  The selected item sorting, if any.
 * @param ?int                            $sizing  The selected size of the page, if any.
 * @return string The user description approval list HTML container.
 *
 * @since 2.0.0
 * @author Alexandros Raikos <alexandros@araikos.gr>
 */
function user_approvals_list_html(
	PolicyMS_Description_Collection $approvals,
	string $description_url_base,
	string $description_archive_url,
	int $active_page = 1,
	string $active_category = null,
	string $sorting = 'newest',
	int $sizing = 12
) {
	$description_list = content_list_html(
		false,
		'PolicyMS_Description',
		$approvals->get_page( $active_page ),
		function ( $type, $description ) use ( $description_url_base, $description_archive_url ) {
			$updated_date_unix      = strtotime( $description->metadata['uploadDate'] );
			$updated_date_formatted = time_elapsed_string(
				gmdate( 'Y-m-d H:i:s', strtotime( $description->metadata['updateDate'] ) )
			);
			// TODO @alexandrosraikos: Add 'Keywords' (#128).
			$category_label = PolicyMS_Description::$categories[ $description->type ];
			return <<<HTML
            <li 
                class="policyms-user-description"
                data-type-filter="{$description->type}" 
                data-date-updated="{$updated_date_unix}" 
                data-rating="{$description->metadata['reviews']['average_rating']}" 
                data-total-views="{$description->metadata['views']}" 
                class="visible">
                <div>
                    <a href="{$description_url_base}?did={$description->id}">
                        <h4>{$description->information['title']}</h4>
                    </a>
                    <p>{$description->information['short_desc']}</p>
                    <div class="metadata">
                        <a href="{$description_archive_url}?type={$description->type}" class="pill">
                            {$category_label}
                        </a>
                        <span>
                            <span class="fas fa-star"></span> {$description->metadata['reviews']['average_rating']} ({$description->metadata['reviews']['no_reviews']} reviews)
                        </span>
                        <span>
                            <span class="fas fa-eye"></span> {$description->metadata['views']} views
                        </span>
                        <span>Last updated {$updated_date_formatted}</span>
                        <span class="label notice">
                            Pending
                        </span>
                    </div>
                </div>
            </li>
            HTML;
		},
		$approvals->total_pages,
		PolicyMS_Description::$categories,
		$active_page,
		$active_category,
		$sorting,
		$sizing
	);

	return <<<HTML
        <section class="policyms policyms-user-approvals">
            <h2>Approvals</h2>
            {$description_list}
        </section>
    HTML;
}

/**
 * Get the user profile details and editor HTML.
 *
 * @param PolicyMS_User             $user The user object.
 * @param bool                      $visitor Whether the requester is a visitor.
 * @param bool                      $administrator Whether the requester is an administrator.
 * @param string                    $profile_image_blob The base64 encoded profile image blob.
 * @param PolicyMS_OAuth_Controller $oauth_controller The OAuth controller instance.
 * @param string                    $editing_nonce The verification nonce for user profile editing.
 * @param string                    $verification_nonce The verification nonce for user profile verification.
 * @param string                    $deletion_nonce The verification nonce for user profile deletion.
 * @param string                    $data_copy_nonce The verification nonce for user profile data copy retrieval.
 * @return string The user profile details and editor HTML.
 *
 * @since 2.0.0
 */
function user_profile_details_html(
	PolicyMS_User $user,
	bool $visitor,
	bool $administrator,
	string $profile_image_blob,
	PolicyMS_OAuth_Controller $oauth_controller,
	string $editing_nonce = '',
	string $verification_nonce = '',
	string $deletion_nonce = '',
	string $data_copy_nonce = ''
): string {

	// The account information editing button.
	$edit_button = '';
	if ( ! $visitor || $administrator ) {
		$edit_button = <<<HTML
            <button class="action small" data-action="policyms-account-edit-toggle">
                <span class="fas fa-pen"></span> Edit
            </button>
        HTML;
	}

	// The profile picture selector.
	$picture_editor = '';
	if ( $administrator || ! $visitor ) {
		$existing_picture = '';
		if ( $user->picture ) {
			$delete_picture = '';
			if ( 'default_image_users' !== $user->preferences['profile_image'] ) {
				$delete_picture = <<<HTML
                    <button 
                        type="button" 
                        data-action="delete-picture" 
                        class="action destructive">
                        Remove
                    </button>
                HTML;
			}
			$existing_picture = <<<HTML
            <div class="file-editor" data-name="profile-picture">
                <img class="file" src="{$profile_image_blob}" draggable="false" />
                {$delete_picture}
            </div>
            HTML;
		}
		$picture_editor = <<<HTML
            <tr>
                <td class="folding">
                    <span>Profile picture</span>
                    <p class="discreet">
                        Please select an image of up to 1MB and over 256x256 for optimal results. Supported file types: jpeg, png.
                    </p>
                </td>
                <td class="folding">
                    {$existing_picture}
                    <span class="folding">
                        <input type="file" name="profile_picture" accept="image/png, image/jpeg" />
                    </span>
                </td>
            </tr>
        HTML;
	}

	// 'About' field editor.
	$about_field = '';
	if ( ! $visitor || $administrator ) {
		$about       = $user->information['about'] ?? '';
		$about_field = <<<HTML
            <textarea 
                name="about" 
                class="folding" 
                placeholder="Tell us about yourself" 
                style="resize:vertical">{$about}</textarea>
        HTML;
	}

	// 'Links' fields and editor.
	$links = '';
	if ( ! empty( $user->information['social'][0] ) ) {
		foreach ( $user->information['social'] as $link ) {
			$link_title = explode( ':', $link, 2 )[0];
			$link_url   = explode( ':', $link, 2 )[1];
			$links     .= <<<HTML
                <a href="{$link_url}" target="blank">{$link_title}</a><br/>
            HTML;
		}
	}
	$links_editor = '';
	if ( (! $visitor || $administrator) ) {
        $links_editor = '<div class="folding">'.link_input_fields_html($user->information['social']).'</div>';
    }

	// 'Password' editor.
	$password_editor = '';
	if ( ! $visitor ) {
		$password_value = '';
		if ( '0' === $user->metadata['password_protected'] ) {
			$password_value = '<span class="folding visible"><em>(Not yet set)</em></span>';
		} else {
			$password_value = '<span class="folding visible">*****************</span>';
		}
		$password_editor = <<<HTML
        <tr>
            <td>
                Password
            </td>
            <td>
                {$password_value}
                <input class="folding" type="password" name="password" placeholder="Enter your new password " />
                <input class="folding" type="password" name="password-confirm" placeholder="Confirm your new password" />
            </td>
        </tr>
        HTML;
	}

	// 'Role' field.
	$role = ( 'admin' === $user->metadata['role'] ) ? 'Administrator' : 'User';

	// 'Full name' fields.
	$full_name        = ( ( ( $user->information['title'] ?? '-' ) === '-' ) ? '' : $user->information['title'] ) . ' ';
	$full_name       .= ( $user->information['name'] ) . ' ' . ( $user->information['surname'] );
	$full_name_editor = '';
	if ( ! $visitor || $administrator ) {
		$full_name_title_options = '';
		foreach ( PolicyMS_User::$titles as $id => $title ) {
			$selected                 = $id === $user->information['title'] ? 'selected' : '';
			$full_name_title_options .= <<<HTML
                <option value="{$id}" {$selected}>{$title}</option>"
            HTML;
		}
		$full_name_editor .= <<<HTML
            <select class="folding" name="title">
                {$full_name_title_options}
            </select>
            <input 
                class="folding" 
                type="text" 
                name="name" 
                placeholder="Name" 
                value="{$user->information['name']}" 
                required />
            <input 
                class="folding" 
                type="text" 
                name="surname" 
                placeholder="Surname" 
                value="{$user->information['surname']}" 
                required />
        HTML;
	}

	// 'Gender' fields.
	$gender_title  = PolicyMS_User::$genders[ $user->information['gender'] ];
	$gender_editor = '';
	if ( ! $visitor || $administrator ) {
		$gender_editor_options = '';
		foreach ( PolicyMS_User::$genders as $id => $gender ) {
			$selected               = $id === $user->information['gender'] ? 'selected' : '';
			$gender_editor_options .= <<<HTML
                <option value="{$id}" {$selected}>$gender</option>
            HTML;
		}
		$gender_editor = <<<HTML
            <select name="gender" class="folding">
                {$gender_editor_options}
            </select>
        HTML;
	}

	// 'Organization' fields.
	$organization        = $user->information['organization'] ?? '-';
	$organization_editor = '';
	if ( ! $visitor || $administrator ) {
		$organization_editor = <<<HTML
            <input 
                class="folding" 
                type="text" 
                name="organization" 
                value="{$organization}" 
                placeholder="Insert your organization here" />
        HTML;
	}

	// 'Email' fields.
	$email_information = '';
	if ( $user->information['email'] || ! $visitor ) {
		$email_verification = '';
		if ( '1' !== $user->metadata['verified'] && ! $visitor ) {
			$email_verification = <<<HTML
                <span class="unverified">(Unverified)</span>
                <button 
                    data-action="policyms-resend-verification-email"
                    data-nonce="{$verification_nonce}">
                    Resend verification email
                </button>
            HTML;
		} else {
			if ( ! $visitor || $administrator ) {
				$email_verification_status = PolicyMS_Account::$privacy_switches[ $user->preferences['public_email'] ];
				$email_verification        = <<<HTML
                    <span class="pill">{$email_verification_status}</span>
                HTML;
			}
		}

		$email_editor = '';
		if ( ! $visitor || $administrator ) {
			$email_privacy_options = '';
			foreach ( PolicyMS_Account::$privacy_switches as $id => $label ) {
				$selected               = $id === $user->preferences['public_email'] ? 'selected' : '';
				$email_privacy_options .= <<<HTML
                    <option value="{$id}" {$selected}>{$label}</option>
                HTML;
			}
			$email_editor = <<<HTML
                <input 
                    class="folding" 
                    type="email" 
                    name="email" 
                    value="{$user->information['email']}" 
                    required />
                <select name="public-email" class="folding">
                    {$email_privacy_options}
                </select>
            HTML;
		}

		$email_information = <<<HTML
            <tr>
                <td>
                    E-mail
                    <p class="folding discreet">Changing this setting will require e-mail verification.</p>
                </td>
                <td>
                    <span class="folding visible">
                        <a href="mailto:{$user->information['email']}">
                            {$user->information['email']}
                        </a>
                        {$email_verification}
                    </span>
                    {$email_editor}
                </td>
            </tr>
        HTML;
	}

	// 'Phone' fields.
	$phone_information = '';
	if ( $user->information['phone'] || ! $visitor ) {
		$phone_privacy_label = '';
		$phone_privacy       = '';
		if ( ( ! $visitor || $administrator ) && ! empty( $user->information['phone'] ) ) {
			$phone_privacy_label = PolicyMS_Account::$privacy_switches[ $user->preferences['public_phone'] ];
			$phone_privacy       = <<<HTML
                <span class="pill">
                    {$phone_privacy_label}
                </span>
            HTML;
		}

		$phone_editor = '';
		if ( ! $visitor || $administrator ) {
			$phone_privacy_options = '';
			foreach ( PolicyMS_Account::$privacy_switches as $id => $label ) {
				$selected               = $id === $user->preferences['public_phone'] ? 'selected' : '';
				$phone_privacy_options .= <<<HTML
                    <option value="{$id}" {$selected}>{$label}</option>
                HTML;
			}
			$phone_editor = <<<HTML
                <input 
                    class="folding" 
                    type="tel" 
                    name="phone" 
                    value="{$user->information['phone']}" 
                    placeholder="Insert your phone number here" />
                <select name="public-phone" class="folding">
                    {$phone_privacy_options}
                </select>
            HTML;
		}

		$phone_value = ( ! empty( $user->information['phone'] ) )
			? $user->information['phone']
			: '<em>Not provided</em>';
        $phone_value = <<<HTML
            <a href="tel:{$phone_value}">{$phone_value}</a>
        HTML;

		$phone_information = <<<HTML
        <tr>
            <td>Phone number</td>
            <td>
                <span class="folding visible">
                    {$phone_value} {$phone_privacy}
                </span>
                {$phone_editor}
            </td>
        </tr>
        HTML;
	}

	// SSO connection fields.
	$sso_information = '';
	if ( ! $visitor ) {

		// 'Google' fields.
		$google_action      = $oauth_controller->get_html( 'google' );
		$google_information = <<<HTML
            <tr>
                <td>Google</td>
                <td>{$google_action}</td>
            </tr>
        HTML;

		// 'KeyCloak' fields.
		$keycloak_action      = $oauth_controller->get_html( 'keycloak' );
		$keycloak_information = <<<HTML
            <tr>
                <td>PolicyCLOUD</td>
                <td>{$keycloak_action}</td>
            </tr>
        HTML;

		// 'EGI' fields.
		$egi_action = $oauth_controller->get_html( 'egi-check-in' );

		$egi_information = <<<HTML
            <tr>
                <td>EGI Check-In</td>
                <td>{$egi_action}</td>
            </tr>
        HTML;

		$sso_information = <<<HTML
            {$google_information}
            {$keycloak_information}
            {$egi_information}
        HTML;
	}

	// 'Member since' field.
	$registration_date_formatted = gmdate( 'd/m/y', strtotime( $user->metadata['registration_datetime'] ) );

	// Request data copy button.
	$request_data_copy_button = '';
	if ( ! $visitor ) {
		$request_data_copy_button = <<<HTML
            <button 
                data-action="policyms-user-request-data-copy" 
                data-nonce="{$data_copy_nonce}"
                class="action">
                Request data copy
            </button>
        HTML;
	}

	// Delete account button.
	$delete_account_button = '';
	if ( ! $visitor || $administrator ) {
		$delete_account_button = <<<HTML
            <button 
                data-action="policyms-delete-user" 
                data-nonce="{$deletion_nonce}" 
                data-user="{$user->uid}"
                class="action destructive" >
                Delete account
            </button>
        HTML;
	}

	// Notices.
	$notice_containers = '';
	if ( ! $visitor || $administrator ) {
		$notice_containers = <<<HTML
            <div class="critical-action">
                <label for="current-password">Please type your current password to continue.</label>
                <input name="current-password" type="password" placeholder="Insert your current password here">
            </div>
        HTML;
	}

	return <<<HTML
        <section class="policyms policyms-user-profile">
            <header>
                <h2>Information</h2>
                {$edit_button}
            </header>
            <form
                data-action="policyms-user-editing"
                data-nonce="{$editing_nonce}" 
                accept-charset="utf8" 
                action="">
                <table class="information">
                    {$picture_editor}
                    <tr>
                        <td>Summary</td>
                        <td>
                            <span class="folding visible">{$user->information['about']}</span>
                            {$about_field}
                        </td>
                    </tr>
                    <tr>
                        <td>Related links</td>
                        <td>
                            <span class="folding visible">{$links}</span>
                            {$links_editor}
                        </td>
                    </tr>
                    {$password_editor}
                    <tr>
                        <td>Role</td>
                        <td>
                            <span>{$role}</span>
                        </td>
                    </tr>
                    <tr>
                        <td>Full name</td>
                        <td>
                            <span class="folding visible">
                                {$full_name}
                            </span>
                            {$full_name_editor}
                        </td>
                    </tr>
                    <tr>
                        <td>Gender</td>
                        <td>
                            <span class="folding visible">
                                {$gender_title}
                            </span>
                            {$gender_editor}
                        </td>
                    </tr>
                    <tr>
                        <td>Organization</td>
                        <td>
                            <span class="folding visible">
                                {$organization}
                            </span>
                            {$organization_editor}
                        </td>
                    </tr>
                    {$email_information}
                    {$phone_information}
                    {$sso_information}
                    <tr>
                        <td>Member since</td>
                        <td>{$registration_date_formatted}</td>
                    </tr>
                </table>
                {$notice_containers}
                <div class="actions">
                    {$delete_account_button}
                    {$request_data_copy_button}
                    <button class="folding action" data-action="policyms-edit-user">
                        Save changes
                    </button>
                </div>
            </form>
        </section>
    HTML;
}

/**
 * Display the account page HTML for authenticated users.
 *
 * @param   PolicyMS_User $user The user object.
 * @param bool          $visitor Whether the requester is a visitor.
 * @param bool          $preview Whether the requester is previewing their own profile.
 * @param string        $account_page_url The generic account page's URL.
 * @param string        $content_html The generated HTML for the main content container.
 * @param string        $selected_tab The identifier of the selected tab.
 * @param string        $tab_switch_nonce The verification nonce for switching profile tabs.
 * @return string The account page HTML.
 *
 * @since   1.0.0
 * @author  Alexandros Raikos <alexandros@araikos.gr>
 */
function user_html(
		PolicyMS_User $user,
		bool $visitor,
		bool $preview,
		string $account_page_url,
		string $content_html,
		string $selected_tab = 'overview',
		string $tab_switch_nonce = ''
		): string {

	// Autocorrects unsupported selected tab value.
	if ( ! PolicyMS_User::$default_tabs[ $selected_tab ] ) {
		$selected_tab = 'overview';
	}

	$preview_notice = '';
	if ( $preview ) {
		$preview_notice = notice_html(
			"You're currently viewing your profile as it is viewed by other registered users.
            <a href=\"{$account_page_url}\">Return to your account page.</a>",
			'notice'
		);
	}

	// Show account verification notice.
	$verification_notice = '';
	if ( isset( $user->metadata['verified'] ) ) {
		if ( '1' !== $user->metadata['verified'] ) {
			$verification_notice = notice_html( 'Your account is still unverified, please check your email inbox or spam folder for a verification email. You can resend it in your profile settings if you can\'t find it.', 'notice' );
		}
	} else {
		return notice_html( "Your account verification status couldn't be accessed." );
	}

	$navigation_html = '';
	foreach ( PolicyMS_User::$default_tabs as $identifier => $label ) {

		// Administration-only tabs check.
		if ( 'approvals' === $identifier &&
		( ! $user->is_admin() || $visitor )
		) {
			continue;
		}

		// Prepare counter.
		$counter = '';
		if ( ! empty( $user->resources[ $identifier ] ) ) {
			$counter = '<span class="pill small">' . $user->resources[ $identifier ] . '</span>';
		}

		$active           = ( $identifier === $selected_tab ) ? 'active' : '';
		$navigation_html .= <<<HTML
            <button 
                class="tactile {$active}"
                data-tab-identifier="{$identifier}"
                data-nonce="{$tab_switch_nonce}"
                data-action="policyms-switch-user-tab">
                    {$label} {$counter}
            </button>
        HTML;
	}

	$log_out_button = '';
	if ( ! $visitor ) {
		$log_out_button = '<button class="tactile" data-action="policyms-user-logout">Sign out</button>';
	}

	$full_name    = '';
	$full_name    = ( ( $user->information['title'] ?? '-' ) === '-' ) ? '' : $user->information['title'] . ' ';
	$full_name   .= $user->information['name'] . ' ' . $user->information['surname'];
	$organization = '';
	if ( ! empty( $user->information['organization'] ) ) {
		$organization = '<span class="fas fa-id-badge"></span> ' . $user->information['organization'];
	}
	$visitor_attribute = $visitor ? 'visitor' : '';
	$picture_encoded   = $user->__get( 'picture' );

	return <<<HTML
        {$verification_notice}
        {$preview_notice}
        <div 
            class="policyms policyms-user" 
            data-user-id="{$user->id}"
            {$visitor_attribute}>
            <aside class="sidebar">
                <img src="{$picture_encoded}"  alt="" draggable="false" />
                <nav>
                    {$navigation_html}
                    {$log_out_button}
                </nav>
            </aside>
            <main>
                <header>
                    <div class="title">{$full_name}</div>
                    <div class="organization">{$organization}</div>
                </header>
                <section data-content="{$selected_tab}">
                    {$content_html}
                </section>
            </main>
        </div>
    HTML;
}
