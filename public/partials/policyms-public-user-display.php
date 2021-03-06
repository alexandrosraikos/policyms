<?php

/**
 * Print the account registration form.
 *
 * @param   string $authenticationurl The url that redirects to the log in page.
 * @param   string $error_message Any potential error message to be displayed.
 *
 * @since    1.0.0
 */
function account_user_registration_html( $authentication_url, $tos_url, $egi_options, $authenticated ) {

	if ( $authenticated ) {
		show_alert( "You're already logged in.", 'notice' );
	} else {
		?>
		<div class="policyms">
			<div class="sso">
				<p>You can quickly setup your account using an existing account in the following services:</p>
				<div class="actions">
					<?php googleButton( true ); ?>
					<button id="keycloak-registration" class="action keycloak" data-action="keycloak-form">Sign up with PolicyMS (Internal)</button>
					<button id="egi-signin" class="action egi" onClick="window.location.href = 'https://aai-demo.egi.eu/auth/realms/egi/protocol/openid-connect/auth?client_id=<?php echo $egi_options['egi_client_id']; ?>&scope=profile%20openid%20email&redirect_uri=<?php echo substr( $egi_options['egi_redirection_page'], 0, -1 ); ?>&response_type=code&code_challenge=<?php echo $egi_options['egi_code_challenge']; ?>&code_challenge_method=S256'">
						Sign in with EGI Check-in
					</button>
				</div>
			</div>
			<form id="policyms-registration" action="">
				<fieldset name="account-details">
					<h2>Account details</h2>
					<p>Fill in the following fields with your personal details. This information will be used to personalize your experience within the marketplace platform and showcase your profile to other registered users. Fields marked with (*) are required for registration.</p>
					<label for="title">Title</label>
					<select name="title">
						<option value="Mr.">Mr.</option>
						<option value="Ms.">Ms.</option>
						<option value="Mrs.">Mrs.</option>
						<option value="Dr.">Dr.</option>
						<option value="Prof.">Prof.</option>
						<option value="Sir">Sir</option>
						<option value="Miss">Miss</option>
						<option value="Mx.">Mx.</option>
						<option value="-" selected>None</option>
					</select>
					<label for="name">First name *</label>
					<input required name="name" placeholder="Insert your first name" type="text" />
					<label for="surname">Last name *</label>
					<input required name="surname" placeholder="Insert your last name" type="text" />
					<label for="organization">Organization</label>
					<input name="organization" placeholder="Insert your organization" type="text" />
					<label for="gender">Gender</label>
					<select name="gender">
						<option value="male">Male</option>
						<option value="female">Female</option>
						<option value="transgender">Transgender</option>
						<option value="genderqueer">Genderqueer</option>
						<option value="questioning">Questioning</option>
						<option value="-" selected>Prefer not to say</option>
					</select>
					<label for="about">Summary</label>
					<textarea name="about" placeholder="Tell us about yourself" style="resize:vertical"></textarea>
					<label for="socials">Related links</label>
					<div class="socials">
						<div>
							<div>
								<input type="text" name="socials-title[]" placeholder="Example" />
								<input type="url" name="socials-url[]" placeholder="https://www.example.org/" />
								<button class="remove-field" title="Remove this link."><span class="fas fa-times"></span></button>
							</div>
						</div>
						<button class="add-field" title="Add another link."><span class="fas fa-plus"></span> Add link</button>
					</div>
				</fieldset>
				<fieldset name="account-contact">
					<h2>Account contact details</h2>
					<p>Fill in your contact information here. This information will be used to validate your new account, as well as optionally make them visible to other logged in Marketplace visitors. Fields marked with (*) are required for registration. These details remain private by default. </p>
					<label for="email">E-mail address *</label>
					<input type="email" name="email" placeholder="e.g. johndoe@example.org" required />
					<label for="phone">Phone number</label>
					<input type="tel" name="phone" placeholder="e.g. +30 6999123456" />
				</fieldset>
				<fieldset name="account-credentials">
					<h2>Account credentials</h2>
					<p>The following information is required for authorization purposes.</p>
					<label for="password">Password *</label>
					<input required name="password" placeholder="Insert your password" type="password" />
					<label for="password-confirm">Confirm password *</label>
					<input required name="password-confirm" placeholder="Insert your password again" type="password" />
				</fieldset>
				<div class="tos-agree">
					<input type="checkbox" id="tos-agree" name="tos-agree" required />
					<label for="tos-agreee">
						By submitting this registration form, you agree to our <a class="underline" href="<?php echo $tos_url; ?>">Terms of Service</a>.
					</label>
				</div>
				<div class="actions">
					<button type="submit" class="action ">Create account</button>
				</div>
				<p>Already have an account? Please <a class="underline" href="<?php echo $authentication_url; ?>">Log in</a>.</p>
			</form>
		</div>
		<?php
	}
}

function googleButton( bool $registration = false ) {
	?>
	<script src="https://accounts.google.com/gsi/client" async defer></script>
	<script>
		function googleRegistrationCallback(response) {
			makeWPRequest(
				'#google-signin',
				'policyms_account_user_registration_google',
				'<?php echo wp_create_nonce( 'policyms_account_user_registration_google' ); ?>', {
					google_token: response.credential
				},
				(data) => {
					setAuthorizedToken(data);
					window.location.href = GlobalProperties.rootURLPath;
				}
			)
		}

		function googleCallback(response) {
			makeWPRequest(
				'#google-signin',
				'policyms_account_user_authentication_google',
				'<?php echo wp_create_nonce( 'policyms_account_user_authentication_google' ); ?>', {
					google_token: response.credential
				},
				(data) => {
					setAuthorizedToken(data);
					window.location.href = GlobalProperties.rootURLPath;
				}
			)
		}

		window.onload = function() {
			google.accounts.id.initialize({
				client_id: "129650564826-9bf7dhacn26c1hf1k0h0qcn48iv8mv8s.apps.googleusercontent.com",
				callback: <?php echo $registration ? 'googleRegistrationCallback' : 'googleCallback'; ?>
			});
			google.accounts.id.renderButton(
				document.getElementById("google-signin"), {
					type: 'standard',
					shape: 'rectangular',
					theme: "filled_black",
					size: "large",
					locale: "en-GB"
				}
			);
			google.accounts.id.prompt();
		}
	</script>
	<div id="google-signin" class="action minimal"></div>
	<?php
}


/**
 * Print the account authorization form.
 *
 * @param   string $registration_url The url that redirects to the registration page.
 * @param   bool   $logged_in Whether the viewer is already logged in.
 * @param   string $error_message Any potential error message to be displayed.
 *
 * @since    1.0.0
 */
function account_user_authentication_html( $registration_url, $reset_password_page, $egi_options, $authenticated ) {
	if ( ! $authenticated ) {
		?>
		<div class="policyms">
			<div class="sso">
				<p>You can connect to your account using the following services:</p>
				<div class="actions">
					<?php googleButton(); ?>
					<button id="keycloak-signin" class="action keycloak" data-action="keycloak-form">Sign in with PolicyMS (Internal)</button>
					<button id="egi-signin" class="action egi" onClick="window.location.href = 'https://aai-demo.egi.eu/auth/realms/egi/protocol/openid-connect/auth?client_id=<?php echo $egi_options['egi_client_id']; ?>&scope=profile%20openid%20email&redirect_uri=<?php echo substr( $egi_options['egi_redirection_page'], 0, -1 ); ?>&response_type=code&code_challenge=<?php echo $egi_options['egi_code_challenge']; ?>&code_challenge_method=S256'">
						Sign in with EGI Check-in
					</button>
				</div>
			</div>
			<form id="policyms-authentication">
				<fieldset name=" account-credentials">
					<h2>Insert your credentials</h2>
					<p>The following information is required to log you in.</p>
					<label for="email">E-mail address *</label>
					<input required name="email" placeholder="e.g. johndoe@example.org" type="email" />
					<label for="password">Password *</label>
					<input required name="password" placeholder="Insert your password" type="password" />
				</fieldset>
				<div class="actions">
					<button type="submit" class="action">Log in</button>
				</div>
				<p>Don't have an account yet? You can <a class="underline" href="<?php echo $registration_url; ?>">register</a> now to obtain full access to the Marketplace. If you have forgotten your credentials, you can <a class="underline" href="<?php echo $reset_password_page; ?>">reset your password.</a></p>
			</form>
		</div>
		<?php
	} else {
		show_alert( "You're already logged in.", 'notice' );
	}
}

/**
 * Print the password reset form.
 *
 * @param   string $registration_url The url that redirects to the registration page.
 * @param   bool   $logged_in Whether the viewer is already logged in.
 * @param   string $error_message Any potential error message to be displayed.
 *
 * @since    1.0.0
 */
function account_user_reset_password_html( $authenticated ) {
	?>
	<div class="policyms">
		<form id="policyms-password-reset">
			<fieldset>
				<h2>Reset your password</h2>
				<p>Insert your e-mail address below and we will contact you with instructions to reset your password.</p>
				<label for="email">E-mail address *</label>
				<input required name="email" placeholder="e.g. johndoe@example.org" type="email" />
				<div class="actions">
					<button type="submit" class="action">Reset password</button>
				</div>
			</fieldset>
		</form>
	</div>
	<?php
}


/**
 * Display the account page HTML for authenticated users.
 *
 * @param   array $information The user information array.
 * @param   array $item The assets connected to this account.
 * @param   array $data['statistics'] The statistics connected to this account.
 * @param   array $reviews The asset reviews connected to this account.
 * @param   array $args An array of arguments.
 *
 * @uses    show_alert()
 * @uses    time_elapsed_string()
 *
 * @usedby  PolicyMS_Public::account_shortcode()
 *
 * @since   1.0.0
 * @author  Alexandros Raikos <araikos@unipi.gr>
 */
function account_user_html( array $data, bool $admin, bool $visitor, array $pages ) {

	// Show account verification notice.
	if ( isset( $data['metadata']['verified'] ) ) {
		if ( $data['metadata']['verified'] !== '1' ) {
			show_alert( 'Your account is still unverified, please check your email inbox or spam folder for a verification email. You can resend it in your profile settings if you can\'t find it.', 'notice' );
		}
	} else {
		show_alert( "Your account verification status couldn't be accessed." );
	}

	$descriptions_count = array_sum(
		array_map(
			function ( $page ) {
				return count( $page );
			},
			$data['descriptions'] ?? array()
		)
	);
	$reviews_count      = array_sum(
		array_map(
			function ( $page ) {
				return count( $page );
			},
			$data['reviews'] ?? array()
		)
	);
	$approvals_count    = array_sum(
		array_map(
			function ( $page ) {
				return count( $page );
			},
			$data['approvals'] ?? array()
		)
	);

	?>
	<div id="policyms-account" class="policyms">
		<div id="policyms-account-sidebar">
			<?php
			echo '<img src="' . $data['picture'] . '" draggable="false" />';
			?>
			<nav>
				<button class="tactile" id="policyms-account-overview" class="active">Overview</button>
				<button class="tactile" id="policyms-account-descriptions">Descriptions <span class="pill"><?php echo $descriptions_count; ?></span></button>
				<button class="tactile" id="policyms-account-reviews">Reviews <span class="pill"><?php echo $reviews_count; ?></span></button>
				<?php
				if ( ! $visitor && $admin ) {
					?>
					<hr />
					<button class="tactile" id="policyms-account-approvals">Approvals <span class="pill"><?php echo $approvals_count; ?></span></button>
					<hr />
					<?php
				}
				?>
				<button class="tactile" id="policyms-account-profile">Profile</button>
				<?php if ( ! $visitor ) { ?>
					<button class="tactile policyms-logout">Log out</button>
				<?php } ?>
			</nav>
		</div>
		<div id="policyms-account-content">
			<div class="policyms-account-title">
				<h2>
					<?php
					echo ( ( ( $data['information']['title'] ?? '-' ) == '-' ) ? '' : $data['information']['title'] ) . ' ' . $data['information']['name'] . ' ' . $data['information']['surname'];
					?>
				</h2>
				<div>
					<?php
					echo ( $data['information']['organization'] ?? '' );
					?>
				</div>
			</div>
			<div>
				<section class="policyms-account-overview focused">
					<header>
						<h3>Overview</h3>
					</header>
					<div>
						<h4>About</h4>
						<p>
							<?php echo $data['information']['about'] ?? ''; ?>
						</p>
						<?php
						if ( ! empty( $data['information']['social'][0] ) ) {
							?>
							<ul>
								<?php
								foreach ( $data['information']['social'] as $link ) {
									echo '<li><a href="' . explode( ':', $link, 2 )[1] . '" target="blank">' . explode( ':', $link, 2 )[0] . '</a></li>';
								}
								?>
							</ul>
						<?php } ?>
					</div>
					<?php if ( ! empty( $data['statistics'] ) ) { ?>
						<h4>Statistics</h4>
						<table class="statistics">
							<tr>
								<td>
									<div class="large-figure"><span class="fas fa-list"></span> <?php echo $data['statistics']['total_descriptions']; ?></div>
									<div class="assets-caption">Total descriptions</div>
								</td>
								<td>
									<div class="large-figure"><span class="fas fa-check"></span> <?php echo $data['statistics']['approved_descriptions']; ?></div>
									<div class="assets-caption">Approved descriptions</div>
								</td>
							</tr>
							<tr>
								<td>
									<div class="large-figure"><span class="fas fa-file"></span> <?php echo $data['statistics']['assets_uploaded']; ?></div>
									<div>Assets uploaded</div>
								</td>
								<td>
									<div class="large-figure"><span class="fas fa-share"></span> <?php echo $data['statistics']['total_links_provided']; ?></div>
									<div>Total links provided</div>
								</td>
							</tr>
							<tr>
								<td>
									<div class="large-figure"><span class="fas fa-eye"></span> <?php echo $data['statistics']['total_views']; ?></div>
									<div class="assets-caption">Total views</div>
								</td>
								<td>
									<div class="large-figure"><span class="fas fa-download"></span> <?php echo $data['statistics']['total_downloads']; ?></div>
									<div class="assets-caption">Total downloads</div>
								</td>
							</tr>
							<tr>
								<td>
									<div class="large-figure"><span class="fas fa-comment"></span> <?php echo $data['statistics']['total_reviews']; ?></div>
									<div class="assets-caption">Total received reviews</div>
								</td>
								<td>
									<div class="large-figure"><span class="fas fa-star"></span> <?php echo $data['statistics']['average_rating']; ?></div>
									<div class="assets-caption">Average rating</div>
								</td>
							</tr>
						</table>
						<?php
					} else {
						show_alert( 'Statistics for this user are currently unavailable.', 'notice' );
					}
					?>
				</section>
				<section class="policyms-account-descriptions">
					<?php
					entity_list_html(
						'descriptions',
						$data['descriptions'],
						$visitor,
						function ( $description ) use ( $pages, $visitor, $admin ) {
							?>
						<li data-type-filter="<?php echo $description->type; ?>" data-date-updated="<?php echo strtotime( $description->metadata['uploadDate'] ); ?>" data-rating="<?php echo $description->metadata['reviews']['average_rating']; ?>" data-total-views="<?php echo $description->metadata['views']; ?>" class="visible">
							<div class="description">
								<a href="<?php echo $pages['description_page'] . '?did=' . $description->id; ?>">
									<h4><?php echo $description->information['title']; ?></h4>
								</a>
								<p><?php echo $description->information['short_desc']; ?></p>
								<div class="metadata">
									<a class="pill"><?php echo $description->type; ?></a>
									<a class="pill"><?php echo $description->information['subtype']; ?></a>
									<span><span class="fas fa-star"></span> <?php echo $description->metadata['reviews']['average_rating'] . ' (' . $description->metadata['reviews']['no_reviews'] . ' reviews)'; ?></span>
									<span><span class="fas fa-eye"></span> <?php echo $description->metadata['views']; ?> views</span>
									<span>Last updated <?php echo time_elapsed_string( date( 'Y-m-d H:i:s', strtotime( $description->metadata['updateDate'] ) ) ); ?></span>
									<?php
									if ( ! $visitor || $admin ) {
										?>
										<span class="label <?php echo ( $description->metadata['approved'] == 1 ) ? 'success' : 'notice'; ?>"><?php echo ( $description->metadata['approved'] == 1 ) ? 'Approved' : 'Pending'; ?></span>
									<?php } ?>
								</div>
							</div>
						</li>
							<?php
						},
						$pages['upload_page']
					);
	?>
				</section>
				<section class="policyms-account-reviews">
					<?php
					entity_list_html(
						'reviews',
						$data['reviews'],
						$visitor,
						function ( $review ) use ( $pages ) {
							?>
						<li data-type-filter="<?php echo $review->description_collection; ?>" data-date-updated="<?php echo strtotime( $review->update_date ); ?>" data-rating="<?php echo $review->rating; ?>" class="visible">
							<div class="review">
								<div class="rating">
									<span><span class="fas fa-star"></span> <?php echo $review->rating; ?></span>
									<span>Posted <?php echo time_elapsed_string( date( 'Y-m-d H:i:s', strtotime( $review->update_date ) ) ); ?></span>
								</div>
								<p>"<?php echo $review->comment; ?>"</p>
								<a href="<?php echo $pages['description_page'] . '?did=' . $review->description_id; ?>#reviews">
									<h4><?php echo $review->description_title; ?></h4>
								</a>
								<div class="metadata">
									<a class="pill"><?php echo $review->description_collection; ?></a>
								</div>
							</div>
						</li>
							<?php
						}
					);
	?>
				</section>
				<section class="policyms-account-approvals">
					<?php
					entity_list_html(
						'approvals',
						$data['approvals'] ?? array(),
						$visitor,
						function ( $pending_description ) use ( $pages ) {
							?>
						<li data-type-filter="<?php echo $pending_description->type; ?>" data-date-updated="<?php echo strtotime( $pending_description->metadata['uploadDate'] ); ?>" data-rating="<?php echo $pending_description->metadata['reviews']['average_rating']; ?>" data-total-views="<?php echo $pending_description->metadata['views']; ?>" class="visible">
							<div class="description">
								<a href="<?php echo $pages['description_page'] . '?did=' . $pending_description->id; ?>">
									<h4><?php echo $pending_description->information['title']; ?></h4>
								</a>
								<p><?php echo $pending_description->information['short_desc']; ?></p>
								<div class="metadata">
									<a class="pill"><?php echo $pending_description->type; ?></a>
									<a class="pill"><?php echo $pending_description->information['subtype']; ?></a>
									<span><span class="fas fa-star"></span> <?php echo $pending_description->metadata['reviews']['average_rating'] . ' (' . $pending_description->metadata['reviews']['no_reviews'] . ' reviews)'; ?></span>
									<span><span class="fas fa-eye"></span> <?php echo $pending_description->metadata['views']; ?> views</span>
									<span>Last updated <?php echo time_elapsed_string( date( 'Y-m-d H:i:s', strtotime( $pending_description->metadata['uploadDate'] ) ) ); ?></span>
									<span class="label notice">Pending</span>
								</div>
							</div>
						</li>
							<?php
						}
					);
	?>
				</section>
				<section class="policyms-account-profile">
					<header>
						<h3>Information</h3>
						<?php
						if ( ! $visitor || $admin ) {
							?>
							<button id="policyms-account-edit-toggle"><span class="fas fa-pen"></span> Edit</button>
							<?php
						}
						?>
					</header>
					<form id="policyms-account-edit" accept-charset="utf8" action="">
						<table class="information">
							<?php
							if ( $admin || ! $visitor ) {
								?>
								<tr>
									<td class="folding">
										<span>Profile picture</span>
									</td>
									<td class="folding">
										<?php
										if ( ! empty( $data['picture'] ) ) {
											?>
											<div class="file-editor" data-name="profile-picture">
												<img class="file" src="<?php echo $data['picture']; ?>" draggable="false" />
												<?php if ( $data['preferences']['profile_image'] != 'default_image_users' ) { ?>
													<button type="button" data-action="delete-picture" class="action destructive">Remove</button>
												<?php } ?>
											</div>
											<?php
										}
										?>
										<span class="folding">
											<input type="file" name="profile_picture" accept="image/png, image/jpeg" />
											<label for="picture">Please select an image of up to 1MB and over 256x256 for optimal results. Supported file types: jpg, png.</label>
										</span>
									</td>
								</tr>
							<?php } ?>
							<tr>
								<td>
									Summary
								</td>
								<td>
									<span class="folding visible">
										<?php echo $data['information']['about']; ?>
									</span>
									<?php
									if ( ! $visitor || $admin ) {
										?>
										<textarea name="about" class="folding" placeholder="Tell us about yourself" style="resize:vertical"><?php echo $data['information']['about'] ?? ''; ?></textarea>
										<?php
									}
									?>
								</td>
							</tr>
							<tr>
								<td>
									Related links
								</td>
								<td>
									<span class="folding visible">
										<?php
										if ( ! empty( $data['information']['social'][0] ) ) {
											foreach ( $data['information']['social'] as $link ) {
												echo '<a href="' . explode( ':', $link, 2 )[1] . '" target="blank">' . explode( ':', $link, 2 )[0] . '</a><br/>';
											}
										}
										?>
									</span>
									<?php
									if ( ! $visitor || $admin ) {
										?>
										<div class="socials folding">
											<div>
												<?php

												if ( ! empty( $data['information']['social'][0] ) ) {
													foreach ( $data['information']['social'] as $link ) {
														$link_title = explode( ':', $link, 2 )[0];
														$link_url   = explode( ':', $link, 2 )[1];
														?>
														<div>
															<input type="text" name="socials-title[]" placeholder="Example" value="<?php echo $link_title; ?>" />
															<input type="url" name="socials-url[]" placeholder="https://www.example.org/" value="<?php echo $link_url; ?>" />
															<button class="remove-field" title="Remove this link."><span class="fas fa-times"></span></button>
														</div>
														<?php
													}
												}
												?>
											</div>
											<button class="add-field" title="Add another link."><span class="fas fa-plus"></span> Add link</button>
										</div>
										<?php
									}
									?>
								</td>
							</tr>
							<tr>
								<?php
								if ( ! $visitor ) {
									?>
									<td>
										Password
									</td>
									<td>
										<?php
										if ( $data['metadata']['password_protected'] == '0' ) {
											?>
											<span class="folding visible"><em>(Not yet set)</em></span>
											<?php
										} else {
											?>
											<span class="folding visible">*****************</span>
											<?php
										}
										?>
										<input class="folding" type="password" name="password" placeholder="Enter your new password here" />
										<input class="folding" type="password" name="password-confirm" placeholder="Confirm new password here" />
									<?php
								}
								?>
									</td>
							</tr>
							<tr>
								<td>
									Role
								</td>
								<td>
									<span>
										<?php echo ( $data['metadata']['role'] == 'admin' ) ? 'Administrator' : 'User'; ?>
									</span>
								</td>
							</tr>
							<tr>
								<td>
									Full name
								</td>
								<td>
									<span class="folding visible">
										<?php
										echo ( ( ( $data['information']['title'] ?? '-' ) == '-' ) ? '' : $data['information']['title'] ) . ' ' . ( $data['information']['name'] ) . ' ' . ( $data['information']['surname'] );
										?>
									</span>
									<?php
									if ( ! $visitor || $admin ) {
										?>
										<select class="folding" name="title">
											<option value="Mr." <?php echo ( $data['information']['title'] == 'Mr.' ? 'selected' : '' ); ?>>Mr.</option>
											<option value="Ms." <?php echo ( $data['information']['title'] == 'Ms.' ? 'selected' : '' ); ?>>Ms.</option>
											<option value="Mrs." <?php echo ( $data['information']['title'] == 'Mrs.' ? 'selected' : '' ); ?>>Mrs.</option>
											<option value="Dr." <?php echo ( $data['information']['title'] == 'Dr.' ? 'selected' : '' ); ?>>Dr.</option>
											<option value="Prof." <?php echo ( $data['information']['title'] == 'Prof.' ? 'selected' : '' ); ?>>Prof.</option>
											<option value="Sir" <?php echo ( $data['information']['title'] == 'Sir' ? 'selected' : '' ); ?>>Sir</option>
											<option value="Miss" <?php echo ( $data['information']['title'] == 'Miss' ? 'selected' : '' ); ?>>Miss</option>
											<option value="Mx." <?php echo ( $data['information']['title'] == 'Mx.' ? 'selected' : '' ); ?>>Mx.</option>
											<option value="-" <?php echo ( $data['information']['title'] == '-' ? 'selected' : '' ); ?>>None</option>
										</select>
										<input class="folding" type="text" name="name" placeholder="Name" value="<?php echo ( $data['information']['name'] ); ?>" required />
										<input class="folding" type="text" name="surname" placeholder="Surname" value="<?php echo ( $data['information']['surname'] ); ?>" required />
										<?php
									}
									?>
								</td>
							</tr>
							<tr>
								<td>
									Gender
								</td>
								<td>
									<span class="folding visible">
										<?php
										echo ( ucfirst( $data['information']['gender'] ?? '-' ) );
										?>
									</span>
									<?php
									if ( ! $visitor || $admin ) {
										?>
										<select name="gender" class="folding">
											<option value="male" <?php echo ( $data['information']['gender'] == 'male' ? 'selected' : '' ); ?>>Male</option>
											<option value="female" <?php echo ( $data['information']['gender'] == 'female' ? 'selected' : '' ); ?>>Female</option>
											<option value="transgender" <?php echo ( $data['information']['gender'] == 'transgender' ? 'selected' : '' ); ?>>Transgender</option>
											<option value="genderqueer" <?php echo ( $data['information']['gender'] == 'genderqueer' ? 'selected' : '' ); ?>>Genderqueer</option>
											<option value="questioning" <?php echo ( $data['information']['gender'] == 'questioning' ? 'selected' : '' ); ?>>Questioning</option>
											<option value="-" <?php echo ( $data['information']['gender'] == '-' ? 'selected' : '' ); ?>>Prefer not to say</option>
										</select>
										<?php
									}
									?>
								</td>
							</tr>
							<tr>
								<td>
									Organization
								</td>
								<td>
									<span class="folding visible">
										<?php
										echo ( $data['information']['organization'] ?? '-' );
										?>
									</span>
									<?php
									if ( ! $visitor || $admin ) {
										?>
										<input class="folding" type="text" name="organization" value="<?php echo ( $data['information']['organization'] ?? '' ); ?>" placeholder="Insert your organization here" />
										<?php
									}
									?>
								</td>
							</tr>
							<?php
							if ( ! empty( $data['information']['email'] ) || ! $visitor ) {
								?>
								<tr>
									<td>
										E-mail
									</td>
									<td>
										<span class="folding visible">
											<?php
											echo ( $data['information']['email'] );
											if ( $data['metadata']['verified'] != '1' && ! $visitor ) {
												?>
												<span class="unverified">(Unverified)</span>
												<button id="policyms-resend-verification-email">Resend verification email</button>
												<?php
											} else {
												if ( ! $visitor || $admin ) {
													echo ' <span class="label ' . ( ( $data['preferences']['public_email'] == 0 ) ? 'notice' : 'success' ) . '">' . ( ( $data['preferences']['public_email'] == 0 ) ? 'Private' : 'Public' ) . '</span>';
												}
											}
											?>
										</span>
										<?php
										if ( ! $visitor || $admin ) {
											?>
											<label for="email" class="folding">Changing this setting will require a verification of the new e-mail address.</label>
											<input class="folding" type="email" name="email" value="<?php echo $data['information']['email']; ?>" required />
											<select name="public-email" class="folding">
												<option value="1" <?php echo ( $data['preferences']['public_email'] == 1 ? 'selected' : '' ); ?>>Public</option>
												<option value="0" <?php echo ( $data['preferences']['public_email'] == 0 ? 'selected' : '' ); ?>>Private</option>
											</select>
											<?php
										}
										?>
									</td>
								</tr>
								<?php
							}
							if ( ! empty( $data['information']['phone'] ) || ! $visitor ) {
								?>
								<tr>
									<td>
										Phone number
									</td>
									<td>
										<span class="folding visible">
											<?php
											if ( ! empty( $data['information']['phone'] ) ) {
												echo ( $data['information']['phone'] );
												if ( ! $visitor || $admin ) {
													echo ' <span class="label ' . ( ( $data['preferences']['public_phone'] == 0 ) ? 'notice' : 'success' ) . '">' . ( ( $data['preferences']['public_phone'] == 0 ) ? 'Private' : ' Public' ) . '</span>';
												}
											} else {
												echo '-';
											}
											?>
										</span>
										<?php
										if ( ! $visitor || $admin ) {
											?>
											<input class="folding" type="text" name="phone" value="<?php echo ( empty( $data['information']['phone'] ) ? '' : $data['information']['phone'] ); ?>" placeholder="Insert your phone number here" />
											<select name="public-phone" class="folding">
												<option value="1" <?php echo ( $data['preferences']['public_phone'] == 1 ? 'selected' : '' ); ?>>Public</option>
												<option value="0" <?php echo ( $data['preferences']['public_phone'] == 0 ? 'selected' : '' ); ?>>Private</option>
											</select>
											<?php
										}
										?>
									</td>
								</tr>
								<?php
							}
							if ( ! $visitor ) {
								?>
								<tr>
									<td>
										Google account
									</td>
									<td>
										<?php
										if ( $data['metadata']['connections']['google'] == '0' ) {
											wp_enqueue_script( 'google-sso' );
											wp_enqueue_script( 'policyms-account-authentication' );
											wp_localize_script(
												'policyms-account-authentication',
												'AccountAuthenticationProperties',
												array(
													'nonce' => wp_create_nonce( 'policyms_account_user_authentication' ),
													'GoogleSSONonce' => wp_create_nonce( 'policyms_account_user_authentication_google_handler' ),
													'KeyCloakSSONonce' => wp_create_nonce( 'policyms_account_user_authentication_keycloak' ),
												)
											);
											?>
											<?php googleButton(); ?>
											<?php
										} else {
											?>
											<button class="action destructive minimal" data-action="disconnect-google" <?php echo $data['metadata']['password_protected'] == '1' ? 'password-protected' : ''; ?>>Disconnect</button>
											<?php
										}
										?>
									</td>
								</tr>
								<tr>
									<td>
										PolicyMS account (Internal)
									</td>
									<td>
										<?php
										if ( $data['metadata']['connections']['keycloak'] == '0' ) {
											wp_enqueue_script( 'policyms-account-authentication' );
											wp_localize_script(
												'policyms-account-authentication',
												'AccountAuthenticationProperties',
												array(
													'nonce' => wp_create_nonce( 'policyms_account_user_authentication' ),
													'GoogleSSONonce' => wp_create_nonce( 'policyms_account_user_authentication_google_handler' ),
													'KeyCloakSSONonce' => wp_create_nonce( 'policyms_account_user_authentication_keycloak' ),
													'RedirectSSO' => PolicyMS_Public::get_plugin_setting( true, 'account_page' ),
												)
											);
											?>
											<button id="keycloak-signin" class="action keycloak" data-action="keycloak-form">Sign in with PolicyMS (Internal)</button>
											<?php
										} else {
											?>
											<button class="action destructive minimal" data-action="disconnect-keycloak" <?php echo $data['metadata']['password_protected'] == '1' ? 'password-protected' : ''; ?>>Disconnect</button>
											<?php
										}
										?>
									</td>
								</tr>
								<tr>
									<td>
										EGI credentials
									</td>
									<td>
										<?php
										if ( $data['metadata']['connections']['egi-check-in'] == '0' ) {
											$egi_options = PolicyMS_Public::get_plugin_setting( true, 'egi_redirection_page', 'egi_client_id', 'egi_code_challenge' );
											?>
											<a id="egi-signin" href="https://aai-demo.egi.eu/auth/realms/egi/protocol/openid-connect/auth?client_id=<?php echo $egi_options['egi_client_id']; ?>&scope=profile%20openid%20email&redirect_uri=<?php echo substr( $egi_options['egi_redirection_page'], 0, -1 ); ?>&response_type=code&code_challenge=<?php echo $egi_options['egi_code_challenge']; ?>&code_challenge_method=S256"> Sign in with EGI Check-in</a>
											<?php
										} else {
											?>
											<button class="action destructive minimal" data-action="disconnect-egi" <?php echo $data['metadata']['password_protected'] == '1' ? 'password-protected' : ''; ?>>Disconnect</button>
											<?php
										}
										?>
									</td>
								</tr>
								<?php
							}
							?>
							<tr>
								<td>
									Member since
								</td>
								<td>
									<?php
									echo date( 'd/m/y', strtotime( $data['metadata']['registration_datetime'] ) )
									?>
								</td>
							</tr>
						</table>
						<?php
						if ( ! $visitor || $admin ) {
							?>
							<div class="folding error"></div>
							<div class="folding notice"></div>
							<div class="critical-action">
								<label for="current-password">Please type your current password to continue.</label>
								<input name="current-password" type="password" placeholder="Insert your current password here">
							</div>
							<button type="submit" class="folding">Submit</button>
							<?php
						}
						?>
					</form>
					<?php
					if ( ! $visitor ) {
						?>
						<button id="policyms-request-data-copy" class="action">Request data copy</button>
						<?php
					}
					if ( ! $visitor || $admin ) {
						?>
						<form id="policyms-delete-account"">
							<div>
								<label for=" current-password">Please type your current password to continue.</label>
							<input name="current-password" type="password" placeholder="Insert your current password here">
			</div>
			<button type="submit" class="action destructive" user="<?php echo $_GET['user'] ?? ''; ?>">Delete account</button>
			</form>
					<?php } ?>
		</section>
		</div>
	</div>
	</div>
	<?php
}
