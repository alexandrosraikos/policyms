<?php

// TODO @alexandrosraikos: Replace username field with full name for logged in users. (#113)


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
        ?>
        <div class="policycloud-marketplace descriptions-grid">
            <ul>
                <?php
                foreach ($descriptions as $description) {
                    ?>
                    <li>
                        <a href="<?= $description_url . '?did=' . $description->id ?>">
                            <div class="cover">
                                <img src="
                                    <?=
                                    (PolicyCloud_Marketplace_Public::get_plugin_setting(true, 'marketplace_host')
                                        . '/descriptions/image/' . $description->id
                                    )
                                    ?>" alt="" />
                                <div class="content">
                                    <h4>
                                        <?= $description->information['title'] ?>
                                    </h4>
                                    <p>
                                        <?= $description->information['short_desc'] ?>
                                    </p>
                                </div>
                            </div>
                            <div class="metadata">
                                <div>
                                    <div class="owner">
                                        <?= $description->metadata['provider'] ?>
                                    </div>
                                    <div class="last-updated">
                                        Updated
                                        <?=
                                        time_elapsed_string(
                                            date(
                                                'Y-m-d H:i:s',
                                                strtotime($description->metadata['updateDate'])
                                            )
                                        )
                                        ?>
                                    </div>
                                </div>
                                <div>
                                    <span class="reviews">
                                        <span class="fas fa-star"></span>
                                        <span>
                                            <?=
                                            ($description->metadata['reviews']['average_rating']
                                                . ' (' . $description->metadata['reviews']['no_reviews'] . ' reviews)'
                                            )
                                            ?>
                                        </span>
                                        <span class="views">
                                            <span class="fas fa-eye"></span>
                                            <?= $description->metadata['views'] ?> views
                                        </span>
                                </div>
                                <div>
                                    <span class="type pill">
                                        <?= $description->type ?>
                                    </span>
                                    <?php
                                    if (!empty($description->information['subtype'])) {
                                        ?>
                                        <span class="sub-type pill">
                                            <?= $description->information['subtype']  ?>
                                        </span>
                                    <?php } ?>
                                </div>
                            </div>
                        </a>
                    </li>
                    <?php
                }
                ?>
            </ul>
        </div>
        <?php
    }
}

function featured_descriptions_html(array $categories, string $description_page): void
{
    ?>
    <div class="policycloud-marketplace featured-descriptions">
        <div class="white-container">
        <div class="row statistics">
            <div class="column">
                <figure>
                    <?= $categories['statistics']['sum'] ?>
                    <figcaption>Total descriptions</figcaption>
                </figure>
            </div>
            <div class="column">
                <figure>
                    <?= $categories['statistics']['top'][0]['descriptions'] ?>
                    <figcaption>
                        <?= ucfirst($categories['statistics']['top'][0]['collection']) ?>
                    </figcaption>
                </figure>
            </div>
            <div class="column">
                <figure>
                    <?= $categories['statistics']['top'][1]['descriptions'] ?>
                    <figcaption>
                        <?= ucfirst($categories['statistics']['top'][1]['collection']) ?>
                    </figcaption>
                </figure>
            </div>
            <div class="column">
                <figure>
                    <?= $categories['statistics']['top'][2]['descriptions'] ?>
                    <figcaption>
                        <?= ucfirst($categories['statistics']['top'][2]['collection']) ?>
                    </figcaption>
                </figure>
            </div>
        </div>
        </div>
        <h2>Top rated descriptions</h2>
        <?php
        descriptions_grid_html($categories['top_rated'][0], $description_page);
        ?>
        <h2>Most viewed descriptions</h2>
        <?php
        descriptions_grid_html($categories['most_viewed'][0], $description_page);
        ?>
        <h2>Latest descriptions</h2>
        <?php
        descriptions_grid_html($categories['latest'][0], $description_page);
        ?>
        <h2>Suggestions</h2>
        <?php
        descriptions_grid_html($categories['suggestions'][0], $description_page);
        ?>
    </div>
    <?php
}

function descriptions_archive_filters_html($filters)
{
    ?>
    <div class="filters">
        <button class="close outlined filters-toggle">Close</button>
        <h2>Filters</h2>
        <p>Select the options below to narrow your search.</p>
        <form>
            <fieldset>
                <input type="text" name="search" placeholder="Search descriptions" value="<?= $_GET['search'] ?? '' ?>" />
            </fieldset>
            <fieldset>
                <h3>Types</h3>
                <div class="types">
                    <span>
                        <input type="radio" name="type" value="" <?= (empty($_GET['type'])) ? 'checked' : '' ?> />
                        <label for="type">All</label>
                    </span>
                    <span>
                        <input type="radio" name="type" value="algorithms" <?= (($_GET['type'] ?? '') == 'algorithms') ? 'checked' : '' ?> />
                        <label for="type">Algorithms</label>
                    </span>
                    <span>
                        <input type="radio" name="type" value="tools" <?= (($_GET['type'] ?? '') == 'tools') ? 'checked' : '' ?> />
                        <label for="type">Tools</label>
                    </span>
                    <span>
                        <input type="radio" name="type" value="policies" <?= (($_GET['type'] ?? '') == 'policies') ? 'checked' : '' ?> />
                        <label for="type">Policies</label>
                    </span>
                    <span>
                        <input type="radio" name="type" value="datasets" <?= (($_GET['type'] ?? '') == 'datasets') ? 'checked' : '' ?> />
                        <label for="type">Datasets</label>
                    </span>
                    <span>
                        <input type="radio" name="type" value="webinars" <?= (($_GET['type'] ?? '') == 'webinars') ? 'checked' : '' ?> />
                        <label for="type">Webinars</label>
                    </span>
                    <span>
                        <input type="radio" name="type" value="tutorials" <?= (($_GET['type'] ?? '') == 'tutorials') ? 'checked' : '' ?> />
                        <label for="type">Tutorials</label>
                    </span>
                    <span>
                        <input type="radio" name="type" value="documents" <?= (($_GET['type'] ?? '') == 'documents') ? 'checked' : '' ?> />
                        <label for="type">Documents</label>
                    </span>
                    <span>
                        <input type="radio" name="type" value="externals" <?= (($_GET['type'] ?? '') == 'externals') ? 'checked' : '' ?> />
                        <label for="type">Externals</label>
                    </span>
                    <span>
                        <input type="radio" name="type" value="other" <?= (($_GET['type'] ?? '') == 'other') ? 'checked' : '' ?> />
                        <label for="type">Other</label>
                    </span>
                </div>
            </fieldset>
            <fieldset>
                <h3>Views</h3>
                <div class="views">
                    <div>
                        <input type="number" name="views-gte" placeholder="0" value="<?= $_GET['views-gte'] ?? '' ?>" min="0" />
                    </div>
                    <div>
                        <input type="number" name="views-lte" placeholder="<?= $filters['max_views'] ?>" value="<?= $_GET['views-lte'] ?? "" ?>" max="<?= $filters['max_views'] ?>" />
                    </div>
                </div>
            </fieldset>
            <fieldset>
                <h3>Date</h3>
                <div class="dates">
                    <div>
                        <label for="update-date-gte">From</label>
                        <input type="date" onfocus="(this.type='date')" name="update-date-gte" placeholder="<?= date("Y-m-d", strtotime($filters['oldest'])) ?>" value="<?= $_GET['update-date-gte'] ?? '' ?>" min="<?= date("Y-m-d", strtotime($filters['oldest'])) ?>" max="<?= date("Y-m-d") ?>" />
                    </div>
                    <div>
                        <label for="update-date-lte">To</label>
                        <input type="date" name="update-date-lte" placeholder="<?= date("Y-m-d") ?>" value="<?= $_GET['update-date-lte'] ?? '' ?>" min="<?= date("Y-m-d", strtotime($filters['oldest'])) ?>" max="<?= date("Y-m-d") ?>" />
                    </div>
                </div>
            </fieldset>
            <button type="submit" class="action">Apply filters</button>
        </form>
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
    <div class="policycloud-marketplace descriptions archive inspect">
        <?= descriptions_archive_filters_html($filters) ?>
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
                            <option value="newest" <?= (((($_GET['sort-by'] ?? '') == 'newest') || empty($_GET['sort-by'])) ? "selected" : "") ?>>Newest</option>
                            <option value="oldest" <?= ((($_GET['sort-by'] ?? '') == 'oldest') ? "selected" : "") ?>>Oldest</option>
                            <option value="rating-desc" <?= ((($_GET['sort-by'] ?? '') == 'rating-desc') ? "selected" : "") ?>>Highest rated</option>
                            <option value="rating-asc" <?= ((($_GET['sort-by'] ?? '') == 'rating-asc') ? "selected" : "") ?>>Lowest rated</option>
                            <option value="views-desc" <?= ((($_GET['sort-by'] ?? '') == 'views-desc') ? "selected" : "") ?>>Most viewed</option>
                            <option value="views-asc" <?= ((($_GET['sort-by'] ?? '') == 'views-asc') ? "selected" : "") ?>>Least viewed</option>
                            <option value="title" <?= ((($_GET['sort-by'] ?? '') == 'title') ? "selected" : "") ?>>Title</option>
                        </select>
                    </fieldset>
                    <fieldset>
                        <label for="items-per-page">Items per page</label>
                        <select name="items-per-page">
                            <option value="10" <?= (((($_GET['items-per-page'] ?? '') == 10) || empty($_GET['sort-by'])) ? "selected" : "") ?>>10</option>
                            <option value="25" <?= ((($_GET['items-per-page'] ?? '') == '25') ? "selected" : "") ?>>25</option>
                            <option value="50" <?= ((($_GET['items-per-page'] ?? '') == '50') ? "selected" : "") ?>>50</option>
                            <option value="100" <?= ((($_GET['items-per-page'] ?? '') == '100') ? "selected" : "") ?>>100</option>
                        </select>
                    </fieldset>
                </form>
            </header>
            <?php
            if (!empty($descriptions)) {
                ?>
                <div class="gallery">
                    <?php
                    descriptions_grid_html($descriptions['content'][0], $description_page);
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
 * The description editing form.
 *
 * This function prints a description editing form if a description object is passed.
 * If a description object is not passed, this function acts as a description creation form.
 *
 * @param PolicyCloud_Marketplace_Description|null $description
 * @return void
 */
function description_editor_html(PolicyCloud_Marketplace_Description $description = null, array $permissions = null): void
{
    // Print the main editor HTML.
    ?>
    <div class="policycloud-marketplace description editor <?= !empty($description) ? 'modalize' : '' ?>">
        <form>
            <fieldset name="basic-information">
                <h2>Basic information</h2>
                <p>
                    To create a new Marketplace asset, the following fields
                    represent basic information that will be visible to others.
                </p>
                <label for="title">
                    Title *
                </label>
                <input required name="title" placeholder="Insert a title" type="text" value="<?= $description->information['title'] ?? '' ?>" />
                <label for="type">
                    Primary collection type *
                </label>
                <select name="type" required>
                    <option value="algorithms" <?= (($description->type ?? '') == "algorithms") ? 'selected' : '' ?>>Algorithms</option>
                    <option value="tools" <?= (($description->type ?? '') == "tools") ? 'selected' : '' ?>>Tools</option>
                    <option value="policies" <?= (($description->type ?? '') == "policies") ? 'selected' : '' ?>>Policies</option>
                    <option value="datasets" <?= (($description->type ?? '') == "datasets") ? 'selected' : '' ?>>Datasets</option>
                    <option value="webinars" <?= (($description->type ?? '') == "webinars") ? 'selected' : '' ?>>Webinars</option>
                    <option value="tutorials" <?= (($description->type ?? '') == "tutorials") ? 'selected' : '' ?>>Tutorials</option>
                    <option value="documents" <?= (($description->type ?? '') == "documents") ? 'selected' : '' ?>>Documents</option>
                    <option value="externals" <?= (($description->type ?? '') == "externals") ? 'selected' : '' ?>>Externals</option>
                    <option value="other" <?= (($description->type ?? '') == "other") ? 'selected' : '' ?>>Other</option>
                </select>
                <label for="subtype">
                    Secondary collection type
                </label>
                <input name="subtype" placeholder="Insert a secondary category" type="text" value="<?= empty($description->information['subtype']) ? '' : $description->information['subtype'] ?>" />
                <label for="owner">
                    Legal owner *
                </label>
                <input required name="owner" placeholder="Insert the legal owner of the object" type="text" value="<?= empty($description->information['owner']) ? '' : $description->information['owner'] ?>" />
                <label for="description">
                    Description *
                </label>
                <textarea name="description" placeholder="Insert a detailed description" style="resize:vertical"><?= empty($description->information['description']) ? '' : $description->information['description'] ?></textarea>
                <label for="fields-of-use">
                    Fields of usage
                </label>
                <textarea name="fields-of-use" placeholder="Separate multiple fields of usage using a comma (lorem, ipsum, etc.)"><?= empty($description->information['fieldOfUse']) ? '' : implode(', ', $description->information['fieldOfUse']) ?></textarea>
            </fieldset>
            <fieldset name="internal-information">
                <h2>Additional information</h2>
                <p>You can include additional comments for authorized visitors. This field is optional.</p>
                <label for="comments">Comments</label>
                <textarea name="comments" placeholder="Insert any additional comments"><?= empty($description->information['comments']) ? '' : $description->information['comments'] ?></textarea>
            </fieldset>
            <?php 
                if (!empty($description)) { 
                ?>
                <fieldset name="uploads">
                    <h2>Uploads</h2>
                    <p>Manage your content and upload new files, images and videos up to <?= ($permissions['administrator'] ?? false) ? '1GB': '100MB' ?> in size.</p>
                    <?php
                    foreach ($description->assets as $category => $assets) {
                        $upload_notice = ($category == 'images') ? ' (supported file types: jpg, png)' : '';
                        $upload_notice = ($category == 'videos') ? ' (supported file types: mp4, ogg, webm)' : '';
                        switch ($category) {
                            case 'images':
                                $allowed_mimetypes = 'image/jpeg,image/png';
                                break;
                            case 'videos':
                                $allowed_mimetypes = 'video/mp4,video/ogg,video/webm';
                                break;
                            default:
                                $allowed_mimetypes = '';
                                break;
                        }
                        ?>
                        <h3>
                            <?php 
                                if ($category == 'images' || $category == 'videos') {
                                    echo ucfirst($category)." (Gallery)";
                                }
                                else {
                                    echo ucfirst($category);
                                }
                            ?>
                        </h3>
                        <?php
                            if ($category == 'videos') {
                                echo "
                                <p>
                                    Uploaded gallery videos are publicly accessible. Please do not include sensitive or protected information.
                                </p>
                                ";
                            }
                        ?>
                        <?php
                        if (!empty($assets)) {
                            foreach ($assets as $asset) { ?>
                                <div class="asset-editor" data-asset-type="<?= $category ?>" data-asset-id="<?= $asset->id ?>">
                                    <div>
                                        <button class="delete" data-asset-category="<?= $category ?>" data-asset-id="<?= $asset->id ?>" data-action="delete">
                                            <span class="fas fa-times"></span>
                                        </button>
                                        <?= $asset->filename . ' (' . $asset->size . ')' ?>
                                    </div>
                                    <label for="<?= $category . '-' . $asset->id ?>">
                                        Replace file<?= $upload_notice ?>:
                                    </label>
                                    <input type="file" name="<?= $category . '-' . $asset->id ?>" accept="<?= $allowed_mimetypes ?>" multiple />
                                </div>
                                <?php
                            }
                        } ?>
                        Upload new files<?= $upload_notice ?>:
                        <div class="chooser">
                            <input type="file" name="<?= $category ?>[]" accept="<?= $allowed_mimetypes ?>" multiple />
                        </div>
                        <?php
                    }
                    ?>
                </fieldset>
            <?php } ?>
            <div class="error"></div>
            <div class="actions">
                <?php
                if (!empty($description)) {
                    ?>
                    <button data-action="delete-description" class="action destructive">Delete</button>
                    <?php
                }
                ?>
                <button type="submit" class="action">Submit</button>
            </div>
        </form>
    </div>
    <?php
}

function description_reviews_list_html(array $reviews)
{
    ?>
    <ul>
        <?php
        foreach ($reviews[0] as $review) { ?>
            <li class="review">
                <div class="rating">
                    <?= $review->rating ?>
                    <span class="stars">
                        <?php
                        for ($i = 0; $i < $review->rating; $i++) {
                            ?>

                            <span class="fas fa-star"></span>
                            <?php
                        }
                        ?>
                    </span>
                </div>
                <div class="comment">
                    <?= $review->comment ?>
                </div>
                <div class="metadata">
                    <span>
                        <?= time_elapsed_string(date('Y-m-d H:i:s', strtotime($review->update_date))) ?>
                    </span>
                    <span>
                        by <a href="<?= PolicyCloud_Marketplace_Public::get_plugin_setting(false, 'account_page') . '?user=' . $review->user_id ?>"><?= $review->user_id ?></a>
                    </span>
                </div>
            </li>
        <?php } ?>
    </ul>
    <?php
}

function description_reviews_html(array $reviews = null, ?int $pages = 0, PolicyCloud_Marketplace_Review $existing_review = null, bool $provider)
{
    // TODO @alexandrosraikos: Allow author and admin to delete reviews. (#108)
    ?>
    <div class="policycloud-marketplace reviews">
        <?php
        if (!empty($reviews)) {
            description_reviews_list_html($reviews);
            ?>
            <nav class="pagination">
                <?php
                for ($page = 1; $page < $pages; $page++) {
                    $activePage = $_GET['reviews-page'] ?? 1;
                    echo '<button class="page-selector ' . (($activePage == ($page)) ? 'active' : '') . '" data-page-number="' . $page . '" data-action="change-review-page">' . ($page) . '</button>';
                }
                ?>
            </nav>
            <?php
        } else {
            show_alert('No reviews yet.', 'notice');
        }

        if (!$provider) {
            ?>
            <form>
                <label for="comment">Comment</label>
                <textarea name="comment" placeholder="Insert your comment here.."><?= !empty($existing_review) ? $existing_review->comment : null ?></textarea>
                <label for="rating">Rating</label>
                <div class="stars">
                    <?php
                    for ($i = 0; $i < 5; $i++) {
                        $rating = $i + 1;
                        ?>
                        <label>
                            <input type="radio" name="rating" value="<?= $rating ?>" class="<?= ($rating <= ($existing_review->rating ?? 0)) ? 'checked' : '' ?>" <?= ($rating == ($existing_review->rating ?? 0)) ? 'checked' : '' ?> required />
                            <span class="fas fa-star"></span>
                        </label>
                        <?php
                    }
                    ?>
                </div>
                <?= !empty($existing_review) ? '<input style="display:none" type="checkbox" name="update" checked/>' : '' ?>
                <?php
                if (!empty($existing_review)) { ?>
                    <p>
                        Last submitted <?= time_elapsed_string(date('Y-m-d H:i:s', strtotime($existing_review->update_date))) ?>
                    </p>
                <?php } ?>
                <div class="actions">
                    <?php if (!empty($existing_review)) {
                        ?>
                        <button class="action destructive" data-action="delete-review">Delete</button>
                        <?php
                    }
                    ?>
                    <button class="action" type="submit">Submit</button>
                </div>
            </form>
        <?php } ?>
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
function description_html($description, $image_blobs, $pages, $reviews, $permissions)
{

    // TODO @alexandrosraikos: Add "Links" section (identical functionality to account links). (#59)

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
        <div class="policycloud-marketplace file-viewer <?= ($collapsed) ? 'collapsed' : '' ?>">
            <button data-files-category="<?= $category ?>" class="action"><?= $title ?></button>
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
                        <tr data-file-id="<?= $asset->id ?>">
                            <td><a class="download" data-file-id="<?= $asset->id ?>" data-type="<?= $category ?>"><?= $asset->filename ?></a></td>
                            <td><?= $asset->version ?></td>
                            <td><?= $asset->size ?></td>
                            <td><?= time_elapsed_string(date('Y-m-d H:i:s', strtotime($asset->update_date))) ?></td>
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

    if (!empty($description)) {
        ?>
        <div class="policycloud-marketplace description">
            <?php
            if ($permissions['administrator'] && $description->metadata['approved'] == "0") {
                ?>
                <div class="policycloud-marketplace-notice" id="policycloud-marketplace-description-approval">
                    <p>This asset is not yet accessible from other authorized Marketplace users.</p>
                    <button class="action destructive" data-response="disapprove">Delete</button>
                    <button class="action" data-response="approve">Approve</button>
                </div>
                <?php
            }
            ?>
            <header>
                <div class="title">
                    <h1><?= $description->information['title'] ?>
                        <?php
                        if ($permissions['provider'] || $permissions['administrator']) {
                            ?>
                            <span class="status label <?= ($description->metadata['approved'] == "1") ? 'success' : 'notice' ?>">
                                <?= ($description->metadata['approved'] == "1") ? 'Approved' : 'Pending' ?>
                            </span>
                            <?php
                        }
                        ?>
                    </h1>
                    <?php
                    if ($permissions['provider'] || $permissions['administrator']) {
                        ?>
                        <button class="outlined" data-action="edit">
                            <span class="fas fa-pen"></span> Edit
                        </button>
                        <?php
                    }
                    ?>
                </div>
                <div class="metadata">
                    <span class="provider">
                        <a href="<?= $pages['account_page'] . '?user=' . $description->metadata['provider'] ?>">
                            <?= $description->metadata['provider'] ?>
                        </a>
                    </span>
                    <?php if (!empty($description->information['owner'])) { ?>
                        <span class="owner">
                            &copy; <?= $description->information['owner'] ?>
                        </span>
                    <?php } ?>
                    <span class="type pill">
                        <a href="<?= $pages['archive_page'] . "?type=" . $description->type ?>">
                            <?= $description->type ?>
                        </a>
                    </span>
                    <?php if (!empty($description->information['subtype'])) {
                        if ($description->information['subtype'] != '-') { ?>
                            <span class="sub-type pill">
                                <a href="<?= $pages['archive_page'] . "?subtype=" . $description->information['subtype'] ?>">
                                    <?= $description->information['subtype'] ?>
                                </a>
                            </span>
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
                    <a href="#reviews" class="reviews">
                        <span class="fas fa-star"></span> <?= $description->metadata['reviews']['average_rating'] . ' (' . $description->metadata['reviews']['no_reviews'] . ' reviews)' ?>
                    </a>
                    <span class="views">
                        <span class="fas fa-eye"></span>
                        <?= $description->metadata['views'] ?> views
                    </span>
                    <span class="last-update-date">
                        Last updated <?= time_elapsed_string(date('Y-m-d H:i:s', strtotime($description->metadata['updateDate']))) ?>
                    </span>
                </div>
            </header>
            <div class="content">
                <div class="files <?= ($permissions['authenticated']) ? '' : 'locked' ?>">
                    <h2>Uploads</h2>
                    <?php
                    if ($permissions['authenticated']) {
                        asset_viewer('Files', 'files', $description->assets['files'], (empty($description->assets['files'])));
                        asset_viewer('Videos (Gallery)', 'videos', $description->assets['videos'], (empty($description->assets['videos'])));
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
                    <div class="gallery">
                        <h2>Gallery</h2>
                        <div class="slider">
                            <?php
                            if ($permissions['authenticated']) {
                                if (!empty($description->assets['videos'])) {
                                    foreach ($description->assets['videos'] as $video) { ?>
                                        <div class="item" data-asset-category="videos" data-asset-id="<?= $video->id ?>">
                                            <img class="play-icon" src="<?= get_site_url(null, '/wp-content/plugins/policycloud-marketplace/public/assets/svg/play.svg'); ?>" />
                                            <img class="video-thumbnail" src="
                                            <?=
                                            (PolicyCloud_Marketplace_Public::get_plugin_setting(true, 'marketplace_host')
                                                . '/videos/' . $video->id . '?thumbnail=1'
                                            )
                                            ?>">
                                            <?php
                                            if ($permissions['provider']) {
                                                ?>
                                                <div class="toolbar">
                                                    <span>
                                                        <?= $video->filename ?>
                                                        (<?= $video->size ?>)
                                                    </span>
                                                    <div class="tools">
                                                        <button data-action="delete" data-asset-category="images" data-asset-id="<?= $video->id ?>" class="action outlined">
                                                            Delete
                                                        </button>
                                                    </div>
                                                </div>
                                                <?php
                                            } ?>
                                        </div>
                                        <?php
                                    }
                                }
                                if (!empty($image_blobs)) {
                                    foreach ($image_blobs as $key => $image_blob) { ?>
                                        <div class="item" data-asset-id="<?= $description->assets['images'][$key]->id ?>" data-asset-category="images">
                                            <?php
                                            echo '<img src="data:image/*;base64,' . base64_encode($image_blob) . '" data-asset-category="images" data-asset-id="' . $description->assets['images'][$key]->id . '" draggable="false" />';
                                            if ($permissions['provider'] || $permissions['administrator']) {
                                                ?>
                                                <div class="toolbar">
                                                    <span>
                                                        <?= $description->assets['images'][$key]->filename ?>

                                                        (<?= $description->assets['images'][$key]->size ?>)
                                                    </span>
                                                    <div class="tools">
                                                        <?php if ($description->assets['images'][$key]->id == $description->image_id) { ?>
                                                            <button data-action="remove-default" data-asset-id="<?= $description->assets['images'][$key]->id ?>" class="action outlined">Remove default image</button>
                                                        <?php } else { ?>
                                                            <button data-action="set-default" data-asset-id="<?= $description->assets['images'][$key]->id ?>" class="action outlined">Set as default image</button>
                                                        <?php } ?>
                                                        <button data-action="delete" data-asset-category="images" data-asset-id="<?= $description->assets['images'][$key]->id ?>" class="action outlined">
                                                            Delete
                                                        </button>
                                                    </div>
                                                </div>
                                                <?php
                                            } ?>
                                        </div> <?php
                                    }
                                }
                                if (empty($description->assets['videos']) && empty($description->assets['images'])) {
                                    show_alert('No images or videos were found.', 'notice');
                                }
                            } else {
                                show_lock($pages['login_page'], 'view the image gallery');
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="reviews" id="reviews">
                <h2>Reviews</h2>
                <?php
                if ($permissions['authenticated']) {
                    description_reviews_html(
                        $reviews['content'] ?? [],
                        $reviews['pages'] ?? null,
                        $description->user_review ?? null,
                        $permissions['provider']
                    );
                } else {
                    show_lock($pages['login_page'], 'view reviews for this description');
                }
                ?>
            </div>
            <?php
            if ($permissions['provider'] || $permissions['administrator']) {
                description_editor_html($description, $permissions);
            }
            ?>
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
            <h3><?= ucfirst($id) ?></h3>
            <div class="actions">
                <form action="" class="selector">
                    <label for="sort-by">Sort by</label>
                    <select name="sort-by" data-category="<?= $id ?>">
                        <option value="newest" <?= ((($_GET['sort-by'] ?? '' == 'newest') || empty($_GET['sort-by'])) ? "selected" : "") ?>>Newest</option>
                        <option value="oldest" <?= (($_GET['sort-by'] ?? '' == 'oldest') ? "selected" : "") ?>>Oldest</option>
                        <option value="rating-asc" <?= (($_GET['sort-by'] ?? '' == 'rating-asc') ? "selected" : "") ?>>Highest rated</option>
                        <option value="rating-desc" <?= (($_GET['sort-by'] ?? '' == 'rating-desc') ? "selected" : "") ?>>Lowest rated</option>
                        <?php
                        if ($id == 'descriptions') {
                            ?>
                            <option value="views-asc" <?= (($_GET['sort-by'] ?? '' == 'views-asc') ? "selected" : "") ?>>Most viewed</option>
                            <option value="views-desc" <?= (($_GET['sort-by'] ?? '' == 'views-desc') ? "selected" : "") ?>>Least viewed</option>
                        <?php } ?>
                        <option value="title" <?= (($_GET['sort-by'] ?? '' == 'title') ? "selected" : "") ?>>Title</option>
                    </select>
                    <label for="items-per-page">Items per page</label>
                    <select name="items-per-page" data-category="<?= $id ?>">
                        <option value="5" <?= ((($_GET['items-per-page'] ?? '' == '5')) ? "selected" : "") ?>>5</option>
                        <option value="10" <?= (($_GET['items-per-page'] ?? '' == '10' || empty($_GET['items-per-page'])) ? "selected" : "") ?>>10</option>
                        <option value="25" <?= (($_GET['items-per-page'] ?? '' == '25') ? "selected" : "") ?>>25</option>
                        <option value="50" <?= (($_GET['items-per-page'] ?? '' == '50') ? "selected" : "") ?>>50</option>
                        <option value="100" <?= (($_GET['items-per-page'] ?? '' == '100') ? "selected" : "") ?>>100</option>
                    </select>
                </form>
                <?php
                if (!$visitor && $id == 'descriptions'  && !empty($create_page_url)) {
                    ?>
                    <a id="policycloud-upload" href="<?= $create_page_url ?>" title="Create new"><span class="fas fa-plus"></span> Create new</a>
                <?php } ?>
            </div>
        </header>
        <div class="collection-filters" data-category="<?= $id ?>">
            <div>Filter by type:</div>
        </div>
        <div class="paginated-list" data-category="<?= $id ?>">
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
