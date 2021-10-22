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
 * Print the account registration form.
 * 
 * @param   string $authorization_url The url that redirects to the log in page.
 * @param   string $error_message Any potential error message to be displayed.
 *
 * @since    1.0.0
 */
function account_registration_html($authorization_url, $logged_in)
{
    if ($logged_in) {
        show_alert("You're already logged in.", false, 'notice');
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
                <p>Already have an account? Please <a href="<?php echo $authorization_url ?>">Log in</a>.</p>
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
function account_authorization_html($registration_url, $logged_in)
{
    if (!$logged_in) {
    ?>
        <div class="policycloud-marketplace">
            <form id="policycloud-authorization" action="">
                <fieldset name="account-credentials">
                    <h2>Insert your credentials</h2>
                    <p>The following information is required to log you in.</p>
                    <label for="username">Username or E-mail address *</label>
                    <input required name="username-email" placeholder="e.x. johndoe / johndoe@example.org" type="text" />
                    <label for="password">Password *</label>
                    <input required name="password" placeholder="Insert your password" type="password" />
                </fieldset>
                <div class="error"></div>
                <button type="submit" class="action">Log in</button>
                <p>Don't have an account yet? You can <a href="<?php echo $registration_url ?>">register</a> now to obtain full access to the Marketplace.</p>
            </form>
        </div>
        <?php
    } else {
        show_alert("You're already logged in.", false, 'notice');
    }
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
function assets_grid_html($assets, $asset_url)
{
    if (empty($asset_url)) {
        echo show_alert('No asset page has been defined in the WordPress settings.');
    }
    if (empty($assets)) {
        echo show_alert('No assets found.', false, 'notice');
    } else {
        echo '<div class="policycloud-marketplace" id="policycloud-marketplace-assets-grid">';
        echo '<ul>';
        foreach ($assets as $asset) {
        ?>
            <li>
                <a href="<?php echo $asset_url . '?did=' . $asset['id'] ?>">
                    <div class="cover">
                        <img src="<?php echo get_site_url('', '/wp-content/plugins/policycloud-marketplace/public/assets/img/placeholder.jpg') ?>" alt="" />
                        <div class="content">
                            <h4><?php echo $asset['info']['title'] ?></h4>
                            <p><?php echo $asset['info']['short_desc'] ?></p>
                        </div>
                    </div>
                    <div class="metadata">
                        <div>
                            <div class="owner"><?php echo $asset['metadata']['provider'] ?></div>
                            <div class="last-updated">Updated <?php echo time_elapsed_string(date('Y-m-d H:i:s', strtotime($asset['metadata']['uploadDate']))) ?></div>
                        </div>
                        <div>
                            <span class="reviews"><span class="fas fa-star"></span> <?php echo $asset['metadata']['reviews']['average_rating'] . ' (' . $asset['metadata']['reviews']['no_reviews'] . ' reviews)' ?></span>
                            <span class="views"><span class="fas fa-eye"></span> <?php echo $asset['metadata']['views'] ?> views</span>
                        </div>
                        <div>
                            <span class="type pill"><?php echo $asset['info']['type']  ?></span>
                            <?php
                            if (!empty($asset['info']['subtype'])) {
                            ?>
                                <span class="sub-type pill"><?php echo $asset['info']['subtype']  ?></span>
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
function assets_archive_html($assets, $args)
{

    // TODO @alexandrosraikos: CSS mockup alignment.
    if (!empty($args['error']))  echo show_alert($args['error']);
    if (!empty($args['notice'])) echo show_alert($args['notice'], false, 'notice');
    ?>
    <div class="policycloud-marketplace inspect" id="policycloud-marketplace-asset-archive">
        <div class="filters">
            <h2>Filters</h2>
            <p>Select the options below to narrow your search.</p>
            <form>
                <fieldset>
                    <input type="text" name="search" placeholder="Search assets" value="<?php echo $_GET['search'] ?? '' ?>" />
                </fieldset>
                <fieldset>
                    <h3>Types</h3>
                    <?php // TODO: @alexandrosraikos Add type checkbox buttons.
                    ?>
                </fieldset>
                <fieldset>
                    <h3>Provider</h3>
                    <?php // TODO: @alexandrosraikos Add owner checkboxes. (waiting on @vkoukos)
                    ?>
                </fieldset>
                <fieldset>
                    <h3>Views</h3>
                    <div class="views">
                        <div>
                            <input type="number" name="views_gte" value="<?php echo $_GET['views_gte'] ?? '0' ?>" />
                        </div>
                        <div>
                            <input type="number" name="views_lte" placeholder="" value="<?php echo $_GET['views_lte'] ?? '0' ?>" />
                            <?php // TODO: @alexandrosraikos Add max views. (waiting on @vkoukos)
                            ?>
                        </div>
                        <?php // TODO: Add visual range selector 
                        ?>
                    </div>
                </fieldset>
                <fieldset>
                    <h3>Date</h3>
                    <div class="dates">
                        <div>
                            <?php // TODO: @alexandrosraikos Add oldest date. (waiting on @vkoukos)
                            ?>
                            <label for="update_date_gte">From</label>
                            <input type="date" onfocus="(this.type='date')" name="update_date_gte" placeholder="0" value="<?php echo $_GET['update_date_gte'] ?? '0' ?>" />
                        </div>
                        <div>
                            <label for="update_date_lte">To</label>
                            <input type="date" name="update_date_lte" placeholder="" />
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
                <?php // TODO @alexandrosraikos: Enable sorting (waiting on @vkoukos)
                ?>
                <?php // TODO @alexandrosraikos: Add and enable page size.
                ?>
                <form action="" class="selector">
                    <label for="sort-by">Sort by</label>
                    <select name="sort-by">
                        <option value="newest" <?php echo ((($_GET['sort_by'] ?? '' == 'newest') || empty($_GET['sort_by'])) ? "selected" : "") ?>>Newest</option>
                        <option value="oldest" <?php echo (($_GET['sort_by'] ?? '' == 'oldest') ? "selected" : "") ?>>Oldest</option>
                        <option value="rating-asc" <?php echo (($_GET['sort_by'] ?? '' == 'rating-asc') ? "selected" : "") ?>>Highest rated</option>
                        <option value="rating-desc" <?php echo (($_GET['sort_by'] ?? '' == 'rating-desc') ? "selected" : "") ?>>Lowest rated</option>
                        <option value="views-asc" <?php echo (($_GET['sort_by'] ?? '' == 'views-asc') ? "selected" : "") ?>>Most viewed</option>
                        <option value="views-desc" <?php echo (($_GET['sort_by'] ?? '' == 'views-desc') ? "selected" : "") ?>>Least viewed</option>
                        <option value="title" <?php echo (($_GET['sort_by'] ?? '' == 'title') ? "selected" : "") ?>>Title</option>
                    </select>
                </form>
            </header>
            <div class="gallery">
                <?php
                if (empty($assets['results'])) {
                    echo show_alert('No assets found', false, 'notice');
                } else {
                    foreach ($assets['results'] as $page) {
                        assets_grid_html($page, $args['asset_url']);
                    }
                }
                ?>
            </div>
            <nav class="pagination">
                <?php
                if (!empty($assets['pages'])) {
                    for ($page = 0; $page < $assets['pages']; $page++) {
                        $activePage = $_GET['asset-page'] ?? 0;
                        echo '<button class="page-selector ' . (($activePage == ($page + 1)) ? 'active' : '') . '" data-page-number="' . $page + 1 . '">' . ($page + 1) . '</button>';
                    }
                }
                ?>
            </nav>
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
function asset_html($asset, $images, $args)
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
    function file_viewer($title, $id, $files, $collapsed = false)
    {
        if (empty($title)) {
            show_alert("Please initialise the file viewer.", false, 'notice');
        } else {
    ?>
            <div class="policycloud-marketplace file-viewer <?php echo ($collapsed) ? 'collapsed' : '' ?>">
                <button data-files-category="<?php echo $id ?>" class="action"><?php echo $title ?></button>
                <table>
                    <tr>
                        <th>Name</th>
                        <th>Version</th>
                        <th>Size</th>
                        <th>Added</th>
                    </tr>
                    <?php
                    if (!empty($files)) {
                        foreach ($files as $file) {
                    ?>
                            <tr>
                                <td><a href=""><?php echo $file['filename'] ?></a></td>
                                <td><?php echo $file['version'] ?></td>
                                <td><?php echo $file['size'] ?></td>
                                <td><?php echo time_elapsed_string(date('Y-m-d H:i:s', strtotime($file['updateDate']))) ?></td>
                            </tr>
                    <?php
                        }
                    } else {
                        echo '<tr><td colspan="4">';
                        show_alert("No " . $id . " found.", false, 'notice');
                        echo '</td></tr>';
                    }
                    ?>
                </table>
            </div>
        <?php
        }
    }

    if (!empty($args['error'])) {
        show_alert($args['error']);
    }
    if (empty($asset)) {
        show_alert("This asset was not found.");
    } else {
        ?>
        <div class="policycloud-marketplace" id="policycloud-marketplace-asset">
            <header>
                <div class="title">
                    <h1><?php echo $asset['info']['title'] ?>
                        <?php
                        if ($args['is_owner']) {
                        ?>
                            <span class="status label <?php echo ($asset['metadata']['approved'] == 1) ? 'success' : 'notice' ?>"><?php echo ($asset['metadata']['approved'] == 1) ? 'Approved' : 'Pending' ?></span>
                        <?php
                        }
                        ?>
                    </h1>
                    <?php
                    if ($args['is_owner']) {
                        echo '<button class="outlined show-editor-modal"><span class="fas fa-pen"></span> Edit</button>';
                    }
                    ?>
                </div>
                <div class="metadata">
                    <span class="provider"><a href="<?php echo $args['account_page'] . '?user=' . $asset['metadata']['provider'] ?>"><?php echo $asset['metadata']['provider'] ?></a></span>
                    <?php if (!empty($asset['info']['owner'])) { ?>
                        <span class="owner">&copy; <?php echo $asset['info']['owner'] ?></span>
                    <?php } ?>
                    <span class="type pill"><a href=""><?php echo $asset['info']['type'] ?></a></span>
                    <?php if (!empty($asset['info']['subtype'])) {
                        if ($asset['info']['subtype'] != '-') { ?>
                            <span class="sub-type pill"><a href=""><?php echo $asset['info']['subtype'] ?></a></span>
                        <?php }
                    }
                    if (!empty($asset['info']['fieldOfUse'])) { ?>
                        <span class="fields-of-use">
                            <?php
                            foreach ($asset['info']['fieldOfUse'] as $field_of_use) {
                                echo '<span>' . $field_of_use . '</span>';
                            }
                            ?>
                        </span>
                    <?php } ?>
                    <span class="reviews"><span class="fas fa-star"></span> <?php echo $asset['metadata']['reviews']['average_rating'] . ' (' . $asset['metadata']['reviews']['no_reviews'] . ' reviews)' ?></span>
                    <span class="views"><?php echo $asset['metadata']['views'] ?> views</span>
                    <span class="last-update-date">Last updated <?php echo time_elapsed_string(date('Y-m-d H:i:s', strtotime($asset['metadata']['uploadDate']))) ?></span>
                </div>
            </header>
            <div class="content">
                <div class="files <?php echo ($args['is_authenticated']) ? '' : 'locked' ?>">
                    <h2>Uploads</h2>
                    <?php
                    if ($args['is_authenticated']) {
                        file_viewer('Files', 'files', $asset['assets']['files'], (empty($asset['assets']['files'])));
                        file_viewer('Images', 'images', $asset['assets']['images'], (empty($asset['assets']['images'])));
                        file_viewer('Videos', 'videos', $asset['assets']['videos'], (empty($asset['assets']['videos'])));
                    } else show_lock($args['login_page'], 'view and download files');
                    if ($args['is_owner']) {
                    ?>
                        <div class="comments">
                            <h2>Private comments</h2>
                            <p><?php echo $asset['info']['comments'] ?></p>
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
                            if ($args['is_authenticated']) echo $asset['info']['description'];
                            else echo $asset['info']['short_desc'];
                            ?>
                        </p>
                    </div>
                    <div class="gallery <?php echo ($args['is_authenticated']) ? '' : 'locked' ?>">
                        <h2>Gallery</h2>
                        <div class="slider">
                            <?php if ($args['is_authenticated']) {
                                if (!empty($images)) {
                                    foreach ($images as $image) {
                                        echo '<img src="data:image/*;base64,' . base64_encode($image) . '" draggable="false" />';
                                    }
                                } else {
                                    show_alert('No images or videos were found.', false, 'notice');
                                }
                            } else show_lock($args['login_page'], 'view the image gallery');
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php 
            if ($args['is_owner']) {
            ?>
            <div class="modal editing-form hidden">
                <button class="close"><span class="fas fa-times"></span></button>
                <form id="policycloud-marketplace-asset-editing" action="">
                    <fieldset name="basic-information">
                        <h2>Basic information</h2>
                        <p>To create a new Marketplace asset, the following fields represent basic information that will be visible to others.</p>
                        <label for="title">Title *</label>
                        <input required name="title" placeholder="Insert a title" type="text" value="<?php echo $asset['info']['title'] ?>" />
                        <label for="type">Primary collection type *</label>
                        <select name="type" required>
                            <option value="algorithms" <?php echo ($asset['info']['type'] == "algorithms") ? 'selected' : '' ?>>Algorithms</option>
                            <option value="tools" <?php echo ($asset['info']['type'] == "tools") ? 'selected' : '' ?>>Tools</option>
                            <option value="policies" <?php echo ($asset['info']['type'] == "policies") ? 'selected' : '' ?>>Policies</option>
                            <option value="datasets" <?php echo ($asset['info']['type'] == "datasets") ? 'selected' : '' ?>>Datasets</option>
                            <option value="webinars" <?php echo ($asset['info']['type'] == "webinars") ? 'selected' : '' ?>>Webinars</option>
                            <option value="tutorials" <?php echo ($asset['info']['type'] == "tutorials") ? 'selected' : '' ?>>Tutorials</option>
                            <option value="documents" <?php echo ($asset['info']['type'] == "documents") ? 'selected' : '' ?>>Documents</option>
                            <option value="externals" <?php echo ($asset['info']['type'] == "externals") ? 'selected' : '' ?>>Externals</option>
                            <option value="other" <?php echo ($asset['info']['type'] == "other") ? 'selected' : '' ?>>Other</option>
                        </select>
                        <label for="subtype">Secondary collection type</label>
                        <input name="subtype" placeholder="Insert a secondary category" type="text" value="<?php echo empty($asset['info']['subtype']) ? '' : $asset['info']['subtype'] ?>" />
                        <label for="owner">Legal owner *</label>
                        <input required name="owner" placeholder="Insert the legal owner of the object" type="text" value="<?php echo empty($asset['info']['owner']) ? '' : $asset['info']['owner'] ?>" />
                        <label for="description">Description *</label>
                        <textarea name="description" placeholder="Insert a detailed description" style="resize:vertical"><?php echo empty($asset['info']['description']) ? '' : $asset['info']['description'] ?></textarea>
                    </fieldset>
                    <fieldset name="internal-information">
                        <h2>Internal information</h2>
                        <p>You can include internal private comments and the asset's field of use for management purposes. These fields are optional.</p>
                        <label for="field-of-use">Fields of usage</label>
                        <textarea name="field-of-use" placeholder="Separate multiple fields of usage using a comma (lorem, ipsum, etc.)"><?php echo empty($asset['info']['fieldOfUse']) ? '' : implode(', ', $asset['info']['fieldOfUse']) ?></textarea>
                        <label for="comments">Comments (Private)</label>
                        <textarea name="comments" placeholder="Insert any additional comments"><?php echo empty($asset['info']['comments']) ? '' : $asset['info']['comments'] ?></textarea>
                    </fieldset>
                    <fieldset name="uploads">
                        <h2>Uploads</h2>
                        <p>Manage your content and upload new files, images and videos.</p>
                        <h3>Files</h3>
                        <?php
                        if (!empty($asset['assets']['files'])) {
                            foreach ($asset['assets']['files'] as $file) {
                        ?>
                                <div class="file">
                                    <div>
                                        <button class="delete"><span class="fas fa-times"></span></button>
                                        <?php echo $file['filename'] . ' (' . $file['size'] . ')' ?>
                                    </div>
                                    <input type="file" name="<?php $file['id'] ?>" accept="image/png, image/jpeg" multiple />
                                </div>
                        <?php
                            }
                        }
                        ?>
                        <input type="file" name="files" accept="image/png, image/jpeg" multiple />
                        <h3>Images</h3>
                        <?php
                        if (!empty($asset['assets']['images'])) {
                            foreach ($asset['assets']['images'] as $file) {
                        ?>
                                <div class="file">
                                    <div>
                                        <button class="delete"><span class="fas fa-times"></span></button>
                                        <?php echo $file['filename'] . ' (' . $file['size'] . ')' ?>
                                    </div>
                                    <input type="file" name="<?php $file['id'] ?>" multiple />
                                </div>
                        <?php
                            }
                        }
                        ?>
                        <input type="file" name="images" accept="image/png, image/jpeg" multiple />
                        <h3>Videos</h3>
                        <?php
                        if (!empty($asset['assets']['videos'])) {
                            foreach ($asset['assets']['videos'] as $file) {
                        ?>
                                <div class="file">
                                    <div>
                                        <button class="delete"><span class="fas fa-times"></span></button>
                                        <?php echo $file['filename'] . ' (' . $file['size'] . ')' ?>
                                    </div>
                                    <input type="file" name="<?php $file['id'] ?>" accept="image/png, image/jpeg" multiple />
                                </div>
                        <?php
                            }
                        }
                        ?>
                        <input type="file" name="videos" accept="image/png, image/jpeg" multiple />
                    </fieldset>
                    <div class="error"></div>
                    <button type="submit" class="action">Submit</button>
                </form>
            </div>
            <?php 
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
function asset_creation_html(string $error = null)
{
    if (!empty($error)) {
        show_alert($error);
    } else {
        // TODO @alexandrosraikos: Move Fields of use outside of internal information.
        // TODO @alexandrosraikos: Comma-separated fields of use.
        // TODO @alexandrosraikos: Write subtype as a custom text field.
    ?>
        <div class="policycloud-marketplace">
            <form id="policycloud-object-create" action="">
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
                    <select name="subtype">
                        <option value="" selected>-</option>
                        <option value="algorithms">Algorithms</option>
                        <option value="tools">Tools</option>
                        <option value="policies">Policies</option>
                        <option value="datasets">Datasets</option>
                        <option value="webinars">Webinars</option>
                        <option value="tutorials">Tutorials</option>
                        <option value="documents">Documents</option>
                        <option value="externals">Externals</option>
                        <option value="other">Other</option>
                    </select>
                    <label for="owner">Legal owner *</label>
                    <input required name="owner" placeholder="Insert the legal owner of the object" type="text" />
                    <label for="description">Description *</label>
                    <textarea name="description" placeholder="Insert a detailed description" style="resize:vertical"></textarea>
                </fieldset>
                <fieldset name="internal-information">
                    <h2>Internal information</h2>
                    <p>You can include internal private comments and the asset's field of use for management purposes. These fields are optional.</p>
                    <label for="field-of-use">Fields of usage</label>
                    <textarea name="field-of-use" placeholder="Separate multiple fields of usage using a comma (lorem, ipsum, etc.)"></textarea>
                    <label for="comments">Comments</label>
                    <textarea name="comments" placeholder="Insert any additional comments"></textarea>
                </fieldset>
                <div class="error"></div>
                <button type="submit" class="action ">Create object</button>
            </form>
        </div>
    <?php
    }
}

/**
 * 
 * Prints an error or notice box with a close button.
 * The close button is handled @see policycloud-marketplace-public.js
 * 
 * @param string $message The message to be shown.
 * @param bool $dismissable Whether the alert is dismissable or not.
 * @param string $type The type of message, a 'notice' or an 'error'.
 * 
 * @since 1.0.0
 */
function show_alert(string $message, bool $dismissable = false, string $type = 'error')
{
    echo  '<div class="policycloud-marketplace-' . $type . ' ' . ($dismissable ? 'dismissable' : '') . '"><span>' . $message . '</span></div>';
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

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}


/**
 * Display the account page HTML for authenticated users.
 *
 * @param   array $information The user information array.
 * @param   array $item The assets connected to this account.
 * @param   array $statistics The statistics connected to this account.
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
function account_html(array $information, $picture, array $statistics, array $assets, array $reviews, array $approvals = [], array $args = [])
{
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
    function asset_viewer_html(string $id, array $content, array $args, callable $inner_html)
    {
    ?>
        <header>
            <h3><?php echo ucfirst($id) ?></h3>
            <div class="actions">
                <form action="" class="selector">
                    <label for="sort-by">Sort by</label>
                    <select name="sort-by" data-category="<?php echo $id ?>">
                        <option value="newest" <?php echo ((($_GET['sort_by'] ?? '' == 'newest') || empty($_GET['sort_by'])) ? "selected" : "") ?>>Newest</option>
                        <option value="oldest" <?php echo (($_GET['sort_by'] ?? '' == 'oldest') ? "selected" : "") ?>>Oldest</option>
                        <option value="rating-asc" <?php echo (($_GET['sort_by'] ?? '' == 'rating-asc') ? "selected" : "") ?>>Highest rated</option>
                        <option value="rating-desc" <?php echo (($_GET['sort_by'] ?? '' == 'rating-desc') ? "selected" : "") ?>>Lowest rated</option>
                        <?php
                        if ($id == 'assets') {
                        ?>
                            <option value="views-asc" <?php echo (($_GET['sort_by'] ?? '' == 'views-asc') ? "selected" : "") ?>>Most viewed</option>
                            <option value="views-desc" <?php echo (($_GET['sort_by'] ?? '' == 'views-desc') ? "selected" : "") ?>>Least viewed</option>
                        <?php } ?>
                        <option value="title" <?php echo (($_GET['sort_by'] ?? '' == 'title') ? "selected" : "") ?>>Title</option>
                    </select>
                    <label for="items-per-page">Items per page</label>
                    <select name="items-per-page" data-category="<?php echo $id ?>">
                        <option value="5" <?php echo ((($_GET['items_per_page'] ?? '' == '5') || empty($_GET['items_per_page'])) ? "selected" : "") ?>>5</option>
                        <option value="10" <?php echo (($_GET['items_per_page'] ?? '' == '10') ? "selected" : "") ?>>10</option>
                        <option value="25" <?php echo (($_GET['items_per_page'] ?? '' == '25') ? "selected" : "") ?>>25</option>
                        <option value="50" <?php echo (($_GET['items_per_page'] ?? '' == '50') ? "selected" : "") ?>>50</option>
                        <option value="100" <?php echo (($_GET['items_per_page'] ?? '' == '100') ? "selected" : "") ?>>100</option>
                    </select>
                </form>
                <?php
                if (!$args['visiting'] && $id == 'assets') {
                ?>
                    <a id="policycloud-upload" href="<?php echo $args['upload_page'] ?>" title="Create new"><span class="fas fa-plus"></span> Create new</a>
                <?php } ?>
            </div>
        </header>
        <div class="collection-filters" data-category="<?php echo $id ?>">
            <div>Filter by type:</div>
        </div>
        <div class="paginated-list" data-category="<?php echo $id ?>">
            <?php
            if (!empty($content['results'])) {
                foreach ($content['results'] as $page => $page_items) {
                    echo '<ul data-page="' . ($page + 1) . '" class="page ' . $id . ' ' . ($page == 0 ? 'visible' : '') . '">';
                    if (!empty($content)) foreach ($page_items as $item) $inner_html($item);
                    else show_alert("You don't have any " . $id . " yet.");
                    echo '</ul>';
                }
            } else {
                show_alert('This user does not have any ' . $id . '.', false, 'notice');
            } ?>
            <nav class="pagination">
                <?php
                if (count($content['results'] ?? []) > 1) {
                    foreach ($content['results'] as $page => $page_items) {
                        echo '<button data-category="' . $id . '" class="page-selector ' . (($page == ($_GET['page'] ?? 0)) ? 'active' : '') . '" data-' . $id . '-page="' . $page + 1 . '">' . ($page + 1) . '</button>';
                    }
                } ?>
            </nav>
        </div>
    <?php
    }

    // Check for any errors regarding authorization.
    if (!empty($args['notice'])) {
        show_alert(($args['notice'] == 'not-logged-in') ? 'You are not logged in, please <a href="' . $args['login_page'] . '">log in</a> to your account. Don\'t have an account yet? You can <a href="' . $args['registration_page'] . '">register</a> here.' : $args['notice'], false, 'notice');
    }

    if (!empty($args['error'])) show_alert($args['error']);

    if (!empty($information)) {
        // Check for any notices.
        if (!empty($args['notice'])) {
            show_alert($args['notice'], true, 'notice');
        }

        // Show account verification notice.
        if (!empty($information['account']['verified'])) {
            if ($information['account']['verified'] !== '1') {
                show_alert('Your account is still unverified, please check your email inbox or spam folder for a verification email. You can <a id="policycloud-marketplace-resend-verification-email">resend</a> it if you can\'t find it.', false, 'notice');
            }
        } else show_alert("Your account verification status couldn't be accessed.");
    ?>
        <div id="policycloud-account" class="policycloud-marketplace">
            <div id="policycloud-account-sidebar">
                <?php
                if (!empty($picture)) {
                    echo '<img src="data:image/*;base64,' . base64_encode($picture) . '" draggable="false" />';
                } else {
                    echo '<img src="' . get_site_url('', '/wp-content/plugins/policycloud-marketplace/public/assets/svg/user.svg') . '" draggable="false" />';
                }
                ?>
                <nav>
                    <button class="tactile" id="policycloud-account-overview" class="active">Overview</button>
                    <button class="tactile" id="policycloud-account-assets">Assets</button>
                    <button class="tactile" id="policycloud-account-reviews">Reviews</button>
                    <?php
                    if (!$args['visiting'] && $args['is_admin']) {
                    ?>
                        <button class="tactile" id="policycloud-account-approvals">Approvals</button>
                    <?php
                    }
                    ?>
                    <button class="tactile" id="policycloud-account-information">Information</button>
                    <?php if (!$args['visiting']) { ?>
                        <button class="tactile policycloud-logout">Log out</button>
                    <?php } ?>
                </nav>
            </div>
            <div id="policycloud-account-content">
                <div class="policycloud-account-title">
                    <h2>
                        <?php
                        echo ($information['info']['title'] ?? '') . ' ' . $information['info']['name'] . ' ' . $information['info']['surname'];
                        ?>
                    </h2>
                    <div>
                        <?php
                        echo ($information['info']['organization'] ?? '');
                        ?>
                    </div>
                </div>
                <div>
                    <section class="policycloud-account-overview focused">
                        <header>
                            <h3>Overview</h3>
                        </header>
                        <div>
                            <h4>About</h4>
                            <p>
                                <?php echo $information['info']['about'] ?? '' ?>
                            </p>
                            <?php
                            if (!empty($information['info']['social'][0])) {
                            ?>
                                <ul>
                                    <?php
                                    foreach ($information['info']['social'] as $link) {
                                        echo '<li><a href="' . explode(':', $link, 2)[1] . '" target="blank">' . explode(':', $link, 2)[0] . '</a></li>';
                                    }
                                    ?>
                                </ul>
                            <?php } ?>
                        </div>
                        <?php if (!empty($statistics)) { ?>
                            <h4>Statistics</h4>
                            <table class="statistics">
                                <tr>
                                    <td>
                                        <div class="large-figure"><span class="fas fa-list"></span> <?php echo $statistics['total_descriptions'] ?></div>
                                        <div class="assets-caption">Total descriptions</div>
                                    </td>
                                    <td>
                                        <div class="large-figure"><span class="fas fa-check"></span> <?php echo $statistics['approved_descriptions'] ?></div>
                                        <div class="assets-caption">Approved descriptions</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="large-figure"><span class="fas fa-download"></span> <?php echo $statistics['total_downloads'] ?></div>
                                        <div class="assets-caption">Total downloads</div>
                                    </td>
                                    <td>
                                        <div class="large-figure"><span class="fas fa-file"></span> <?php echo $statistics['assets_uploaded'] ?></div>
                                        <div>Assets uploaded</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="large-figure"><span class="fas fa-comment"></span> <?php echo $statistics['total_reviews'] ?></div>
                                        <div class="assets-caption">Total reviews</div>
                                    </td>
                                    <td>
                                        <div class="large-figure"><span class="fas fa-star"></span> <?php echo $statistics['average_rating'] ?></div>
                                        <div class="assets-caption">Average rating</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="large-figure"><span class="fas fa-eye"></span> <?php echo $statistics['total_views'] ?></div>
                                        <div class="assets-caption">Total views</div>
                                    </td>
                                </tr>
                            </table>
                        <?php
                        } else {
                            show_alert("Statistics for this user are currently unavailable.", false, 'notice');
                        }
                        ?>
                    </section>
                    <section class="policycloud-account-assets">
                        <?php
                        asset_viewer_html('assets', $assets, $args, function ($asset) use ($args) {
                        ?>
                            <li data-type-filter="<?php echo $asset['info']['type'] ?>" data-date-updated="<?php echo strtotime($asset['metadata']['uploadDate']) ?>" data-rating="<?php echo $asset['metadata']['reviews']['average_rating'] ?>" data-total-views="<?php echo $asset['metadata']['views'] ?>" class="visible">
                                <div class="description">
                                    <a href="<?php echo $args['description_page'] . "?did=" . $asset['id'] ?>">
                                        <h4><?php echo $asset['info']['title'] ?></h4>
                                    </a>
                                    <p><?php echo $asset['info']['short_desc'] ?></p>
                                    <div class="metadata">
                                        <a class="pill"><?php echo $asset['info']['type']  ?></a>
                                        <a class="pill"><?php echo $asset['info']['subtype']  ?></a>
                                        <span><span class="fas fa-star"></span> <?php echo $asset['metadata']['reviews']['average_rating'] . ' (' . $asset['metadata']['reviews']['no_reviews'] . ' reviews)' ?></span>
                                        <span><span class="fas fa-eye"></span> <?php echo $asset['metadata']['views'] ?> views</span>
                                        <span>Last updated <?php echo time_elapsed_string(date('Y-m-d H:i:s', strtotime($asset['metadata']['uploadDate']))) ?></span>
                                        <span class="label <?php echo ($asset['metadata']['approved'] == 1) ? 'success' : 'notice' ?>"><?php echo ($asset['metadata']['approved'] == 1) ? 'Approved' : 'Pending' ?></span>
                                    </div>
                                </div>
                            </li>
                        <?php
                        });
                        ?>
                    </section>
                    <section class="policycloud-account-reviews">
                        <?php
                        asset_viewer_html('reviews', $reviews, $args, function ($review) use ($args) {
                        ?>
                            <li data-type-filter="<?php echo $review['collection'] ?>" data-date-updated="<?php echo strtotime($review['updated_review_date']) ?>" data-rating="<?php echo $review['rating'] ?>" class="visible">
                                <div class="review">
                                    <div class="rating">
                                        <span><span class="fas fa-star"></span> <?php echo $review['rating'] ?></span>
                                        <span>Posted <?php echo time_elapsed_string(date('Y-m-d H:i:s', strtotime($review['updated_review_date']))) ?></span>
                                    </div>
                                    <p>"<?php echo $review['comment'] ?>"</p>
                                    <a href="<?php echo $args['description_page'] . "?did=" . $review['did'] ?>">
                                        <h4><?php echo $review['title'] ?></h4>
                                    </a>
                                    <div class="metadata">
                                        <a class="pill"><?php echo $review['collection']  ?></a>
                                    </div>
                                </div>
                            </li>
                        <?php
                        });
                        ?>
                    </section>
                    <?php
                    if (!$args['visiting'] && $args['is_admin']) {
                    ?>
                        <section class="policycloud-account-approvals">
                            <?php
                            asset_viewer_html('approvals', $approvals, $args, function ($approval) use ($args) {
                            ?>
                                <li data-type-filter="<?php echo $approval['info']['type'] ?>" data-date-updated="<?php echo strtotime($approval['metadata']['uploadDate']) ?>" data-rating="<?php echo $approval['metadata']['reviews']['average_rating'] ?>" data-total-views="<?php echo $approval['metadata']['views'] ?>" class="visible">
                                    <div class="description">
                                        <a href="<?php echo $args['description_page'] . "?did=" . $approval['id'] ?>">
                                            <h4><?php echo $approval['info']['title'] ?></h4>
                                        </a>
                                        <p><?php echo $approval['info']['short_desc'] ?></p>
                                        <div class="metadata">
                                            <a class="pill"><?php echo $approval['info']['type']  ?></a>
                                            <a class="pill"><?php echo $approval['info']['subtype']  ?></a>
                                            <span><span class="fas fa-star"></span> <?php echo $approval['metadata']['reviews']['average_rating'] . ' (' . $approval['metadata']['reviews']['no_reviews'] . ' reviews)' ?></span>
                                            <span><span class="fas fa-eye"></span> <?php echo $approval['metadata']['views'] ?> views</span>
                                            <span>Last updated <?php echo time_elapsed_string(date('Y-m-d H:i:s', strtotime($approval['metadata']['uploadDate']))) ?></span>
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
                    <section class="policycloud-account-information">
                        <header>
                            <h3>Information</h3>
                            <?php
                            if (!$args['visiting'] || $args['is_admin']) {
                            ?>
                                <button id="policycloud-marketplace-account-edit-toggle"><span class="fas fa-pen"></span> Edit</button>
                            <?php
                            }
                            ?>
                        </header>
                        <form id="policycloud-marketplace-account-edit" accept-charset="utf8" action="">
                            <table class="information">
                                <?php
                                if ($args['is_admin'] || !$args['visiting']) {
                                ?>
                                    <tr>
                                        <td class="folding">
                                            <span>Profile picture</span>
                                        </td>
                                        <td class="folding">
                                            <?php
                                            if (!empty($picture)) {
                                            ?>
                                                <div class="file-editor" data-name="profile-picture">
                                                    <img class="file" src="data:image/*;base64,<?php echo base64_encode($picture) ?>" draggable="false" />
                                                    <button type="button" class="delete"><span class="fas fa-times"></span></button>
                                                </div>
                                            <?php
                                            }
                                            if (!$args['is_admin'] && !$args['visiting']) {
                                            ?>
                                                <span class="folding">
                                                    <input type="file" name="profile_picture" accept="image/png, image/jpeg" />
                                                    <label for="picture">Please select an image of up to 1MB and over 256x256 for optimal results. Supported file types: jpg, png.</label>
                                                </span>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                                <tr>
                                    <td>
                                        Summary
                                    </td>
                                    <td>
                                        <span class="folding visible">
                                            <?php echo $information['info']['about']; ?>
                                        </span>
                                        <?php
                                        if (!$args['visiting'] || $args['is_admin']) {
                                        ?>
                                            <textarea name="about" class="folding" placeholder="Tell us about yourself" style="resize:vertical"><?php echo $information['info']['about'] ?? ''; ?></textarea>
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
                                            if (!empty($information['info']['social'][0])) {
                                                foreach ($information['info']['social'] as $link) {
                                                    echo '<a href="' . explode(':', $link, 2)[1] . '" target="blank">' . explode(':', $link, 2)[0] . '</a><br/>';
                                                }
                                            }
                                            ?>
                                        </span>
                                        <?php
                                        if (!$args['visiting'] || $args['is_admin']) {
                                        ?>
                                            <div class="socials folding">
                                                <div>
                                                    <?php

                                                    if (!empty($information['info']['social'][0])) {
                                                        foreach ($information['info']['social'] as $link) {
                                                            $link_title = explode(':', $link, 2)[0];
                                                            $link_url = explode(':', $link, 2)[1];
                                                    ?>
                                                            <div>
                                                                <input type="text" name="socials-title[]" placeholder="Example" value="<?php echo $link_title ?>" />
                                                                <input type="url" name="socials-url[]" placeholder="https://www.example.org/" value="<?php echo $link_url ?>" />
                                                                <button class="remove-field" title="Remove this link." <?php if (count($information['info']['social']) == 1) echo 'disabled' ?>><span class="fas fa-times"></span></button>
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
                                            <?php
                                            echo ($information['username'] ?? '-');
                                            ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <?php
                                    if (!$args['visiting']) {
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
                                            <?php echo ($information['account']['role'] == 'admin') ? 'Administrator' : 'User'; ?>
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
                                            echo ($information['info']['title'] ?? '') . ' ' . ($information['info']['name']) . ' ' . ($information['info']['surname']);
                                            ?>
                                        </span>
                                        <?php
                                        if (!$args['visiting'] || $args['is_admin']) {
                                        ?>
                                            <select class="folding" name="title">
                                                <option value="Mr." <?php echo ($information['info']['title'] == 'Mr.' ? 'selected' : '') ?>>Mr.</option>
                                                <option value="Ms." <?php echo ($information['info']['title'] == 'Ms.' ? 'selected' : '') ?>>Ms.</option>
                                                <option value="Mrs." <?php echo ($information['info']['title'] == 'Mrs.' ? 'selected' : '') ?>>Mrs.</option>
                                                <option value="Dr." <?php echo ($information['info']['title'] == 'Dr.' ? 'selected' : '') ?>>Dr.</option>
                                                <option value="Prof." <?php echo ($information['info']['title'] == 'Prof.' ? 'selected' : '') ?>>Prof.</option>
                                                <option value="Sir" <?php echo ($information['info']['title'] == 'Sir' ? 'selected' : '') ?>>Sir</option>
                                                <option value="Miss" <?php echo ($information['info']['title'] == 'Miss' ? 'selected' : '') ?>>Miss</option>
                                                <option value="Mx." <?php echo ($information['info']['title'] == 'Mx.' ? 'selected' : '') ?>>Mx.</option>
                                                <option value="-" <?php echo ($information['info']['title'] == '-' ? 'selected' : '') ?>>None</option>
                                            </select>
                                            <input class="folding" type="text" name="name" placeholder="Name" value="<?php echo ($information['info']['name']); ?>" required />
                                            <input class="folding" type="text" name="surname" placeholder="Surname" value="<?php echo ($information['info']['surname']); ?>" required />
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
                                            echo ($information['info']['gender'] ?? '-');
                                            ?>
                                        </span>
                                        <?php
                                        if (!$args['visiting'] || $args['is_admin']) {
                                        ?>
                                            <select name="gender" class="folding">
                                                <option value="male" <?php echo ($information['info']['gender'] == 'male' ? 'selected' : '') ?>>Male</option>
                                                <option value="female" <?php echo ($information['info']['gender'] == 'female' ? 'selected' : '') ?>>Female</option>
                                                <option value="transgender" <?php echo ($information['info']['gender'] == 'transgender' ? 'selected' : '') ?>>Transgender</option>
                                                <option value="genderqueer" <?php echo ($information['info']['gender'] == 'genderqueer' ? 'selected' : '') ?>>Genderqueer</option>
                                                <option value="questioning" <?php echo ($information['info']['gender'] == 'questioning' ? 'selected' : '') ?>>Questioning</option>
                                                <option value="-" <?php echo ($information['info']['gender'] == '-' ? 'selected' : '') ?>>Prefer not to say</option>
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
                                            echo ($information['info']['organization'] ?? '-');
                                            ?>
                                        </span>
                                        <?php
                                        if (!$args['visiting'] || $args['is_admin']) {
                                        ?>
                                            <input class="folding" type="text" name="organization" value="<?php echo ($information['info']['organization'] ?? ''); ?>" placeholder="Insert your organization here" />
                                        <?php
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <?php
                                if (!empty($information['info']['email']) || !$args['visiting']) {
                                ?>
                                    <tr>
                                        <td>
                                            E-mail
                                        </td>
                                        <td>
                                            <span class="folding visible">
                                                <?php
                                                echo ($information['info']['email']);
                                                if ($information['account']['verified'] != '1') {
                                                ?>
                                                    <span class="unverified">(Unverified)</span>
                                                    <button id="policycloud-marketplace-resend-verification-email">Resend verification email</button>
                                                <?php
                                                } else {
                                                    if (!$args['visiting'] || $args['is_admin']) {
                                                        echo ' <span class="label ' . (($information['profile_parameters']['public_email'] == 0) ? 'notice' : 'success') . '">' . (($information['profile_parameters']['public_email'] == 0) ? 'Private' : 'Public') . '</span>';
                                                    }
                                                }
                                                ?>
                                            </span>
                                            <?php
                                            if (!$args['visiting'] || $args['is_admin']) {
                                            ?>
                                                <label for="email" class="folding">Changing this setting will require a verification of the new e-mail address.</label>
                                                <input class="folding" type="email" name="email" value="<?php echo $information['info']['email'] ?>" required />
                                                <select name="public-email" class="folding">
                                                    <option value="1" <?php echo ($information['profile_parameters']['public_email'] == 1 ? 'selected' : '') ?>>Public</option>
                                                    <option value="0" <?php echo ($information['profile_parameters']['public_email'] == 0 ? 'selected' : '') ?>>Private</option>
                                                </select>
                                            <?php
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                <?php
                                }
                                if (!empty($information['info']['phone']) || !$args['visiting']) {
                                ?>
                                    <tr>
                                        <td>
                                            Phone number
                                        </td>
                                        <td>
                                            <span class="folding visible">
                                                <?php
                                                if (!empty($information['info']['phone'])) {
                                                    echo ($information['info']['phone']);
                                                    if (!$args['visiting'] || $args['is_admin']) {
                                                        echo ' <span class="label ' . (($information['profile_parameters']['public_phone'] == 0) ? 'notice' : 'success') . '">' . (($information['profile_parameters']['public_phone'] == 0) ? 'Private' : ' Public') . '</span>';
                                                    }
                                                } else echo '-';
                                                ?>
                                            </span>
                                            <?php
                                            if (!$args['visiting'] || $args['is_admin']) {
                                            ?>
                                                <input class="folding" type="text" name="phone" value="<?php
                                                                                                        echo (empty($information['info']['phone']) ? '' : $information['info']['phone']); ?>" placeholder="Insert your phone number here" />
                                                <select name="public-phone" class="folding">
                                                    <option value="1" <?php echo ($information['profile_parameters']['public_phone'] == 1 ? 'selected' : '') ?>>Public</option>
                                                    <option value="0" <?php echo ($information['profile_parameters']['public_phone'] == 0 ? 'selected' : '') ?>>Private</option>
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
                                        echo date('d/m/y', strtotime($information['account']['registration_datetime']))
                                        ?>
                                    </td>
                                </tr>
                            </table>
                            <?php
                            if (!$args['visiting'] || $args['is_admin']) {
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
                        if (!$args['visiting']) {
                        ?>
                            <button id="policycloud-marketplace-request-data-copy" class="action">Request data copy</button>
                        <?php
                        }
                        if (!$args['visiting'] || $args['is_admin']) {
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
}
