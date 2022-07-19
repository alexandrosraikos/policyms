<?php
/**
 * The HTML generating functions for description-related content.
 *
 * @link       https://github.com/alexandrosraikos/policyms/
 * @since      1.0.0
 *
 * @package    PolicyMS
 * @subpackage PolicyMS/public/partials
 */

/**
 * Print the standard HTML of a sorting selector.
 *
 * The `action` attribute is `policyms-sort-content`.
 *
 * @param string $content_type The object class identifier for use in the `content-type` attribute.
 * @param string $selected_option The value of the selected type (must be slugified). Defaults to `newest`.
 * @return string The selector HTML.
 * @throws PolicyMSInvalidDataException On invalid sorting options.
 *
 * @since 2.0.0
 * @author Alexandros Raikos <alexandros@araikos.gr>
 */
function sorting_selector_html( string $content_type, string $selected_option = 'newest' ): string {

	// Populate the sorting options.
	$sorting_options = array(
		'newest' => __( 'Most recent', 'policyms' ),
		'oldest' => __( 'Oldest', 'policyms' ),
		'title'  => __( 'A-Z', 'policyms' ),
	);
	if ( 'PolicyMS_Description' === $content_type ) {
		$sorting_options['views-asc']  = __( 'Least viewed', 'policyms' );
		$sorting_options['views-desc'] = __( 'Most viewed', 'policyms' );
	}
	if ( 'PolicyMS_Review' === $content_type || 'PolicyMS_Description' === $content_type ) {
		$sorting_options['rating-desc'] = __( 'Highest rating', 'policyms' );
		$sorting_options['rating-asc']  = __( 'Lowest rating', 'policyms' );
	}

	// Check for valid data.
	if ( ! in_array( $selected_option, $sorting_options, true ) ) {
		throw new PolicyMSInvalidDataException(
			'The sorting option can only be one of the following: ' . print_r( $sorting_options )
		);
	}

	// Create the HTML form options.
	$html_options = array();
	foreach ( $sorting_options as $option => $option_label ) {
		$selected_label = ( ! empty( $selected_option ) ) ?
			( ( $selected_option === $option ) ? 'selected' : '' ) :
			'';
		$html_options  .= <<<HTML
			<option value="{$option}" {$selected_label}> {$option_label} </option>
		HTML;
	}

	// Translated strings.
	$sorting_label = __( 'Sort by', 'policyms' );

	// Return the entire sorting selector form HTML.
	return <<<HTML
			<form action="policyms-sort-content" content-type="{$content_type}">
				<label for="sorting">{$sorting_label}</label>
				<select name="sorting">
					{$html_options}
				</select>
			</form>
		HTML;
}

/**
 * Print the standard HTML of a page size selector (items per page).
 *
 * The `action` attribute is `policyms-change-page-size`.
 *
 * @param int $selected_size The pre-selected size of the page, if any.
 * @return string The selector HTML.
 * @throws PolicyMSInvalidDataException On invalid page sizes.
 *
 * @since 2.0.0
 * @author Alexandros Raikos <alexandros@araikos.gr>
 */
function sizing_selector_html( int $selected_size = 12 ): string {

	// Populate the available sizing options.
	$size_options = array( 12, 30, 60, 90 );

	// Check for valid data.
	if ( ! in_array( $selected_size, $size_options, true ) ) {
		throw new PolicyMSInvalidDataException(
			'The page size can only be one of the following sizes: ' . print_r( $size_options )
		);
	}

	// Create the HTML form options.
	$html_options = array();
	foreach ( $size_options as $option ) {
		$selected_label = ( ! empty( $selected_option ) ) ?
			( ( $selected_option === $option ) ? 'selected' : '' ) :
			'';
		$html_options  .= <<<HTML
			<option value="{$option}" {$selected_label}> {$option} </option>
		HTML;
	}

	// Translated strings.
	$size_label = __( 'Items per page', 'policyms' );

	// Return the entire sorting selector form HTML.
	return <<<HTML
		<form action="policyms-change-page-size">
			<label for="sizing">{$size_label}</label>
			<select name="sizing">
				{$html_options}
			</select>
		</form>
	HTML;
}


/**
 * The descriptions' archive filter aside HTML.
 *
 * @param PolicyMS_Description_Filters $defaults The default filter values.
 * @param PolicyMS_Description_Filters $selected The selected filter values.
 * @return string
 *
 * @since 1.0.0
 * @author Alexandros Raikos <alexandros@araikos.gr>
 */
function descriptions_archive_filters_html(
	PolicyMS_Description_Filters $defaults,
	PolicyMS_Description_Filters $selected,
	string $nonce ):string {
	$type_radios = '';
	$checked     = false;
	foreach ( PolicyMS_Description::$categories as $type => $label ) {
		$checked           = ( $selected->type ?? '' ) === $type;
		$checked_attribute = $checked ? 'checked' : '';
		$type_radios      .= <<<HTML
			<span>
				<input 
				type="radio" 
				name="type" 
				value="{$type}" 
				{$checked_attribute}/>
				<label for="type">{$label}</label>
			</span>	
		HTML;
	}

	// Prepend the default 'All' option and check if none is checked.
	$checked_attribute = ! $checked ? 'checked' : '';
	$type_radios       = <<<HTML
		<span>
			<input type="radio" name="type" value="" {$checked_attribute} />
			<label for="type">All</label>
		</span>
		{$type_radios}
	HTML;

	return <<<HTML
	<div class="filters">
		<button class="close outlined filters-toggle">Close</button>
		<h2>Filters</h2>
		<p>Select the options below to narrow your search.</p>
		<form>
			<fieldset>
				<input 
					type="text" 
					name="search" 
					placeholder="Search descriptions" 
					value="{$selected->query}" />
			</fieldset>
			<fieldset>
				<h3>Types</h3>
				<div class="types">
					{$type_radios}
				</div>
			</fieldset>
			<fieldset>
				<h3>Views</h3>
				<div class="views">
					<div>
						<input 
							type="number" 
							name="views-gte" 
							placeholder="0" 
							value="{$selected->views_gte}" 
							min="0" 
							max="{$defaults->views_gte}" />
					</div>
					<div>
						<input 
							type="number" 
							name="views-lte" 
							placeholder="{$defaults->views_gte}" 
							value="{$selected->views_lte}" 
							min="0" 
							max="{$defaults->views_gte}" />
					</div>
				</div>
			</fieldset>
			<fieldset>
				<h3>Date</h3>
				<div class="dates">
					<div>
						<label for="update-date-gte">From</label>
						<input 
							type="date" 
							onfocus="(this.type='date')" 
							name="update-date-gte" 
							placeholder="{$defaults->date_gte}" 
							value="{$selected->date_gte}" 
							min="{$defaults->date_gte}" 
							max="{$defaults->date_lte}" />
					</div>
					<div>
						<label for="update-date-lte">To</label>
						<input 
							type="date" 
							name="update-date-lte" 
							placeholder="{$defaults->date_lte}" 
							value="{$selected->date_lte}" 
							min="{$defaults->date_gte}" 
							max="{$defaults->date_lte}" />
					</div>
				</div>
			</fieldset>
			<input type="hidden" name="archive-filtering-nonce" value="{$nonce}"/>
			<button type="submit" class="action">Apply filters</button>
		</form>
		<?php } ?>
	</div>
	HTML;
}

/**
 * The descriptions' grid HTML.
 *
 * @param   array $descriptions The descriptions.
 * @param   bool  $empty_notice Whether to notify on blank descriptions collection.
 *
 * @since   1.0.0
 * @author  Alexandros Raikos <alexandros@araikos.gr>
 * @author  Eleftheria Kouremenou <elkour@unipi.gr>
 */
function descriptions_grid_html( array $descriptions, bool $empty_notice = true ): string {
	if ( ! $descriptions && $empty_notice ) {
		return notice_html( 'No descriptions found.', 'notice' );
	} else {
		$description_cards = '';
		foreach ( $descriptions as $description ) {
			$last_updated_label = time_elapsed_string(
				gmdate(
					'Y-m-d H:i:s',
					strtotime( $description->metadata['updateDate'] )
				)
			);
			$description_cards .= <<<HTML
				<li>
					<a href="{$description->url}">
						<div class="cover">
							<img src="{$description->cover_thumbnail_url}" alt="" />
							<div class="content">
								<h4>
									{$description->information['title']}
								</h4>
								<p>
									{$description->information['short_desc']}
								</p>
							</div>
						</div>
						<div class="metadata">
							<div>
								<div class="last-updated">
									Updated {$last_updated_label}
								</div>
							</div>
							<div>
								<span class="reviews">
									<span class="fas fa-star"></span>
									<span>
										{$description->metadata['reviews']['average_rating']} ({$description->metadata['reviews']['no_reviews']} reviews)
									</span>
									<span class="views">
										<span class="fas fa-eye"></span>
										{$description->metadata['views']} views
									</span>
							</div>
							<div>
								<span class="type pill">
									{$description->type}
								</span>
							</div>
						</div>
					</a>
				</li>
			HTML;
		}
		return <<<HTML
			<div class="policyms-descriptions-grid">
				<ul>
					{$description_cards}
				</ul>
			</div>
		HTML;
	}
}

/**
 * Get the featured descriptions view.
 *
 * @param array $categories The specially formatted categories array.
 * @see PolicyMS_Description_Collection::get_featured()
 * @return string The generated HTML.
 *
 * @since 1.0.0
 * @author Alexandros Raikos <alexandros@araikos.gr>
 */
function featured_descriptions_html( array $categories ): string {
	$top_collections_labels = array(
		ucfirst( $categories['statistics']['top'][0]['collection'] ),
		ucfirst( $categories['statistics']['top'][1]['collection'] ),
		ucfirst( $categories['statistics']['top'][2]['collection'] ),
	);

	// Prepare the description showcasing grids.
	$top_rated_grid   = descriptions_grid_html( $categories['top_rated']->descriptions );
	$most_viewed_grid = descriptions_grid_html( $categories['most_viewed']->descriptions );
	$latest_grid      = descriptions_grid_html( $categories['latest']->descriptions );
	$suggested_grid   = descriptions_grid_html( $categories['suggestions']->descriptions );

	return <<<HTML
		<div class="policyms featured-descriptions">
			<div class="white-container">
				<div class="row statistics">
					<div class="column">
						<figure>
							{$categories['statistics']['sum']}
							<figcaption>Total descriptions</figcaption>
						</figure>
					</div>
					<div class="column">
						<figure>
							{$categories['statistics']['top'][0]['descriptions']}
							<figcaption>
								{$top_collections_labels[0]}
							</figcaption>
						</figure>
					</div>
					<div class="column">
						<figure>
							{$categories['statistics']['top'][1]['descriptions']}
							<figcaption>
								{$top_collections_labels[1]}
							</figcaption>
						</figure>
					</div>
					<div class="column">
						<figure>
							{$categories['statistics']['top'][2]['descriptions']}
							<figcaption>
								{$top_collections_labels[2]}
							</figcaption>
						</figure>
					</div>
				</div>
			</div>
			<h2>Top rated descriptions</h2>
			{$top_rated_grid}
			<h2>Most viewed descriptions</h2>
			{$most_viewed_grid}
			<h2>Latest descriptions</h2>
			{$latest_grid}
			<h2>Suggestions</h2>
			{$suggested_grid}
		</div>
	HTML;
}

/**
 * The descriptions archive HTML.
 *
 * @param   PolicyMS_Description_Collection $collection The description collection.
 * @param   PolicyMS_Description_Filters    $filter_defaults The default values of the filters.
 * @param   PolicyMS_Description_Filters    $selected_filters The selected values of the filters.
 * @param   string                          $archive_filtering_nonce The nonce for the archive filter form.
 * @param   ?string                         $sorting The sorting setting.
 * @param   ?string                         $sizing The sizing setting.
 * @param   int                             $selected_page The selected page.
 * @return string The descriptions archive HTML.
 * @since   1.0.0
 * @author  Alexandros Raikos <alexandros@araikos.gr>
 * @author  Eleftheria Kouremenou <elkour@unipi.gr>
 */
function descriptions_archive_html(
	PolicyMS_Description_Collection $collection,
	PolicyMS_Description_Filters $filter_defaults,
	PolicyMS_Description_Filters $selected_filters,
	string $archive_filtering_nonce,
	string $sorting = null,
	string $sizing = null,
	int $selected_page = 1,
	): string {
	$filters    = descriptions_archive_filters_html(
		$filter_defaults,
		$selected_filters,
		$archive_filtering_nonce
	);
	$sorting    = sorting_selector_html( 'PolicyMS_Description', $sorting );
	$sizing     = sizing_selector_html( $sizing );
	$grid       = descriptions_grid_html( $collection->get_page( $selected_page ) );
	$pagination = ( $collection->is_paginated )
		? show_pagination_html( $collection->total_pages, $selected_page )
		: '';

	return <<<HTML
		<div class="policyms-descriptions-archive inspect">
			{$filters}
			<div class="content">
				<header>
					<button class="filters-toggle tactile">
						<div></div>
						<div></div>
						<div></div>
					</button>
					{$sorting}
					{$sizing}
				</header>
				{$grid}
				{$pagination}
			</div>
		</div>
	HTML;
}


/**
 * Prints the description editing contained form.
 *
 * This function prints a description editing form with a modal flag if a description object is passed.
 * If a description object is not passed, this function acts as a description creation form.
 *
 * @param PolicyMS_Description $description An existing description to edit, if any.
 * @param bool                 $administrator Whether the requester is an administrator.
 * @return string The contained form HTML.
 *
 * @since 1.0.0
 * @author Alexandros Raikos <alexandros@araikos.gr>
 */
function description_editor_html(
	PolicyMS_Description $description = null,
	string $create_redirect = '',
	string $delete_redirect = '',
	bool $administrator = false,
	string $nonce = '',
	string $set_cover_nonce = '',
	string $remove_cover_nonce = '',
	string $delete_asset_nonce = '',
	string $delete_nonce = ''
) {

	/**
	 * Used for editing or creation context.
	 *
	 * @var string The context.
	 */
	$is_editing        = ! empty( $description );
	$context_attribute = $is_editing ? 'editing' : 'creation';

	/**
	 * Appended as a class to parse as a modal in the front-end.
	 *
	 * @var string The modal class indicator.
	 */
	$is_modal = $is_editing ? 'modalize' : '';

	/**
	 * Appended for existing title value.
	 *
	 * @var string The description.
	 */
	$existing_title = $description->information['title'] ?? '';

	/**
	 * Appended for selecting type.
	 *
	 * @var string The select options.
	 */
	$existing_type_options = '';
	$allowed_type_editing  = $is_editing ? 'disabled' : 'required';
	foreach ( PolicyMS_Description::$categories as $type => $label ) {
		$is_selected            = ( ( $description->type ?? '' ) === $type )
			? 'selected' : '';
		$existing_type_options .= <<<HTML
			<option value="{$type}" {$is_selected}>{$label}</option>
		HTML;
	}

	/**
	 * Appended for existing owner value.
	 *
	 * @var string The owner.
	 */
	$existing_owner = empty( $description->information['owner'] )
		? ''
		: $description->information['owner'];

	/**
	 * Appended for existing description value.
	 *
	 * @var string The description.
	 */
	$existing_description = empty( $description->information['description'] )
		? ''
		: $description->information['description'];

	/**
	 * Appended for existing keyword values.
	 *
	 * @var string The comma-separated keywords.
	 */
	$existing_keywords = empty( $description->information['keywords'] )
		? ''
		: implode( ', ', $description->information['keywords'] );

	/**
	 * Appended for creating and managing existing hyperlinks.
	 *
	 * @var string Existing form inputs.
	 */
	$existing_links = '';
	if ( ! empty( $description->links ) ) {
		foreach ( $description->links as $link ) {
			$link_title      = explode( ':', $link, 2 )[0];
			$link_url        = explode( ':', $link, 2 )[1];
			$existing_links .= <<<HTML
				<div>
					<input type="text" name="links-title[]" placeholder="Example" value="{$link_title}" />
					<input type="url" name="links-url[]" placeholder="https://www.example.org/" value="{$link_url}" />
					<button data-action="remove-field" title="Remove this link.">
						<span class="fas fa-times"></span>
					</button>
				</div>
			HTML;
		}
	} else {
		$existing_links .= <<<HTML
			<div>
				<input type="text" name="links-title[]" placeholder="Example" />
				<input type="url" name="links-url[]" placeholder="https://www.example.org/" />
				<button class="remove-field" title="Remove this link."><span class="fas fa-times"></span></button>
			</div>
		HTML;
	}

	/**
	 * Appended for existing comment values.
	 *
	 * @var string The comment.
	 */
	$existing_comment = empty( $description->information['comments'] ) ? '' : $description->information['comments'];

	/**
	 * Appended to allow the upload of assets.
	 *
	 * @var string The additional asset-related form elements.
	 */
	$asset_editor = '';

	/**
	 * Appended to notify of allowed sizes.
	 *
	 * @var string The size limit.
	 */
	$upload_limit_label = ( $administrator ?? false ) ? '1GB' : '100MB';

	foreach ( PolicyMS_Asset_Type::get_supported_types() as $asset_type ) {

		/**
		 * Appended to notify for media which will be part of the gallery.
		 *
		 * @var string The appended gallery string.
		 */
		$type_title = $asset_type->label_plural . ( $asset_type->in_gallery() ? ' (Gallery)' : '' );

		/**
		 * Appended to notify of special media handling.
		 *
		 * @var string The HTML paragraph.
		 */
		$asset_type_notice = $asset_type->notice ? "<p>{$asset_type->notice}</p> " : '';

		/**
		 * Appended to notify the user of the
		 * supported asset extensions.
		 *
		 * @var string The supported extensions string.
		 */
		$extensions = $asset_type->get_extensions();
		if ( $extensions ) {
			$extensions = ' (' . implode( ', ', $asset_type->get_extensions() ) . ')';
		} else {
			$extensions = '';
		}

		/**
		 * Appended to manage existing assets.
		 *
		 * @var string The asset management fields.
		 */
		$existing_assets = '';
		foreach ( ( $description->assets[ $asset_type->id ] ?? array() ) as $asset ) {

			/**
			 * Appended to allow cover setting on image assets.
			 *
			 * @var string The button.
			 */
			$cover_button = '';
			if ( 'image' === $asset_type->id ) {
				if ( $asset->id === $description->image_id ) {
					$cover_button = <<<HTML
						<button 
							data-action="policyms-remove-cover-asset" 
							data-asset-id="{$asset->id}}"
							data-nonce="{$remove_cover_nonce}"
							class="action outlined">
						Remove cover image
						</button>
					HTML;
				} else {
					$cover_button = <<<HTML
						<button 
							data-action="policyms-set-cover-asset"
							data-asset-id="{$asset->id}"
							data-nonce="{$set_cover_nonce}"
							class="action outlined">
						Set as cover image
						</button>
					HTML;
				}
			}

			/**
			 * Appended to manage existing assets.
			 *
			 * @var string The existing asset controls.
			 */
			$existing_assets .= <<<HTML
				<div 
					class="asset-editor" 
					data-asset-type="{$asset_type->id}" 
					data-asset-id="{$asset->id}">
					<div>
						<button 
							class="delete" 
							data-action="policyms-delete-asset"
							data-nonce="{$delete_asset_nonce}">
							<span class="fas fa-times"></span>
						</button>
						{$asset->filename} ({$asset->size})
					</div>
					<label for="{$asset_type->id}-{$asset->id}">
						Replace file{$extensions}:
					</label>
					{$cover_button}
					<input 
						type="file" 
						name="{$asset_type->id}-{$asset->id}" 
						accept="{$asset_type->mimetypes}" 
						multiple />
				</div>
			HTML;
		}

		/**
		 * Appended to allow new assets to be uploaded.
		 *
		 * @var string The new asset upload field.
		 */
		$new_assets = <<<HTML
			Upload {$extensions}:
			<div class="chooser">
				<input 
					type="file" 
					name="{$asset_type->id}[]" 
					accept="{$asset_type->mimetypes}" 
					multiple />
			</div>
		HTML;

		// Append editor sections.
		$asset_editor .= <<<HTML
			<h3>{$type_title}</h3>
			{$asset_type_notice}
			{$existing_assets}
			{$new_assets}
		HTML;
	}

	/**
	 * Appended to allow deletion of an existing description.
	 *
	 * @var string The new asset upload field.
	 */
	$delete_button = '';
	if ( ! $description ) {
		$delete_button = <<<HTML
			<button 
				data-action="delete-description" 
				data-nonce="{$delete_nonce}"
				data-redirect="{$delete_redirect}"
				class="action destructive">
				Delete
			</button>
		HTML;
	}

	/**
	 * Appended to notify of (re)approval.
	 *
	 * @var string The paragraph.
	 */
	$approval_notice = '';
	if ( $is_editing ) {
		$approval_notice = '<p>Please note that after submitting your changes, the description will need to be reapproved by an administrator.</p>';
	} else {
		$approval_notice = '<p>Please note that after submitting, an administrator needs to approve of the content before other users can view it.</p>';
	}

	/**
	 * Appended to allow closing the modal when modalized.
	 *
	 * @var string The button.
	 */
	$cancel_button = $is_editing ? <<<HTML
		<button
			action="policyms-close-modal">
			Cancel
		</button>
	HTML : '';

	return <<<HTML
		<div class="policyms-description-editor {$is_modal}">
			<form 
				data-action="policyms-edit-description"
				data-context="{$context_attribute}"
				data-nonce="{$nonce}"
				data-description-id="{$description->id}"
				data-redirect="{$create_redirect}">
				<fieldset name="basic-information">
					<input type="hidden" name="description-id" value="{$description->id}" />
					<h2>Basic information</h2>
					<p>
						To create a new description, the following fields
						represent basic information that will be visible to others.
					</p>
					<label for="title">
						Title *
					</label>
					<input 
						required 
						name="title" 
						placeholder="Insert a title" 
						type="text" 
						value="{$existing_title}" />
					<label for="type">
						Primary collection type *
					</label>
					<select name="type" {$allowed_type_editing}>
						{$existing_type_options}
					</select>
					<label for="owner">
						Legal owner *
					</label>
					<input 
						required 
						name="owner" 
						placeholder="Insert the legal owner of the object" 
						type="text" 
						value="{$existing_owner}" />
					<label for="description">
						Description *
					</label>
					<textarea 
						name="description" 
						placeholder="Insert a detailed description" 
						style="resize:vertical">
						{$existing_description}
					</textarea>
					<label for="fields-of-use">
						Keywords
					</label>
					<textarea 
						name="keywords" 
						placeholder="Separate multiple keywords using a comma (lorem, ipsum, etc.)">
						{$existing_keywords}
					</textarea>
					<label for="links">Related links</label>
					<div class="links">
						<div>
							{$existing_links}
						</div>
						<button data-action="add-field" title="Add another link.">
							<span class="fas fa-plus"></span> Add link
						</button>
					</div>
				</fieldset>
				<fieldset name="internal-information">
					<h2>Additional information</h2>
					<p>You can include additional comments for authorized visitors. This field is optional.</p>
					<label for="comments">Comments</label>
					<textarea 
						name="comments" 
						placeholder="Insert any additional comments">
						{$existing_comment}
					</textarea>
				</fieldset>
				<fieldset name="assets">
					<h2>Assets</h2>
					<p>Manage your content and upload new files, images and videos up to {$upload_limit_label} in size.</p>
					{$asset_editor}
				</fieldset>
			<div class="error"></div>
			<div class="actions">
				{$delete_button}
				{$cancel_button}
				<button type="submit" class="action">Submit</button>
			</div>
			{$approval_notice}
			</form>
		</div>
	HTML;
}

/**
 * The reviews list.
 *
 * @param   array  $reviews The array of reviews in the given page.
 * @param   string $author_id The author ID.
 * @param   bool   $administrator Whether the requester is an administrator.
 *
 * @since   1.0.0
 * @author  Alexandros Raikos <alexandros@araikos.gr>
 * @author  Eleftheria Kouremenou <elkour@unipi.gr>
 */
function description_reviews_list_html(
	array $reviews,
	string $author_id = null,
	bool $administrator = false ) {
	$reviews_list = '';
	foreach ( $reviews[0] as $review ) {
		$stars = '';
		for ( $i = 0; $i < $review->rating; $i++ ) {
			$stars .= <<<HTML
				<span class="fas fa-star"></span>
			HTML;
		}
		$last_updated     = time_elapsed_string(
			gmdate( 'Y-m-d H:i:s', strtotime( $review->update_date ) )
		);
		$user_account_url = PolicyMS_Public::get_setting( false, 'account_page' )
			. '?user=' . $review->uid;
		$delete_button    = '';
		if ( ! empty( $author_id ) || $administrator ) {
			if ( $review->uid === $author_id || $administrator ) {
				$delete_button .= <<<HTML
				| 
					<button 
						class="action destructive minimal" 
						data-action="delete-review" 
						data-author-id="{$review->uid}">
						Delete
					</button>
				HTML;
				?>
				<?php
			}
		}
		$reviews_list .= <<< HTML
			<li class="review" >
				<div class="rating" >
					{$review->rating}
					<span class="stars">
						{$stars}
					</span>
				</div>
				<div class="comment">
					{$review->comment}
				</div>
				<div class="metadata">
					<span>
						{$last_updated}
					</span>
					<span>
						by <a href="{$user_account_url}">{$review->reviewer}</a>
						{$delete_button}
					</span>
				</div>
			</li>
		HTML;
	}

	return <<<HTML
		<ul>
			{$reviews_list}
		</ul>
	HTML;
}

/**
 * The reviews container for each description.
 *
 * @param   array           $reviews The array of reviews.
 * @param   int             $pages The number of total pages.
 * @param   PolicyMS_Review $existing_review The existing review.
 * @param   array           $permissions The permissions array in
 *                          `['authenticated' => bool, 'administrator' => bool, 'provider' =>bool]` format.
 *
 * @since   1.0.0
 * @author  Alexandros Raikos <alexandros@araikos.gr>
 * @author  Eleftheria Kouremenou <elkour@unipi.gr>
 */
function description_reviews_html(
	array $reviews = null,
	?int $pages = 0,
	PolicyMS_Review $existing_review = null,
	bool $administrator,
	bool $provider,
	string $get_reviews_nonce,
	string $create_review_nonce = '',
	string $delete_review_nonce = ''
	) {

	if ( ! empty( $existing_review ) ) {
		$author_id = $existing_review->uid;
	}

	if ( ! empty( $reviews ) ) {
		$description_reviews_list = description_reviews_list_html(
			$reviews,
			$author_id ?? null,
			$administrator
		);
	} else {
		$description_reviews_list = notice_html( 'No reviews yet.', 'notice' );
	}

	$description_reviews_pagination = '';
	for ( $page = 1; $page < $pages; $page++ ) {
		$active_page                     = $selected_page ?? 1;
		$active_attribute                = ( ( $page === $active_page ) ? 'active' : '' );
		$description_reviews_pagination .= <<<HTML
			<button 
				class="page-selector {$active_attribute}" 
				data-page-number="{$page}" 
				data-nonce="{$get_reviews_nonce}"
				data-action="policyms-description-change-review-page">
				{$page}
			</button>';
		HTML;
	}

	$description_review_editor = '';
	if ( ! $provider ) {
		$update_review_attribute = ! empty( $existing_review ) ? 'update-review' : '';
		$existing_comment        = ! empty( $existing_review ) ? $existing_review->comment : '';
		$last_submitted          = '';
		$delete_review_button    = '';
		if ( ! empty( $existing_review ) ) {
			$existing_review_date = time_elapsed_string(
				gmdate( 'Y-m-d H:i:s', strtotime( $existing_review->update_date ) )
			);
			$last_submitted       = <<<HTML
				<p>
					Last submitted {$existing_review_date}
				</p>
			HTML;
			$delete_review_button = <<<HTML
				<button 
					class="action destructive" 
					data-action="policyms-delete-review" 
					data-author-id="{$existing_review->uid}"
					data-nonce="{$delete_review_nonce}">
					Delete
				</button>
			HTML;
		}
		$review_stars = '';
		for ( $i = 0; $i < 5; $i++ ) {
			$rating            = $i + 1;
			$checked_attribute = ( $rating <= ( $existing_review->rating ?? 0 ) ) ? 'checked' : '';
			$review_stars     .= <<<HTML
			<label>
				<input type="radio" name="rating" value="{$rating}" {$checked_attribute} required />
				<span class="fas fa-star"></span>
			</label>
			HTML;
		}

		$description_review_editor .= <<<HTML
			<form 
				data-nonce="{$create_review_nonce}"
				data-action="policyms-add-review"
				{$update_review_attribute}>
				<label for="comment">Comment</label>
				<textarea name="comment" placeholder="Insert your comment here..">{$existing_comment}</textarea>
				<label for="rating">Rating</label>
				<div class="stars">
					{$review_stars}
				</div>
				{$last_submitted}
				<div class="actions">
					{$delete_review_button}
					<button class="action" type="submit">Submit</button>
				</div>
			</form>
		HTML;
	}

	return <<<HTML
		<div class="policyms-description-reviews">
			{$description_reviews_list}
			<nav class="pagination">
				{$description_reviews_pagination}
			</nav>
			{$description_review_editor}
		</div>
	HTML;
}

/**
 * Print the asset HTML.
 *
 * @param   PolicyMS_Description $description The description.
 * @param   string               $account_page_url The base URL of the account page.
 * @param   string               $archive_page_url The base URL of the archive page.
 * @param   string               $authentication_page_url The base URL of the authentication page.
 * @param   bool                 $authenticated Whether the requesting user is authenticated.
 * @param   bool                 $administrator Whether the requesting user is an administrator.
 * @param   bool                 $provider Whether the requesting user is a provider.
 * @param   array                $reviews The array of reviews as returned by @see description_reviews_html.
 * @param   array                $image_blobs The array of image blob data.
 * @param   string               $asset_download_nonce The array of image blob data.
 *
 * @since   1.0.0
 * @author  Alexandros Raikos <alexandros@araikos.gr>
 * @author  Eleftheria Kouremenou <elkour@unipi.gr>
 */
function description_html(
		PolicyMS_Description $description,
		string $account_page_url,
		string $archive_page_url,
		string $authentication_page_url,
		bool $authenticated,
		bool $administrator,
		bool $provider,
		array $reviews = array(),
		array $image_blobs = array(),
		string $asset_download_nonce = '',
		string $get_reviews_nonce = '',
		string $create_review_nonce = '',
		string $delete_review_nonce = '',
		string $approval_nonce = '',
		string $editing_nonce = '',
		string $set_cover_nonce = '',
		string $remove_cover_nonce = '',
		string $delete_asset_nonce = '',
		string $delete_nonce = '',
		string $delete_redirect = '',
		string $content_host = '',
	) {

	/**
	 * The interactive file information table.
	 *
	 * @var callable $asset_information_table Prints the HTML.
	 * @since 2.0.0
	 */
	$asset_information_table = function(
		string $title,
		string $category_slug,
		array $assets,
		string $asset_download_nonce
		): string {

		// Attribute preparation.
		$disabled_attribute  = ( empty( $assets ) ) ? 'disabled' : '';
		$collapsed_attribute = ( empty( $assets ) ) ? 'collapsed' : '';

		// Expand / collapse control.
		$interactive_title = <<<HTML
			<button 
				data-asset-type="{$category_slug}" 
				data-action="policyms-toggle-file-table-vibility"
				class="action" 
				{$disabled_attribute}>
				{$title}
			</button>
		HTML;

		$asset_information_cells = '';
		if ( ! empty( $assets ) ) {
			foreach ( $assets as $asset ) {
				$formatted_updated_time   = time_elapsed_string(
					gmdate( 'Y-m-d H:i:s', strtotime( $asset->update_date ) )
				);
				$asset_information_cells .= <<<HTML
					<tr data-asset-type="{$category_slug}" data-asset-identifier="{$asset->id}">
						<td>
							<a 
								data-nonce="{$asset_download_nonce}"
								class="download">
								{$asset->filename}
							</a>
						</td>
						<td>
							{$asset->version}
						</td>
						<td>
							{$asset->size}
						</td>
						<td>
							{$formatted_updated_time}
						</td>
					</tr>
				HTML;
			}
		} else {
			$asset_information_cells .= '<tr><td colspan="4">';
			$asset_information_cells .= notice_html( 'Nothing to display yet.', 'notice' );
			$asset_information_cells .= '</td></tr>';
		}

		return <<<HTML
			<div class="policyms-asset-information-table" {$collapsed_attribute}>
				{$interactive_title}
			<table>
				<tr>
					<th>Name</th>
					<th>Version</th>
					<th>Size</th>
					<th>Added</th>
				</tr>
				{$asset_information_cells}
			</table>
			</div>
		HTML;
	};

	/**
	 * Approval / Rejection notice for administrators.
	 *
	 * @var string $administrator_approval The printed HTML.
	 * @since 2.0.0
	 */
	$administrator_approval = '';
	if ( $administrator && ! $description->is_approved() ) {
		$administrator_approval_label         = __( 'This description requires manual approval.', 'policyms' );
		$administrator_rejection_reason_label = __( 'What is the reason for rejection?', 'policyms' );
		$administrator_approval               = <<<HTML
			<form>
				<p>{$administrator_approval_label}.</p>
				<div class="hidden">
					<label for="rejection-reason">
						{$administrator_rejection_reason_label}
					</label>
					<textarea name="rejection-reason" required></textarea>
				</div>
				<button 
					data-action="policyms-reject-description"
					data-nonce="{$approval_nonce}">
					Approve
				</button>
				<button 
					data-action="policyms-approve-description"
					data-nonce="{$approval_nonce}">
					Approve
				</button>
			</form>
		HTML;
	}

	$approval_tag = '';
	if ( $provider || $administrator ) {
		$status       = ( $description->is_approved() ) ? 'approved' : 'pending';
		$status_label = ( $description->is_approved() ) ? __( 'Approved', 'policyms' ) : __( 'Pending', 'policyms' );
		$approval_tag = <<<HTML
			<span class="policyms-status-label" status="{$status}">
				{$status_label}
			</span>
		HTML;
	}

	$description_metadata_provider = '';
	if ( $authenticated ) {
		$description_metadata_provider = <<<HTML
			<span class="provider">
				<a href="{$account_page_url}?user={$description->metadata['provider']}">
					{$description->metadata['provider_name']}
				</a>
			</span>
		HTML;
	}

	$description_metadata_keywords = '';
	if ( ! empty( $description->information['keywords'] ) ) {
		$description_metadata_keyword_urls = '';
		foreach ( $description->information['keywords'] as $keyword ) {
			$description_metadata_keyword_urls .= <<<HTML
				<a href="{$archive_page_url}?keyword={$keyword}">{$keyword}</a>
			HTML;
		}
		$description_metadata_keywords = <<<HTML
			<span class="keywords">
				{$description_metadata_keyword_urls}
			</span>
		HTML;
	}

	$formatted_updated_time = time_elapsed_string(
		gmdate( 'Y-m-d H:i:s', strtotime( $description->metadata['updateDate'] ) )
	);

	/**
	 * The description's side bar asset information table.
	 *
	 * @var string $description_sidebar_assets The container HTML table.
	 * @since 2.0.0
	 */
	$description_sidebar_assets = '';
	if ( $authenticated ) {
		foreach ( $description->assets as $category_slug => $assets ) {
			$description_sidebar_assets .= $asset_information_table(
				ucfirst( $category_slug ),
				$category_slug,
				$assets,
				$asset_download_nonce
			);
		}
	} else {
		$description_sidebar_assets .= show_lock(
			$authentication_page_url
		);
	}

	/**
	 * The description's side bar asset additional comments section.
	 *
	 * @var string $description_sidebar_assets The container HTML table.
	 * @since 2.0.0
	 */
	$description_sidebar_comments = '';
	if ( $authenticated && ! empty( $description->information['comments'] ) ) {
		$description_sidebar_comments .= <<<HTML
			<div class="comments">
				<h2>Additional information</h2>
				<p>{$description->information['comments']}</p>
			</div>
		HTML;
	}

	$description_text = $authenticated ?
		$description->information['description'] :
		$description->information['short_desc'];

	$description_links = '';
	if ( ! empty( $description->links[0] ) ) {
		$description_links .= '<ul>';
		foreach ( $description->links as $link ) {
			$url                = explode( ':', $link, 2 )[1];
			$label              = explode( ':', $link, 2 )[0];
			$description_links .= <<<HTML
				<li>
					<a class="button outlined" href="{$url}" target="blank">
						{$label}
					</a>
				</li>
			HTML;
		}
		$description_links .= '</ul>';
	}

	$description_gallery_slider = '';
	if ( $authenticated ) {
		if ( ! empty( $description->assets['videos'] ) ) {
			foreach ( $description->assets['videos'] as $video ) {
				$play_icon_src = get_site_url(
					null,
					'/wp-content/plugins/policyms/public/assets/svg/play.svg'
				);
				$thumbnail_url = $content_host
					. '/videos/' . $video->id . '?thumbnail=1';
				$toolbar       = '';
				if ( $provider || $administrator ) {
					$toolbar = <<<HTML
						<div class="toolbar">
							<span>
								{$video->filename} ({$video->size})
							</span>
							<div class="tools">
								<button 
									data-action="delete" 
									data-asset-category="videos" 
									data-asset-id="{$video->id}" 
									class="action outlined">
									Delete
								</button>
							</div>
						</div>
					HTML;
				}
				$description_gallery_slider .= <<<HTML
					<div class="item" data-asset-category="videos" data-asset-id="{$video->id}">
						<img class="play-icon" src="{$play_icon_src}" />
						<img class="video-thumbnail" src="{$thumbnail_url}">
						{$toolbar}
					</div>
				HTML;
			}
		}
		if ( ! empty( $image_blobs ) ) {
			foreach ( $image_blobs as $key => $image_blob ) {
				$thumbnail_blob = base64_encode( $image_blob );
				$toolbar        = '';
				if ( $provider || $administrator ) {
					if ( $description->assets['images'][ $key ]->id === $description->image_id ) {
						$toolbar_cover_action = <<<HTML
							<button 
								data-action="remove-default" 
								data-asset-id="{$description->assets['images'][ $key ]->id}"
								class="action outlined">
							Remove cover image
							</button>
						HTML;
					} else {
						$toolbar_cover_action = <<<HTML
							<button 
								data-action="set-default"
								data-asset-id="{$description->assets['images'][ $key ]->id}"
								class="action outlined">
							Set as cover image
							</button>
						HTML;
					}
					$toolbar = <<<HTML
						<div class="toolbar">
							<span>
								{$description->assets['images'][ $key ]->filename} ({$description->assets['images'][ $key ]->size})
							</span>
							<div class="tools">
								{$toolbar_cover_action}
								<button
									data-action="delete"
									data-asset-category="images"
									data-asset-id="{$description->assets['images'][ $key ]->id}"
									class="action outlined">
									Delete
								</button>
							</div>
						</div>
					HTML;
				}
				$description_gallery_slider .= <<<HTML
					<div 
						class="item" 
						data-asset-id="{$description->assets['images'][ $key ]->id}" 
						data-asset-category="images">
						<img 
							src="data:image/*;base64,{$thumbnail_blob}" 
							data-asset-category="images" 
							data-asset-id="{$description->assets['images'][ $key ]->id}" 
							draggable="false"/>
						{$toolbar}
					</div> 
				HTML;
			}
		}
		if ( empty( $description->assets['videos'] ) && empty( $description->assets['images'] ) ) {
			$description_gallery_slider .= notice_html( 'No images or videos were found.', 'notice' );
		}
	} else {
		$description_gallery_slider = show_lock( $authentication_page_url );
	}

	if ( $authenticated ) {
		$description_review_list = description_reviews_html(
			$reviews['content'] ?? array(),
			$reviews['pages'] ?? null,
			$description->user_review ?? null,
			$administrator,
			$provider,
			$get_reviews_nonce,
			$create_review_nonce,
			$delete_review_nonce
		);
	} else {
		$description_review_list = show_lock( $authentication_page_url, );
	}

	$description_editor = '';
	if ( $provider || $administrator ) {
		$description_editor .= description_editor_html(
			$description,
			'',
			$delete_redirect,
			$administrator,
			$editing_nonce,
			$set_cover_nonce,
			$remove_cover_nonce,
			$delete_asset_nonce,
			$delete_nonce
		);
	}

	$edit_button = '';
	if ( $provider || $administrator ) {
		$edit_button = <<<HTML
			<button 
				class="outlined" 
				data-action="policyms-edit-description">
				<span class="fas fa-pen"></span> Edit
			</button>
		HTML;
	}

	return <<<HTML
		<div class="policyms-description-single">
			{$administrator_approval}
			<header>
				<div class="title">
					<h1>
						{$description->information['title']}
					</h1>
					{$approval_tag}
					{$edit_button}
				</div>
				<div class="metadata">
					{$description_metadata_provider}
					<span class="owner">
						&copy; {$description->information['owner']}
					</span>
					<span class="type pill">
						<a href="{$archive_page_url}?type={$description->type}">
							{$description->type}
						</a>
					</span>
					{$description_metadata_keywords}
					<a href="#reviews" class="reviews">
						<span class="fas fa-star"></span> {$description->metadata['reviews']['average_rating']} ({$description->metadata['reviews']['no_reviews']} reviews)'; ?>
					</a>
					&nbsp;
					<span class="views">
						<span class="fas fa-eye"></span>
						{$description->metadata['views']} views
					</span>
					<span class="last-updated">
						Last updated {$formatted_updated_time}
					</span>
				</div>
			</header>
			<div class="content">
				<aside>
					<h2>Uploads</h2>
					{$description_sidebar_assets}
					{$description_sidebar_comments}
				</aside>
				<div class="information">
					<h2>Description</h2>
					<div class="description">
						<p>{$description_text}</p>
						{$description_links}
						<div 
							class="gallery"
							data-content-host="{$content_host}">
							<h2>Gallery</h2>
							<div class="slider">
								{$description_gallery_slider}
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="reviews" id="reviews">
				<h2>Reviews</h2>
				{$description_review_list}
			</div>
			{$description_editor}
		</div>
	HTML;
}
