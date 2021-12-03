<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://dac.ds.unipi.gr/policycloud-eu/
 * @since      1.0.0
 *
 * @package    PolicyCloud_Marketplace
 * @subpackage PolicyCloud_Marketplace/public/partials
 */
?>

<?php


/**
 *
 * Prints an auto-disappearing error or notice box.
 * The close button is handled @see policycloud-marketplace-public.js
 *
 * @param string $message The message to be shown.
 * @param bool $dismissable Whether the alert is dismissable or not.
 * @param string $type The type of message, a 'notice' or an 'error'.
 *
 * @since 1.0.0
 */
function show_alert(string $message, string $type = 'error')
{
    echo  '<div class="policycloud-marketplace-' . $type . ' "><span>' . $message . '</span></div>';
}

/**
 *
 * Prints a hidden modal with controls and a close button.
 * The visibility is handled @see policycloud-marketplace-public.js.
 *
 * @param callable $inner_html The modal content (return null if managed by jQuery).
 * @param bool $controls Whether the modal has next/previous controls.
 *
 * @since 1.0.0
 */
function show_modal($inner_html, $controls = false)
{
?>
    <div id="policycloud-marketplace-modal" class="hidden">
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
 * Print the account registration form.
 *
 * @param   string $authenticationurl The url that redirects to the log in page.
 * @param   string $error_message Any potential error message to be displayed.
 *
 * @since    1.0.0
 */
function account_user_registration_html($authentication_url, $tos_url, $authenticated)
{
    if ($authenticated) {
        show_alert("You're already logged in.", 'notice');
    } else {
    ?>
        <div class="policycloud-marketplace">
            <form id="policycloud-registration" action="">
                <fieldset name="account-credentials">
                    <h2>Account credentials</h2>
                    <p>The following information is required for authorization purposes.</p>
                    <label for="username">Username *</label>
                    <input required name="username" placeholder="e.x. johndoe" type="text" />
                    <label for="password">Password *</label>
                    <input required name="password" placeholder="Insert your password" type="password" />
                    <label for="password-confirm">Confirm password *</label>
                    <input required name="password-confirm" placeholder="Insert your password again" type="password" />
                </fieldset>
                <fieldset name="account-details">
                    <h2>Account details</h2>
                    <p>Fill in the following fields with your personal details. This information will be used to personalize your experience within the marketplace platform and showcase your profile to other visitors. Fields marked with (*) are required for registration.</p>
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
                                <button class="remove-field" title="Remove this link." disabled><span class="fas fa-times"></span></button>
                            </div>
                        </div>
                        <button class="add-field" title="Add another link."><span class="fas fa-plus"></span> Add link</button>
                    </div>
                </fieldset>
                <fieldset name="account-contact">
                    <h2>Account contact details</h2>
                    <p>Fill in your contact information here. This information will be used to validate your new account, as well as optionally make them available to other logged in Marketplace visitors. Fields marked with (*) are required for registration. These details remain private by default. </p>
                    <label for="email">E-mail address *</label>
                    <input type="email" name="email" placeholder="e.x. johndoe@example.org" required />
                    <label for="phone">Phone number</label>
                    <input type="tel" name="phone" placeholder="e.x. +30 6999123456" />
                </fieldset>
                <div class="error"></div>
                <button type="submit" class="action ">Create account</button>
                <p>By submitting this form, you agree to our <a href="<?php echo $tos_url ?>">Terms of Service</a>.
                    Already have an account? Please <a href="<?php echo $authentication_url ?>">Log in</a>.</p>
            </form>
        </div>
    <?php
    }
}




/**
 * Print the account authorization form.
 *
 * @param   string $registration_url The url that redirects to the registration page.
 * @param   bool $logged_in Whether the viewer is already logged in.
 * @param   string $error_message Any potential error message to be displayed.
 *
 * @since    1.0.0
 */
function account_user_authentication_html($registration_url, $reset_password_page, $authenticated)
{
    if (!$authenticated) {
    ?>
        <div class="policycloud-marketplace">
            <form id="policycloud-authentication">
                <fieldset name=" account-credentials">
                    <h2>Insert your credentials</h2>
                    <p>The following information is required to log you in.</p>
                    <label for="username">Username or E-mail address *</label>
                    <input required name="username-email" placeholder="e.x. johndoe / johndoe@example.org" type="text" />
                    <label for="password">Password *</label>
                    <input required name="password" placeholder="Insert your password" type="password" />
                </fieldset>
                <div class="error"></div>
                <button type="submit" class="action">Log in</button>
                <p>Don't have an account yet? You can <a href="<?php echo $registration_url ?>">register</a> now to obtain full access to the Marketplace. If you have forgotten your credentials, you can <a href="<?php echo $reset_password_page ?>">reset your password.</a></p>
            </form>
        </div>
    <?php
    } else {
        show_alert("You're already logged in.", 'notice');
    }
}

/**
 * Print the password reset form.
 *
 * @param   string $registration_url The url that redirects to the registration page.
 * @param   bool $logged_in Whether the viewer is already logged in.
 * @param   string $error_message Any potential error message to be displayed.
 *
 * @since    1.0.0
 */
function account_user_reset_password_html($authenticated)
{
    if (!$authenticated) {
    ?>
        <div class="policycloud-marketplace">
            <form id="policycloud-marketplace-password-reset">
                <fieldset>
                    <h2>Reset your password</h2>
                    <p>Insert your e-mail address below and we will contact you with instructions to reset your password.</p>
                    <label for="username">E-mail address *</label>
                    <input required name="email" placeholder="e.x. johndoe@example.org" type="email" />
                    <button type="submit" class="action">Reset password</button>
                </fieldset>
            </form>
        </div>
    <?php
    } else {
        show_alert("You're already logged in.", 'notice');
    }
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
 * @usedby  PolicyCloud_Marketplace_Public::account_shortcode()
 *
 * @since   1.0.0
 * @author  Alexandros Raikos <araikos@unipi.gr>
 */
function account_user_html(array $data, bool $admin, bool $visitor, array $pages)
{
    // Show account verification notice.
    if (!empty($data['metadata']['verified'])) {
        if ($data['metadata']['verified'] !== '1') {
            show_alert('Your account is still unverified, please check your email inbox or spam folder for a verification email. You can <a id="policycloud-marketplace-resend-verification-email">resend</a> it if you can\'t find it.', 'notice');
        }
    } else {
        show_alert("Your account verification status couldn't be accessed.");
    }

    $descriptions_count = array_sum(array_map(function ($page) {
        return count($page);
    }, $data['descriptions'] ?? []));
    $reviews_count = array_sum(array_map(function ($page) {
        return count($page);
    }, $data['reviews'] ?? []));
    $approvals_count = array_sum(array_map(function ($page) {
        return count($page);
    }, $data['approvals'] ?? []));

    ?>
    <div id="policycloud-marketplace-account" class="policycloud-marketplace">
        <div id="policycloud-marketplace-account-sidebar">
            <?php
            echo '<img src="' . $data['picture'] . '" draggable="false" />';
            ?>
            <nav>
                <button class="tactile" id="policycloud-marketplace-account-overview" class="active">Overview</button>
                <button class="tactile" id="policycloud-marketplace-account-descriptions">Descriptions <span class="pill"><?php echo $descriptions_count ?></span></button>
                <button class="tactile" id="policycloud-marketplace-account-reviews">Reviews <span class="pill"><?php echo $reviews_count ?></span></button>
                <?php
                if (!$visitor && $admin) {
                ?>
                    <hr />
                    <button class="tactile" id="policycloud-marketplace-account-approvals">Approvals <span class="pill"><?php echo $approvals_count ?></span></button>
                    <hr />
                <?php
                }
                ?>
                <button class="tactile" id="policycloud-marketplace-account-profile">Profile</button>
                <?php if (!$visitor) { ?>
                    <button class="tactile policycloud-logout">Log out</button>
                <?php } ?>
            </nav>
        </div>
        <div id="policycloud-marketplace-account-content">
            <div class="policycloud-marketplace-account-title">
                <h2>
                    <?php
                    echo ((($data['information']['title'] ?? '-') == '-') ? '' : $data['information']['title'] ) . ' ' . $data['information']['name'] . ' ' . $data['information']['surname'];
                    ?>
                </h2>
                <div>
                    <?php
                    echo ($data['information']['organization'] ?? '');
                    ?>
                </div>
            </div>
            <div>
                <section class="policycloud-marketplace-account-overview focused">
                    <header>
                        <h3>Overview</h3>
                    </header>
                    <div>
                        <h4>About</h4>
                        <p>
                            <?php echo $data['information']['about'] ?? '' ?>
                        </p>
                        <?php
                        if (!empty($data['information']['social'][0])) {
                        ?>
                            <ul>
                                <?php
                                foreach ($data['information']['social'] as $link) {
                                    echo '<li><a href="' . explode(':', $link, 2)[1] . '" target="blank">' . explode(':', $link, 2)[0] . '</a></li>';
                                }
                                ?>
                            </ul>
                        <?php } ?>
                    </div>
                    <?php if (!empty($data['statistics'])) { ?>
                        <h4>Statistics</h4>
                        <table class="statistics">
                            <tr>
                                <td>
                                    <div class="large-figure"><span class="fas fa-list"></span> <?php echo $data['statistics']['total_descriptions'] ?></div>
                                    <div class="assets-caption">Total descriptions</div>
                                </td>
                                <td>
                                    <div class="large-figure"><span class="fas fa-check"></span> <?php echo $data['statistics']['approved_descriptions'] ?></div>
                                    <div class="assets-caption">Approved descriptions</div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="large-figure"><span class="fas fa-download"></span> <?php echo $data['statistics']['total_downloads'] ?></div>
                                    <div class="assets-caption">Total downloads</div>
                                </td>
                                <td>
                                    <div class="large-figure"><span class="fas fa-file"></span> <?php echo $data['statistics']['assets_uploaded'] ?></div>
                                    <div>Assets uploaded</div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="large-figure"><span class="fas fa-comment"></span> <?php echo $data['statistics']['total_reviews'] ?></div>
                                    <div class="assets-caption">Total reviews</div>
                                </td>
                                <td>
                                    <div class="large-figure"><span class="fas fa-star"></span> <?php echo $data['statistics']['average_rating'] ?></div>
                                    <div class="assets-caption">Average rating</div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="large-figure"><span class="fas fa-eye"></span> <?php echo $data['statistics']['total_views'] ?></div>
                                    <div class="assets-caption">Total views</div>
                                </td>
                            </tr>
                        </table>
                    <?php
                    } else {
                        show_alert("Statistics for this user are currently unavailable.", 'notice');
                    }
                    ?>
                </section>
                <section class="policycloud-marketplace-account-descriptions">
                    <?php
                    entity_list_html('descriptions', $data['descriptions'], $visitor, function ($description) use ($pages) {
                    ?>
                        <li data-type-filter="<?php echo $description->type ?>" data-date-updated="<?php echo strtotime($description->metadata['uploadDate']) ?>" data-rating="<?php echo $description->metadata['reviews']['average_rating'] ?>" data-total-views="<?php echo $description->metadata['views'] ?>" class="visible">
                            <div class="description">
                                <a href="<?php echo $pages['description_page'] . "?did=" . $description->id ?>">
                                    <h4><?php echo $description->information['title'] ?></h4>
                                </a>
                                <p><?php echo $description->information['short_desc'] ?></p>
                                <div class="metadata">
                                    <a class="pill"><?php echo $description->type  ?></a>
                                    <a class="pill"><?php echo $description->information['subtype']  ?></a>
                                    <span><span class="fas fa-star"></span> <?php echo $description->metadata['reviews']['average_rating'] . ' (' . $description->metadata['reviews']['no_reviews'] . ' reviews)' ?></span>
                                    <span><span class="fas fa-eye"></span> <?php echo $description->metadata['views'] ?> views</span>
                                    <span>Last updated <?php echo time_elapsed_string(date('Y-m-d H:i:s', strtotime($description->metadata['uploadDate']))) ?></span>
                                    <span class="label <?php echo ($description->metadata['approved'] == 1) ? 'success' : 'notice' ?>"><?php echo ($description->metadata['approved'] == 1) ? 'Approved' : 'Pending' ?></span>
                                </div>
                            </div>
                        </li>
                    <?php
                    }, $pages['upload_page']);
                    ?>
                </section>
                <section class="policycloud-marketplace-account-reviews">
                    <?php
                    entity_list_html('reviews', $data['reviews'], $visitor, function ($review) use ($pages) {
                    ?>
                        <li data-type-filter="<?php echo $review->collection ?>" data-date-updated="<?php echo strtotime($review->update_date) ?>" data-rating="<?php echo $review->rating ?>" class="visible">
                            <div class="review">
                                <div class="rating">
                                    <span><span class="fas fa-star"></span> <?php echo $review->rating ?></span>
                                    <span>Posted <?php echo time_elapsed_string(date('Y-m-d H:i:s', strtotime($review->update_date))) ?></span>
                                </div>
                                <p>"<?php echo $review->comment ?>"</p>
                                <a href="<?php echo $pages['description_page'] . "?did=" . $review->description_id ?>">
                                    <h4><?php echo $review->title ?></h4>
                                </a>
                                <div class="metadata">
                                    <a class="pill"><?php echo $review->collection  ?></a>
                                </div>
                            </div>
                        </li>
                    <?php
                    });
                    ?>
                </section>
                <?php
                if (!empty($data['approvals'])) {
                ?>
                    <section class="policycloud-marketplace-account-approvals">
                        <?php
                        entity_list_html('approvals', $data['approvals'], $visitor, function ($pending_description) use ($pages) {
                        ?>
                            <li data-type-filter="<?php echo $pending_description->type ?>" data-date-updated="<?php echo strtotime($pending_description->metadata['uploadDate']) ?>" data-rating="<?php echo $pending_description->metadata['reviews']['average_rating'] ?>" data-total-views="<?php echo $pending_description->metadata['views'] ?>" class="visible">
                                <div class="description">
                                    <a href="<?php echo $pages['description_page'] . "?did=" . $pending_description->id ?>">
                                        <h4><?php echo $pending_description->information['title'] ?></h4>
                                    </a>
                                    <p><?php echo $pending_description->information['short_desc'] ?></p>
                                    <div class="metadata">
                                        <a class="pill"><?php echo $pending_description->type  ?></a>
                                        <a class="pill"><?php echo $pending_description->information['subtype']  ?></a>
                                        <span><span class="fas fa-star"></span> <?php echo $pending_description->metadata['reviews']['average_rating'] . ' (' . $pending_description->metadata['reviews']['no_reviews'] . ' reviews)' ?></span>
                                        <span><span class="fas fa-eye"></span> <?php echo $pending_description->metadata['views'] ?> views</span>
                                        <span>Last updated <?php echo time_elapsed_string(date('Y-m-d H:i:s', strtotime($pending_description->metadata['uploadDate']))) ?></span>
                                        <span class="label notice">Pending</span>
                                    </div>
                                </div>
                            </li>
                        <?php
                        });
                        ?>
                    </section>
                <?php
                }
                ?>
                <section class="policycloud-marketplace-account-profile">
                    <header>
                        <h3>Information</h3>
                        <?php
                        if (!$visitor || $admin) {
                        ?>
                            <button id="policycloud-marketplace-account-edit-toggle"><span class="fas fa-pen"></span> Edit</button>
                        <?php
                        }
                        ?>
                    </header>
                    <form id="policycloud-marketplace-account-edit" accept-charset="utf8" action="">
                        <table class="information">
                            <?php
                            if ($admin || !$visitor) {
                            ?>
                                <tr>
                                    <td class="folding">
                                        <span>Profile picture</span>
                                    </td>
                                    <td class="folding">
                                        <?php
                                        if (!empty($data['picture'])) {
                                        ?>
                                            <div class="file-editor" data-name="profile-picture">
                                                <img class="file" src="<?php echo $data['picture'] ?>" draggable="false" />
                                                <button type="button" class="delete"><span class="fas fa-times"></span></button>
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
                                    if (!$visitor || $admin) {
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
                                        if (!empty($data['information']['social'][0])) {
                                            foreach ($data['information']['social'] as $link) {
                                                echo '<a href="' . explode(':', $link, 2)[1] . '" target="blank">' . explode(':', $link, 2)[0] . '</a><br/>';
                                            }
                                        }
                                        ?>
                                    </span>
                                    <?php
                                    if (!$visitor || $admin) {
                                    ?>
                                        <div class="socials folding">
                                            <div>
                                                <?php

                                                if (!empty($data['information']['social'][0])) {
                                                    foreach ($data['information']['social'] as $link) {
                                                        $link_title = explode(':', $link, 2)[0];
                                                        $link_url = explode(':', $link, 2)[1];
                                                ?>
                                                        <div>
                                                            <input type="text" name="socials-title[]" placeholder="Example" value="<?php echo $link_title ?>" />
                                                            <input type="url" name="socials-url[]" placeholder="https://www.example.org/" value="<?php echo $link_url ?>" />
                                                            <button class="remove-field" title="Remove this link." <?php if (count($data['information']['social']) == 1) {
                                                                                                                        echo 'disabled';
                                                                                                                    } ?>><span class="fas fa-times"></span></button>
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
                                <td>
                                    Username
                                </td>
                                <td>
                                    <span>
                                        <?php echo $data['username']; ?>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <?php
                                if (!$visitor) {
                                ?>
                                    <td>
                                        Password
                                    </td>
                                    <td>
                                        <span class="folding visible">*****************</span>
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
                                        <?php echo ($data['metadata']['role'] == 'admin') ? 'Administrator' : 'User'; ?>
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
                                        echo ((($data['information']['title'] ?? '-') == '-') ? '' : $data['information']['title'] )  . ' ' . ($data['information']['name']) . ' ' . ($data['information']['surname']);
                                        ?>
                                    </span>
                                    <?php
                                    if (!$visitor || $admin) {
                                    ?>
                                        <select class="folding" name="title">
                                            <option value="Mr." <?php echo ($data['information']['title'] == 'Mr.' ? 'selected' : '') ?>>Mr.</option>
                                            <option value="Ms." <?php echo ($data['information']['title'] == 'Ms.' ? 'selected' : '') ?>>Ms.</option>
                                            <option value="Mrs." <?php echo ($data['information']['title'] == 'Mrs.' ? 'selected' : '') ?>>Mrs.</option>
                                            <option value="Dr." <?php echo ($data['information']['title'] == 'Dr.' ? 'selected' : '') ?>>Dr.</option>
                                            <option value="Prof." <?php echo ($data['information']['title'] == 'Prof.' ? 'selected' : '') ?>>Prof.</option>
                                            <option value="Sir" <?php echo ($data['information']['title'] == 'Sir' ? 'selected' : '') ?>>Sir</option>
                                            <option value="Miss" <?php echo ($data['information']['title'] == 'Miss' ? 'selected' : '') ?>>Miss</option>
                                            <option value="Mx." <?php echo ($data['information']['title'] == 'Mx.' ? 'selected' : '') ?>>Mx.</option>
                                            <option value="-" <?php echo ($data['information']['title'] == '-' ? 'selected' : '') ?>>None</option>
                                        </select>
                                        <input class="folding" type="text" name="name" placeholder="Name" value="<?php echo ($data['information']['name']); ?>" required />
                                        <input class="folding" type="text" name="surname" placeholder="Surname" value="<?php echo ($data['information']['surname']); ?>" required />
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
                                        echo (ucfirst($data['information']['gender'] ?? '-'));
                                        ?>
                                    </span>
                                    <?php
                                    if (!$visitor || $admin) {
                                    ?>
                                        <select name="gender" class="folding">
                                            <option value="male" <?php echo ($data['information']['gender'] == 'male' ? 'selected' : '') ?>>Male</option>
                                            <option value="female" <?php echo ($data['information']['gender'] == 'female' ? 'selected' : '') ?>>Female</option>
                                            <option value="transgender" <?php echo ($data['information']['gender'] == 'transgender' ? 'selected' : '') ?>>Transgender</option>
                                            <option value="genderqueer" <?php echo ($data['information']['gender'] == 'genderqueer' ? 'selected' : '') ?>>Genderqueer</option>
                                            <option value="questioning" <?php echo ($data['information']['gender'] == 'questioning' ? 'selected' : '') ?>>Questioning</option>
                                            <option value="-" <?php echo ($data['information']['gender'] == '-' ? 'selected' : '') ?>>Prefer not to say</option>
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
                                        echo ($data['information']['organization'] ?? '-');
                                        ?>
                                    </span>
                                    <?php
                                    if (!$visitor || $admin) {
                                    ?>
                                        <input class="folding" type="text" name="organization" value="<?php echo ($data['information']['organization'] ?? ''); ?>" placeholder="Insert your organization here" />
                                    <?php
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php
                            if (!empty($data['information']['email']) || !$visitor) {
                            ?>
                                <tr>
                                    <td>
                                        E-mail
                                    </td>
                                    <td>
                                        <span class="folding visible">
                                            <?php
                                            echo ($data['information']['email']);
                                            if ($data['metadata']['verified'] != '1') {
                                            ?>
                                                <span class="unverified">(Unverified)</span>
                                                <button id="policycloud-marketplace-resend-verification-email">Resend verification email</button>
                                            <?php
                                            } else {
                                                if (!$visitor || $admin) {
                                                    echo ' <span class="label ' . (($data['preferences']['public_email'] == 0) ? 'notice' : 'success') . '">' . (($data['preferences']['public_email'] == 0) ? 'Private' : 'Public') . '</span>';
                                                }
                                            }
                                            ?>
                                        </span>
                                        <?php
                                        if (!$visitor || $admin) {
                                        ?>
                                            <label for="email" class="folding">Changing this setting will require a verification of the new e-mail address.</label>
                                            <input class="folding" type="email" name="email" value="<?php echo $data['information']['email'] ?>" required />
                                            <select name="public-email" class="folding">
                                                <option value="1" <?php echo ($data['preferences']['public_email'] == 1 ? 'selected' : '') ?>>Public</option>
                                                <option value="0" <?php echo ($data['preferences']['public_email'] == 0 ? 'selected' : '') ?>>Private</option>
                                            </select>
                                        <?php
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php
                            }
                            if (!empty($data['information']['phone']) || !$visitor) {
                            ?>
                                <tr>
                                    <td>
                                        Phone number
                                    </td>
                                    <td>
                                        <span class="folding visible">
                                            <?php
                                            if (!empty($data['information']['phone'])) {
                                                echo ($data['information']['phone']);
                                                if (!$visitor || $admin) {
                                                    echo ' <span class="label ' . (($data['preferences']['public_phone'] == 0) ? 'notice' : 'success') . '">' . (($data['preferences']['public_phone'] == 0) ? 'Private' : ' Public') . '</span>';
                                                }
                                            } else {
                                                echo '-';
                                            }
                                            ?>
                                        </span>
                                        <?php
                                        if (!$visitor || $admin) {
                                        ?>
                                            <input class="folding" type="text" name="phone" value="<?php echo (empty($data['information']['phone']) ? '' : $data['information']['phone']); ?>" placeholder="Insert your phone number here" />
                                            <select name="public-phone" class="folding">
                                                <option value="1" <?php echo ($data['preferences']['public_phone'] == 1 ? 'selected' : '') ?>>Public</option>
                                                <option value="0" <?php echo ($data['preferences']['public_phone'] == 0 ? 'selected' : '') ?>>Private</option>
                                            </select>
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
                                    echo date('d/m/y', strtotime($data['metadata']['registration_datetime']))
                                    ?>
                                </td>
                            </tr>
                        </table>
                        <?php
                        if (!$visitor || $admin) {
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
                    if (!$visitor) {
                    ?>
                        <button id="policycloud-marketplace-request-data-copy" class="action">Request data copy</button>
                    <?php
                    }
                    if (!$visitor || $admin) {
                    ?>
                        <form id="policycloud-marketplace-delete-account">
                            <div>
                                <label for="current-password">Please type your current password to continue.</label>
                                <input name="current-password" type="password" placeholder="Insert your current password here">
                            </div>
                            <button type="submit" class="action destructive">Delete account</button>
                        </form>
                    <?php } ?>
                </section>
            </div>
        </div>
    </div>
    <?php
}


/**
 *
 * Print the assets grid HTML.
 *
 * @param   array $assets The PolicyCloud Marketplace API assets.
 * @param   string $asset_url The asset page URL.
 *
 * @since   1.0.0
 * @author  Alexandros Raikos <araikos@unipi.gr>
 * @author  Eleftheria Kouremenou <elkour@unipi.gr>
 */
function descriptions_grid_html(array $descriptions, string $description_url)
{
    if (empty($descriptions)) {
        echo show_alert('No assets found.', 'notice');
    } else {
        echo '<div class="policycloud-marketplace descriptions-grid">';
        echo '<ul>';
        foreach ($descriptions as $description) {
    ?>
            <li>
                <a href="<?php echo $description_url . '?did=' . $description->id ?>">
                    <div class="cover">
                        <img src="<?php echo get_site_url('', '/wp-content/plugins/policycloud-marketplace/public/assets/svg/marketplace.svg') ?>" alt="" />
                        <div class="content">
                            <h4><?php echo $description->information['title'] ?></h4>
                            <p><?php echo $description->information['short_desc'] ?></p>
                        </div>
                    </div>
                    <div class="metadata">
                        <div>
                            <div class="owner"><?php echo $description->metadata['provider'] ?></div>
                            <div class="last-updated">Updated <?php echo time_elapsed_string(date('Y-m-d H:i:s', strtotime($description->metadata['uploadDate']))) ?></div>
                        </div>
                        <div>
                            <span class="reviews"><span class="fas fa-star"></span> <?php echo $description->metadata['reviews']['average_rating'] . ' (' . $description->metadata['reviews']['no_reviews'] . ' reviews)' ?></span>
                            <span class="views"><span class="fas fa-eye"></span> <?php echo $description->metadata['views'] ?> views</span>
                        </div>
                        <div>
                            <span class="type pill"><?php echo $description->type  ?></span>
                            <?php
                            if (!empty($description->information['subtype'])) {
                            ?>
                                <span class="sub-type pill"><?php echo $description->information['subtype']  ?></span>
                            <?php } ?>
                        </div>
                    </div>
                </a>
            </li>
    <?php
        }
        echo '</ul>';
        echo '</div>';
    }
}

function featured_descriptions_html(array $categories, string $description_page): void
{
    ?>
    <div class="policycloud-marketplace featured-descriptions">
        <div class="row statistics">
            <div class="column">
                <figure>
                    <?php echo $categories['statistics']['sum'] ?>
                    <figcaption>Total descriptions</figcaption>
                </figure>
            </div>
            <div class="column">
                <figure>
                    <?php echo $categories['statistics']['top'][0]['descriptions'] ?>
                    <figcaption>
                        <?php echo ucfirst($categories['statistics']['top'][0]['collection']) ?>
                    </figcaption>
                </figure>
            </div>
            <div class="column">
                <figure>
                    <?php echo $categories['statistics']['top'][1]['descriptions'] ?>
                    <figcaption>
                        <?php echo ucfirst($categories['statistics']['top'][1]['collection']) ?>
                    </figcaption>
                </figure>
            </div>
            <div class="column">
                <figure>
                    <?php echo $categories['statistics']['top'][2]['descriptions'] ?>
                    <figcaption>
                        <?php echo ucfirst($categories['statistics']['top'][2]['collection']) ?>
                    </figcaption>
                </figure>
            </div>
        </div>
        <h2>Top rated descriptions</h2>
        <?php
        descriptions_grid_html($categories['top_rated'], $description_page);
        ?>
        <h2>Most viewed descriptions</h2>
        <?php
        descriptions_grid_html($categories['most_viewed'], $description_page);
        ?>
        <h2>Latest descriptions</h2>
        <?php
        descriptions_grid_html($categories['latest'], $description_page);
        ?>
        <h2>Suggestions</h2>
        <?php
        descriptions_grid_html($categories['suggestions'], $description_page);
        ?>
    </div>
    <?php
}


/**
 * Print the assets archive HTML.
 *
 * @param   array $assets The PolicyCloud Marketplace API assets.
 * @param   array $args Various printing arguments.
 *
 * @since   1.0.0
 * @author  Alexandros Raikos <araikos@unipi.gr>
 * @author  Eleftheria Kouremenou <elkour@unipi.gr>
 */
function descriptions_archive_html(array $descriptions, array $filters, string $description_page)
{
    ?>
    <div class="policycloud-marketplace inspect" id="policycloud-marketplace-description-archive">
        <div class="filters">
            <button class="close outlined filters-toggle">Close</button>
            <h2>Filters</h2>
            <p>Select the options below to narrow your search.</p>
            <form>
                <fieldset>
                    <input type="text" name="search" placeholder="Search assets" value="<?php echo $_GET['search'] ?? '' ?>" />
                </fieldset>
                <fieldset>
                    <h3>Types</h3>
                    <div class="types">
                        <span>
                            <input type="radio" name="type" value="" <?php echo (empty($_GET['type'])) ? 'checked' : '' ?> />
                            <label for="type">All</label>
                        </span>
                        <span>
                            <input type="radio" name="type" value="algorithms" <?php echo (($_GET['type'] ?? '') == 'algorithms') ? 'checked' : '' ?> />
                            <label for="type">Algorithms</label>
                        </span>
                        <span>
                            <input type="radio" name="type" value="tools" <?php echo (($_GET['type'] ?? '') == 'tools') ? 'checked' : '' ?> />
                            <label for="type">Tools</label>
                        </span>
                        <span>
                            <input type="radio" name="type" value="policies" <?php echo (($_GET['type'] ?? '') == 'policies') ? 'checked' : '' ?> />
                            <label for="type">Policies</label>
                        </span>
                        <span>
                            <input type="radio" name="type" value="datasets" <?php echo (($_GET['type'] ?? '') == 'datasets') ? 'checked' : '' ?> />
                            <label for="type">Datasets</label>
                        </span>
                        <span>
                            <input type="radio" name="type" value="webinars" <?php echo (($_GET['type'] ?? '') == 'webinars') ? 'checked' : '' ?> />
                            <label for="type">Webinars</label>
                        </span>
                        <span>
                            <input type="radio" name="type" value="tutorials" <?php echo (($_GET['type'] ?? '') == 'tutorials') ? 'checked' : '' ?> />
                            <label for="type">Tutorials</label>
                        </span>
                        <span>
                            <input type="radio" name="type" value="documents" <?php echo (($_GET['type'] ?? '') == 'documents') ? 'checked' : '' ?> />
                            <label for="type">Documents</label>
                        </span>
                        <span>
                            <input type="radio" name="type" value="externals" <?php echo (($_GET['type'] ?? '') == 'externals') ? 'checked' : '' ?> />
                            <label for="type">Externals</label>
                        </span>
                        <span>
                            <input type="radio" name="type" value="other" <?php echo (($_GET['type'] ?? '') == 'other') ? 'checked' : '' ?> />
                            <label for="type">Other</label>
                        </span>
                    </div>
                </fieldset>
                <fieldset>
                    <h3>Provider</h3>
                    <div class="providers">
                        <?php
                        foreach ($filters['providers'] as $provider) {
                            echo '<span><input type="checkbox" name="provider[]" value="' . $provider . '"/ ' . ((in_array($provider, $_GET['provider'] ?? [])) ? 'checked' : '') . '><label for="provider[]">' . $provider . '</label></span>';
                        }
                        ?>
                    </div>
                </fieldset>
                <fieldset>
                    <h3>Views</h3>
                    <div class="views">
                        <div>
                            <input type="number" name="views-gte" placeholder="0" value="<?php echo $_GET['views-gte'] ?? '' ?>" min="0" />
                        </div>
                        <div>
                            <input type="number" name="views-lte" placeholder="<?php echo $filters['max_views'] ?>" value="<?php echo $_GET['views-lte'] ?? "" ?>" max="<?php echo $filters['max_views'] ?>" />
                        </div>
                    </div>
                </fieldset>
                <fieldset>
                    <h3>Date</h3>
                    <div class="dates">
                        <div>
                            <label for="update-date-gte">From</label>
                            <input type="date" onfocus="(this.type='date')" name="update-date-gte" placeholder="<?php echo date("Y-m-d", strtotime($filters['oldest'])) ?>" value="<?php echo $_GET['update-date-gte'] ?? '' ?>" min="<?php echo date("Y-m-d", strtotime($filters['oldest'])) ?>" max="<?php echo date("Y-m-d") ?>" />
                        </div>
                        <div>
                            <label for="update-date-lte">To</label>
                            <input type="date" name="update-date-lte" placeholder="<?php echo date("Y-m-d") ?>" value="<?php echo $_GET['update-date-lte'] ?? '' ?>" min="<?php echo date("Y-m-d", strtotime($filters['oldest'])) ?>" max="<?php echo date("Y-m-d") ?>" />
                        </div>
                    </div>
                </fieldset>
                <button type="submit" class="action">Apply filters</button>
            </form>
        </div>
        <div class="content">
            <header>
                <button class="filters-toggle tactile">
                    <div></div>
                    <div></div>
                    <div></div>
                </button>
                <form action="" class="sorting-selector">
                    <fieldset>
                        <label for="sort-by">Sort by</label>
                        <select name="sort-by">
                            <option value="newest" <?php echo (((($_GET['sort-by'] ?? '') == 'newest') || empty($_GET['sort-by'])) ? "selected" : "") ?>>Newest</option>
                            <option value="oldest" <?php echo ((($_GET['sort-by'] ?? '') == 'oldest') ? "selected" : "") ?>>Oldest</option>
                            <option value="rating-desc" <?php echo ((($_GET['sort-by'] ?? '') == 'rating-desc') ? "selected" : "") ?>>Highest rated</option>
                            <option value="rating-asc" <?php echo ((($_GET['sort-by'] ?? '') == 'rating-asc') ? "selected" : "") ?>>Lowest rated</option>
                            <option value="views-desc" <?php echo ((($_GET['sort-by'] ?? '') == 'views-desc') ? "selected" : "") ?>>Most viewed</option>
                            <option value="views-asc" <?php echo ((($_GET['sort-by'] ?? '') == 'views-asc') ? "selected" : "") ?>>Least viewed</option>
                            <option value="title" <?php echo ((($_GET['sort-by'] ?? '') == 'title') ? "selected" : "") ?>>Title</option>
                        </select>
                    </fieldset>
                    <fieldset>
                        <label for="items-per-page">Items per page</label>
                        <select name="items-per-page">
                            <option value="10" <?php echo (((($_GET['items-per-page'] ?? '') == 10) || empty($_GET['sort-by'])) ? "selected" : "") ?>>10</option>
                            <option value="25" <?php echo ((($_GET['items-per-page'] ?? '') == '25') ? "selected" : "") ?>>25</option>
                            <option value="50" <?php echo ((($_GET['items-per-page'] ?? '') == '50') ? "selected" : "") ?>>50</option>
                            <option value="100" <?php echo ((($_GET['items-per-page'] ?? '') == '100') ? "selected" : "") ?>>100</option>
                        </select>
                    </fieldset>
                </form>
            </header>
            <?php
            if (!empty($descriptions)) {
            ?>
                <div class="gallery">
                    <?php
                    descriptions_grid_html($descriptions['content'], $description_page);
                    ?>
                </div>
                <nav class="pagination">
                    <?php
                    for ($page = 1; $page < $descriptions['pages'] + 1; $page++) {
                        $activePage = $_GET['descriptions-page'] ?? 1;
                        echo '<button class="page-selector ' . (($activePage == ($page)) ? 'active' : '') . '" data-page-number="' . $page . '">' . ($page) . '</button>';
                    }
                    ?>
                </nav>
            <?php
            } else {
                show_alert('No descriptions found.', 'notice');
            }
            ?>
        </div>
    </div>
    <?php
}




/**
 * Print the asset HTML.
 *
 * @param   array $asset The PolicyCloud Marketplace API asset.
 * @param   array $args Various printing arguments.
 *
 * @since   1.0.0
 * @author  Alexandros Raikos <araikos@unipi.gr>
 * @author  Eleftheria Kouremenou <elkour@unipi.gr>
 */
function description_html($description, $image_blobs, $pages, $permissions)
{
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
        echo '<div class="lock"><img src="' . get_site_url('', '/wp-content/plugins/policycloud-marketplace/public/assets/img/lock.svg') . '" /><p>Please <a href="' . $login_page . '">log in</a> to ' . $message . '.</p></div>';
    }

    /**
     * Print the file viewer table.
     *
     * @param   string $title The title of the collection.
     * @param   string $id The id of the collection.
     * @param   array $files A collection of files.
     * @param   bool $collapsed Whether the view is collapsed by default.
     *
     * @since   1.0.0
     * @author  Alexandros Raikos <araikos@unipi.gr>
     * @author  Eleftheria Kouremenou <elkour@unipi.gr>
     */
    function asset_viewer(string $title, string $category, array $assets, bool $collapsed = false): void
    {
    ?>
        <div class="policycloud-marketplace file-viewer <?php echo ($collapsed) ? 'collapsed' : '' ?>">
            <button data-files-category="<?php echo $category ?>" class="action"><?php echo $title ?></button>
            <table>
                <tr>
                    <th>Name</th>
                    <th>Version</th>
                    <th>Size</th>
                    <th>Added</th>
                </tr>
                <?php
                if (!empty($assets)) {
                    foreach ($assets as $asset) {
                ?>
                        <tr data-file-id="<?php echo $asset->id ?>">
                            <td><a class="download" data-file-id="<?php echo $asset->id ?>" data-type="<?php echo $category ?>"><?php echo $asset->filename ?></a></td>
                            <td><?php echo $asset->version ?></td>
                            <td><?php echo $asset->size ?></td>
                            <td><?php echo time_elapsed_string(date('Y-m-d H:i:s', strtotime($asset->update_date))) ?></td>
                        </tr>
                <?php
                    }
                } else {
                    echo '<tr><td colspan="4">';
                    show_alert("No " . $category . " found.", 'notice');
                    echo '</td></tr>';
                }
                ?>
            </table>
        </div>
    <?php
    }

    function description_editing_form_html(PolicyCloud_Marketplace_Description $description)
    {
    ?>
        <div id="policycloud-marketplace-description-editing" class="policycloud-marketplace modal-content">
            <form action="">
                <fieldset name="basic-information">
                    <h2>Basic information</h2>
                    <p>To create a new Marketplace asset, the following fields represent basic information that will be visible to others.</p>
                    <label for="title">Title *</label>
                    <input required name="title" placeholder="Insert a title" type="text" value="<?php echo $description->information['title'] ?>" />
                    <label for="type">Primary collection type *</label>
                    <select name="type" required>
                        <option value="algorithms" <?php echo ($description->type == "algorithms") ? 'selected' : '' ?>>Algorithms</option>
                        <option value="tools" <?php echo ($description->type == "tools") ? 'selected' : '' ?>>Tools</option>
                        <option value="policies" <?php echo ($description->type == "policies") ? 'selected' : '' ?>>Policies</option>
                        <option value="datasets" <?php echo ($description->type == "datasets") ? 'selected' : '' ?>>Datasets</option>
                        <option value="webinars" <?php echo ($description->type == "webinars") ? 'selected' : '' ?>>Webinars</option>
                        <option value="tutorials" <?php echo ($description->type == "tutorials") ? 'selected' : '' ?>>Tutorials</option>
                        <option value="documents" <?php echo ($description->type == "documents") ? 'selected' : '' ?>>Documents</option>
                        <option value="externals" <?php echo ($description->type == "externals") ? 'selected' : '' ?>>Externals</option>
                        <option value="other" <?php echo ($description->type == "other") ? 'selected' : '' ?>>Other</option>
                    </select>
                    <label for="subtype">Secondary collection type</label>
                    <input name="subtype" placeholder="Insert a secondary category" type="text" value="<?php echo empty($description->information['subtype']) ? '' : $description->information['subtype'] ?>" />
                    <label for="owner">Legal owner *</label>
                    <input required name="owner" placeholder="Insert the legal owner of the object" type="text" value="<?php echo empty($description->information['owner']) ? '' : $description->information['owner'] ?>" />
                    <label for="description">Description *</label>
                    <textarea name="description" placeholder="Insert a detailed description" style="resize:vertical"><?php echo empty($description->information['description']) ? '' : $description->information['description'] ?></textarea>
                    <label for="fields-of-use">Fields of usage</label>
                    <textarea name="fields-of-use" placeholder="Separate multiple fields of usage using a comma (lorem, ipsum, etc.)"><?php echo empty($description->information['fieldOfUse']) ? '' : implode(', ', $description->information['fieldOfUse']) ?></textarea>
                </fieldset>
                <fieldset name="internal-information">
                    <h2>Additional information</h2>
                    <p>You can include additional comments for authorized visitors. This field is optional.</p>
                    <label for="comments">Comments</label>
                    <textarea name="comments" placeholder="Insert any additional comments"><?php echo empty($description->information['comments']) ? '' : $description->information['comments'] ?></textarea>
                </fieldset>
                <fieldset name="uploads">
                    <h2>Uploads</h2>
                    <p>Manage your content and upload new files, images and videos.</p>
                    <h3>Files</h3>
                    <?php
                    if (!empty($description->assets['files'])) {
                        foreach ($description->assets['files'] as $asset) {
                    ?>
                            <div class="file" data-file-type="files" data-file-identifier="<?php echo $asset->id ?>">
                                <div>
                                    <button class="delete"><span class="fas fa-times"></span></button>
                                    <?php echo $asset->filename . ' (' . $asset->size . ')' ?>
                                </div>
                                <label for="file-<?php echo $asset->id ?>">Replace file (supported file types: jpg, png):</label>
                                <input type="file" name="file-<?php echo $asset->id ?>" multiple />
                            </div>
                    <?php
                        }
                    }
                    ?>
                    <label for="file">Upload new file (supported file types: jpg, png):</label>
                    <input type="file" name="files[]" multiple />
                    <h3>Images</h3>
                    <?php
                    if (!empty($description->assets['images'])) {
                        foreach ($description->assets['images'] as $asset) {
                    ?>
                            <div class="file" data-file-type="images" data-file-identifier="<?php echo $asset->id ?>">
                                <div>
                                    <button class="delete"><span class="fas fa-times"></span></button>
                                    <?php echo $asset->filename . ' (' . $asset->size . ')' ?>
                                </div>
                                <label for="image-<?php echo $asset->id ?>">Replace image</label>
                                <input type="file" name="image-<?php echo $asset->id ?>" accept="image/png, image/jpeg" />
                            </div>
                    <?php
                        }
                    }
                    ?>
                    <input type="file" name="images[]" accept="image/png, image/jpeg" multiple />
                    <h3>Videos</h3>
                    <?php
                    if (!empty($description->assets['videos'])) {
                        foreach ($description->assets['videos'] as $asset) {
                    ?>
                            <div class="file" data-file-type="videos" data-file-identifier="<?php echo $asset->id ?>">
                                <div>
                                    <button class="delete"><span class="fas fa-times"></span></button>
                                    <?php echo $asset->filename . ' (' . $asset->size . ')' ?>
                                </div>
                                <label for="video-<?php echo $asset->id ?>">Replace video</label>
                                <input type="file" name="video-<?php echo $asset->id ?>" accept="image/png, image/jpeg" multiple />
                            </div>
                    <?php
                        }
                    }
                    ?>
                    <input type="file" name="videos[]" accept="image/png, image/jpeg" multiple />
                </fieldset>
                <div class="error"></div>
                <div class="actions">
                    <button data-action="delete-description" class="action destructive">Delete</button>
                    <button type="submit" class="action">Submit</button>
                </div>
            </form>
        </div>
    <?php
    }

    if (!empty($description)) {
    ?>
        <div class="policycloud-marketplace" id="policycloud-marketplace-asset">
            <?php
            if ($permissions['administrator'] && $description->metadata['approved'] == "0") {
            ?>
                <div class="policycloud-marketplace-notice" id="policycloud-marketplace-description-approval">
                    <p>This asset is not yet accessible from other authorized Marketplace users.</p>
                    <button class="action productive" data-response="approve">Approve</button>
                    <button class="action destructive" data-response="disapprove">Delete</button>
                </div>
            <?php
            }
            ?>
            <header>
                <div class="title">
                    <h1><?php echo $description->information['title'] ?>
                        <?php
                        if ($permissions['provider'] || $permissions['administrator']) {
                        ?>
                            <span class="status label <?php echo ($description->metadata['approved'] == "1") ? 'success' : 'notice' ?>"><?php echo ($description->metadata['approved'] == "1") ? 'Approved' : 'Pending' ?></span>
                        <?php
                        }
                        ?>
                    </h1>
                    <?php
                    if ($permissions['provider'] || $permissions['administrator']) {
                        echo '<button class="outlined show-editor-modal"><span class="fas fa-pen"></span> Edit</button>';
                    }
                    ?>
                </div>
                <div class="metadata">
                    <span class="provider"><a href="<?php echo $pages['account_page'] . '?user=' . $description->metadata['provider'] ?>"><?php echo $description->metadata['provider'] ?></a></span>
                    <?php if (!empty($description->information['owner'])) { ?>
                        <span class="owner">&copy; <?php echo $description->information['owner'] ?></span>
                    <?php } ?>
                    <span class="type pill"><a href="<?php echo $pages['archive_page'] . "?type=" . $description->type ?>"><?php echo $description->type ?></a></span>
                    <?php if (!empty($description->information['subtype'])) {
                        if ($description->information['subtype'] != '-') { ?>
                            <span class="sub-type pill"><a href="<?php echo $pages['archive_page'] . "?subtype=" . $description->information['subtype'] ?>"><?php echo $description->information['subtype'] ?></a></span>
                        <?php }
                    }
                    if (!empty($description->information['fieldOfUse'])) { ?>
                        <span class="fields-of-use">
                            <?php
                            foreach ($description->information['fieldOfUse'] as $field_of_use) {
                                if (!empty($field_of_use)) {
                                    echo '<span>' . $field_of_use . '</span>';
                                }
                            }
                            ?>
                        </span>
                    <?php } ?>
                    <span class="reviews"><span class="fas fa-star"></span> <?php echo $description->metadata['reviews']['average_rating'] . ' (' . $description->metadata['reviews']['no_reviews'] . ' reviews)' ?></span>
                    <span class="views"><?php echo $description->metadata['views'] ?> views</span>
                    <span class="last-update-date">Last updated <?php echo time_elapsed_string(date('Y-m-d H:i:s', strtotime($description->metadata['uploadDate']))) ?></span>
                </div>
            </header>
            <div class="content">
                <div class="files <?php echo ($permissions['authenticated']) ? '' : 'locked' ?>">
                    <h2>Uploads</h2>
                    <?php
                    if ($permissions['authenticated']) {
                        asset_viewer('Files', 'files', $description->assets['files'], (empty($description->assets['files'])));
                        asset_viewer('Video', 'video', $description->assets['videos'], (empty($description->assets['videos'])));
                        asset_viewer('Images (Gallery)', 'images', $description->assets['images'], (empty($description->assets['images'])));
                    } else {
                        show_lock($pages['login_page'], 'view and download files');
                    }
                    if ($permissions['authenticated']) {
                    ?>
                        <div class="comments">
                            <h2>Additional information</h2>
                            <?php
                            if (!empty($description->information['comments'])) {
                                echo '<p>' . $description->information['comments'] . '</p>';
                            } else {
                                show_alert("No additional information.", 'notice');
                            }
                            ?>
                        </div>
                    <?php
                    }
                    ?>
                </div>
                <div class="information">
                    <h2>Description</h2>
                    <div class="description">
                        <p>
                            <?php
                            if ($permissions['authenticated']) {
                                echo $description->information['description'];
                            } else {
                                echo $description->information['short_desc'];
                            }
                            ?>
                        </p>
                    </div>
                    <div class="gallery <?php echo ($permissions['authenticated']) ? '' : 'locked' ?>">
                        <h2>Gallery</h2>
                        <div class="slider">
                            <?php
                            if ($permissions['authenticated']) {
                                if (!empty($image_blobs)) {
                                    foreach ($image_blobs as $key => $image_blob) {
                                        echo '<img src="data:image/*;base64,' . base64_encode($image_blob) . '" data-image-id="' . $description->assets['images'][$key]->id . '" draggable="false" />';
                                    }
                                } else {
                                    show_alert('No images or videos were found.', 'notice');
                                }
                            } else {
                                show_lock($pages['login_page'], 'view the image gallery');
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <?php
                if ($permissions['provider'] || $permissions['administrator']) {
                    description_editing_form_html($description);
                }
                ?>
            </div>
        <?php
    }
}


/**
 * Print the asset HTML.
 *
 * @param   array $asset The PolicyCloud Marketplace API asset.
 * @param   array $args Various printing arguments.
 *
 * @since   1.0.0
 * @author  Alexandros Raikos <araikos@unipi.gr>
 */
function description_creation_html()
{
    if (!empty($error)) {
        show_alert($error);
    } else {
        ?>
            <div class="policycloud-marketplace">
                <form id="policycloud-marketplace-description-creation" action="">
                    <fieldset name="basic-information">
                        <h2>Basic information</h2>
                        <p>To create a new Marketplace asset, the following fields represent basic information that will be visible to others.</p>
                        <label for="title">Title *</label>
                        <input required name="title" placeholder="Insert a title" type="text" />
                        <label for="type">Primary collection type *</label>
                        <select name="type" required>
                            <option value="algorithms" selected>Algorithms</option>
                            <option value="tools">Tools</option>
                            <option value="policies">Policies</option>
                            <option value="datasets">Datasets</option>
                            <option value="webinars">Webinars</option>
                            <option value="tutorials">Tutorials</option>
                            <option value="documents">Documents</option>
                            <option value="externals">Externals</option>
                            <option value="other">Other</option>
                        </select>
                        <label for="subtype">Secondary collection type</label>
                        <input type="text" placeholder="Insert a secondary collection type" name="subtype" />
                        <label for="fields-of-use">Fields of usage</label>
                        <textarea name="fields-of-use" placeholder="Separate multiple fields of usage using a comma (lorem, ipsum, etc.)"></textarea>
                        <label for="owner">Legal owner *</label>
                        <input required name="owner" placeholder="Insert the legal owner of the object" type="text" />
                        <label for="description">Description *</label>
                        <textarea name="description" placeholder="Insert a detailed description" style="resize:vertical"></textarea>
                    </fieldset>
                    <fieldset name="internal-information">
                        <h2>Additional information</h2>
                        <p>You can include additional comments for authorized visitors. This field is optional.</p>
                        <label for="comments">Comments</label>
                        <textarea name="comments" placeholder="Insert any additional comments"></textarea>
                    </fieldset>
                    <div class="error"></div>
                    <button type="submit" class="action ">Create</button>
                </form>
            </div>
        <?php
    }
}



/**
 * Display a list of assets with filtering, sorting and custom pagination.
 *
 * @param string $id The identifier for the viewer.
 * @param array $content The asset structure to be displayed.
 * @param callable $inner_html The callback that prints the list item HTML.
 * @param array $args The arguments of the parent function
 *
 * @since   1.0.0
 * @author  Alexandros Raikos <araikos@unipi.gr>
 *
 */
function entity_list_html(string $id, array $content, bool $visitor, callable $inner_html, ?string $create_page_url = null)
{
        ?>
        <header>
            <h3><?php echo ucfirst($id) ?></h3>
            <div class="actions">
                <form action="" class="selector">
                    <label for="sort-by">Sort by</label>
                    <select name="sort-by" data-category="<?php echo $id ?>">
                        <option value="newest" <?php echo ((($_GET['sort-by'] ?? '' == 'newest') || empty($_GET['sort-by'])) ? "selected" : "") ?>>Newest</option>
                        <option value="oldest" <?php echo (($_GET['sort-by'] ?? '' == 'oldest') ? "selected" : "") ?>>Oldest</option>
                        <option value="rating-asc" <?php echo (($_GET['sort-by'] ?? '' == 'rating-asc') ? "selected" : "") ?>>Highest rated</option>
                        <option value="rating-desc" <?php echo (($_GET['sort-by'] ?? '' == 'rating-desc') ? "selected" : "") ?>>Lowest rated</option>
                        <?php
                        if ($id == 'descriptions') {
                        ?>
                            <option value="views-asc" <?php echo (($_GET['sort-by'] ?? '' == 'views-asc') ? "selected" : "") ?>>Most viewed</option>
                            <option value="views-desc" <?php echo (($_GET['sort-by'] ?? '' == 'views-desc') ? "selected" : "") ?>>Least viewed</option>
                        <?php } ?>
                        <option value="title" <?php echo (($_GET['sort-by'] ?? '' == 'title') ? "selected" : "") ?>>Title</option>
                    </select>
                    <label for="items-per-page">Items per page</label>
                    <select name="items-per-page" data-category="<?php echo $id ?>">
                        <option value="5" <?php echo ((($_GET['items-per-page'] ?? '' == '5')) ? "selected" : "") ?>>5</option>
                        <option value="10" <?php echo (($_GET['items-per-page'] ?? '' == '10' || empty($_GET['items-per-page'])) ? "selected" : "") ?>>10</option>
                        <option value="25" <?php echo (($_GET['items-per-page'] ?? '' == '25') ? "selected" : "") ?>>25</option>
                        <option value="50" <?php echo (($_GET['items-per-page'] ?? '' == '50') ? "selected" : "") ?>>50</option>
                        <option value="100" <?php echo (($_GET['items-per-page'] ?? '' == '100') ? "selected" : "") ?>>100</option>
                    </select>
                </form>
                <?php
                if (!$visitor && $id == 'descriptions'  && !empty($create_page_url)) {
                ?>
                    <a id="policycloud-upload" href="<?php echo $create_page_url ?>" title="Create new"><span class="fas fa-plus"></span> Create new</a>
                <?php } ?>
            </div>
        </header>
        <div class="collection-filters" data-category="<?php echo $id ?>">
            <div>Filter by type:</div>
        </div>
        <div class="paginated-list" data-category="<?php echo $id ?>">
            <?php
            if (!empty($content)) {
                foreach ($content as $page => $page_items) {
                    echo '<ul data-page="' . ($page + 1) . '" class="page ' . $id . ' ' . ($page == 0 ? 'visible' : '') . '">';
                    if (!empty($content)) {
                        foreach ($page_items as $item) {
                            $inner_html($item);
                        }
                    } else {
                        show_alert("You don't have any " . $id . " yet.");
                    }
                    echo '</ul>';
                }
            } else {
                show_alert('This user does not have any ' . $id . '.', 'notice');
            } ?>
            <nav class="pagination">
                <?php
                if (count($content ?? []) > 1) {
                    foreach ($content as $page => $page_items) {
                        echo '<button data-category="' . $id . '" class="page-selector ' . (($page == ($_GET['page'] ?? 0)) ? 'active' : '') . '" data-' . $id . '-page="' . ($page + 1) . '">' . ($page + 1) . '</button>';
                    }
                } ?>
            </nav>
        </div>
    <?php
}
