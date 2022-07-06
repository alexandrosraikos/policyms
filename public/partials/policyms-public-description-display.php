<?php

/**
 *
 * Print the assets grid HTML.
 *
 * @param   array  $assets The PolicyMS API assets.
 * @param   string $asset_url The asset page URL.
 *
 * @since   1.0.0
 * @author  Alexandros Raikos <araikos@unipi.gr>
 * @author  Eleftheria Kouremenou <elkour@unipi.gr>
 */
function descriptions_grid_html( array $descriptions, string $description_url ) {
	if ( empty( $descriptions ) ) {
		echo show_alert( 'No assets found.', 'notice' );
	} else {
		?>
		<div class="policyms descriptions-grid">
			<ul>
				<?php
				foreach ( $descriptions as $description ) {
					?>
					<li>
						<a href="<?php echo $description_url . '?did=' . $description->id; ?>">
							<div class="cover">
								<img src="
									<?php
									echo ( PolicyMS_Public::get_plugin_setting( true, 'marketplace_host' )
										. '/descriptions/image/' . $description->id
									)
									?>
									" alt="" />
								<div class="content">
									<h4>
										<?php echo $description->information['title']; ?>
									</h4>
									<p>
										<?php echo $description->information['short_desc']; ?>
									</p>
								</div>
							</div>
							<div class="metadata">
								<div>
									<div class="last-updated">
										Updated
										<?php
										echo time_elapsed_string(
											date(
												'Y-m-d H:i:s',
												strtotime( $description->metadata['updateDate'] )
											)
										)
										?>
									</div>
								</div>
								<div>
									<span class="reviews">
										<span class="fas fa-star"></span>
										<span>
											<?php
											echo ( $description->metadata['reviews']['average_rating']
												. ' (' . $description->metadata['reviews']['no_reviews'] . ' reviews)'
											)
											?>
										</span>
										<span class="views">
											<span class="fas fa-eye"></span>
											<?php echo $description->metadata['views']; ?> views
										</span>
								</div>
								<div>
									<span class="type pill">
										<?php echo $description->type; ?>
									</span>
									<?php
									if ( ! empty( $description->information['subtype'] ) ) {
										?>
										<span class="sub-type pill">
											<?php echo $description->information['subtype']; ?>
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

function featured_descriptions_html( array $categories, string $description_page ): void {
	?>
	<div class="policyms featured-descriptions">
		<div class="white-container">
			<div class="row statistics">
				<div class="column">
					<figure>
						<?php echo $categories['statistics']['sum']; ?>
						<figcaption>Total descriptions</figcaption>
					</figure>
				</div>
				<div class="column">
					<figure>
						<?php echo $categories['statistics']['top'][0]['descriptions']; ?>
						<figcaption>
							<?php echo ucfirst( $categories['statistics']['top'][0]['collection'] ); ?>
						</figcaption>
					</figure>
				</div>
				<div class="column">
					<figure>
						<?php echo $categories['statistics']['top'][1]['descriptions']; ?>
						<figcaption>
							<?php echo ucfirst( $categories['statistics']['top'][1]['collection'] ); ?>
						</figcaption>
					</figure>
				</div>
				<div class="column">
					<figure>
						<?php echo $categories['statistics']['top'][2]['descriptions']; ?>
						<figcaption>
							<?php echo ucfirst( $categories['statistics']['top'][2]['collection'] ); ?>
						</figcaption>
					</figure>
				</div>
			</div>
		</div>
		<?php
		if ( ! empty( $categories['top_rated'][0] ) ) {
			?>
			<h2>Top rated descriptions</h2>
			<?php
			descriptions_grid_html( $categories['top_rated'][0], $description_page );
		}
		if ( ! empty( $categories['most_viewed'][0] ) ) {
			?>
			<h2>Most viewed descriptions</h2>
			<?php
			descriptions_grid_html( $categories['most_viewed'][0], $description_page );
		}
		if ( ! empty( $categories['most_viewed'][0] ) ) {
			?>
			<h2>Latest descriptions</h2>
			<?php
			descriptions_grid_html( $categories['latest'][0], $description_page );
		}
		if ( ! empty( $categories['most_viewed'][0] ) ) {
			?>
			<h2>Suggestions</h2>
			<?php
			descriptions_grid_html( $categories['suggestions'][0], $description_page );
		}
		?>
	</div>
	<?php
}

function descriptions_archive_filters_html( $filters ) {
	?>
	<div class="filters">
		<button class="close outlined filters-toggle">Close</button>
		<h2>Filters</h2>
		<?php
		if ( empty( $filters ) ) {
			show_alert( 'Filters are currently unavailable.', 'notice' );
		} else {
			?>
		<p>Select the options below to narrow your search.</p>
		<form>
			<fieldset>
				<input type="text" name="search" placeholder="Search descriptions" value="<?php echo $_GET['search'] ?? ''; ?>" />
			</fieldset>
			<fieldset>
				<h3>Types</h3>
				<div class="types">
					<span>
						<input type="radio" name="type" value="" <?php echo ( empty( $_GET['type'] ) ) ? 'checked' : ''; ?> />
						<label for="type">All</label>
					</span>
					<span>
						<input type="radio" name="type" value="algorithms" <?php echo ( ( $_GET['type'] ?? '' ) == 'algorithms' ) ? 'checked' : ''; ?> />
						<label for="type">Algorithms</label>
					</span>
					<span>
						<input type="radio" name="type" value="tools" <?php echo ( ( $_GET['type'] ?? '' ) == 'tools' ) ? 'checked' : ''; ?> />
						<label for="type">Tools</label>
					</span>
					<span>
						<input type="radio" name="type" value="policies" <?php echo ( ( $_GET['type'] ?? '' ) == 'policies' ) ? 'checked' : ''; ?> />
						<label for="type">Policies</label>
					</span>
					<span>
						<input type="radio" name="type" value="datasets" <?php echo ( ( $_GET['type'] ?? '' ) == 'datasets' ) ? 'checked' : ''; ?> />
						<label for="type">Datasets</label>
					</span>
					<span>
						<input type="radio" name="type" value="webinars" <?php echo ( ( $_GET['type'] ?? '' ) == 'webinars' ) ? 'checked' : ''; ?> />
						<label for="type">Webinars</label>
					</span>
					<span>
						<input type="radio" name="type" value="tutorials" <?php echo ( ( $_GET['type'] ?? '' ) == 'tutorials' ) ? 'checked' : ''; ?> />
						<label for="type">Tutorials</label>
					</span>
					<span>
						<input type="radio" name="type" value="documents" <?php echo ( ( $_GET['type'] ?? '' ) == 'documents' ) ? 'checked' : ''; ?> />
						<label for="type">Documents</label>
					</span>
					<span>
						<input type="radio" name="type" value="externals" <?php echo ( ( $_GET['type'] ?? '' ) == 'externals' ) ? 'checked' : ''; ?> />
						<label for="type">Externals</label>
					</span>
					<span>
						<input type="radio" name="type" value="other" <?php echo ( ( $_GET['type'] ?? '' ) == 'other' ) ? 'checked' : ''; ?> />
						<label for="type">Other</label>
					</span>
				</div>
			</fieldset>
			<fieldset>
				<h3>Views</h3>
				<div class="views">
					<div>
						<input type="number" name="views-gte" placeholder="0" value="<?php echo $_GET['views-gte'] ?? ''; ?>" min="0" max="<?php echo $filters['max_views']; ?>" />
					</div>
					<div>
						<input type="number" name="views-lte" placeholder="<?php echo $filters['max_views']; ?>" value="<?php echo $_GET['views-lte'] ?? ''; ?>" min="0" max="<?php echo $filters['max_views']; ?>" />
					</div>
				</div>
			</fieldset>
			<fieldset>
				<h3>Date</h3>
				<div class="dates">
					<div>
						<label for="update-date-gte">From</label>
						<input type="date" onfocus="(this.type='date')" name="update-date-gte" placeholder="<?php echo date( 'Y-m-d', strtotime( $filters['oldest'] ) ); ?>" value="<?php echo $_GET['update-date-gte'] ?? ''; ?>" min="<?php echo date( 'Y-m-d', strtotime( $filters['oldest'] ) ); ?>" max="<?php echo date( 'Y-m-d' ); ?>" />
					</div>
					<div>
						<label for="update-date-lte">To</label>
						<input type="date" name="update-date-lte" placeholder="<?php echo date( 'Y-m-d' ); ?>" value="<?php echo $_GET['update-date-lte'] ?? ''; ?>" min="<?php echo date( 'Y-m-d', strtotime( $filters['oldest'] ) ); ?>" max="<?php echo date( 'Y-m-d' ); ?>" />
					</div>
				</div>
			</fieldset>
			<button type="submit" class="action">Apply filters</button>
		</form>
		<?php } ?>
	</div>
	<?php
}

/**
 * Print the assets archive HTML.
 *
 * @param   array $assets The PolicyMS API assets.
 * @param   array $args Various printing arguments.
 *
 * @since   1.0.0
 * @author  Alexandros Raikos <araikos@unipi.gr>
 * @author  Eleftheria Kouremenou <elkour@unipi.gr>
 */
function descriptions_archive_html( array $descriptions, array $filters, string $description_page ) {
	?>
	<div class="policyms descriptions archive inspect">
		<?php echo descriptions_archive_filters_html( $filters ); ?>
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
							<option value="newest" <?php echo ( ( ( ( $_GET['sort-by'] ?? '' ) == 'newest' ) || empty( $_GET['sort-by'] ) ) ? 'selected' : '' ); ?>>Newest</option>
							<option value="oldest" <?php echo ( ( ( $_GET['sort-by'] ?? '' ) == 'oldest' ) ? 'selected' : '' ); ?>>Oldest</option>
							<option value="rating-desc" <?php echo ( ( ( $_GET['sort-by'] ?? '' ) == 'rating-desc' ) ? 'selected' : '' ); ?>>Highest rated</option>
							<option value="rating-asc" <?php echo ( ( ( $_GET['sort-by'] ?? '' ) == 'rating-asc' ) ? 'selected' : '' ); ?>>Lowest rated</option>
							<option value="views-desc" <?php echo ( ( ( $_GET['sort-by'] ?? '' ) == 'views-desc' ) ? 'selected' : '' ); ?>>Most viewed</option>
							<option value="views-asc" <?php echo ( ( ( $_GET['sort-by'] ?? '' ) == 'views-asc' ) ? 'selected' : '' ); ?>>Least viewed</option>
							<option value="title" <?php echo ( ( ( $_GET['sort-by'] ?? '' ) == 'title' ) ? 'selected' : '' ); ?>>Title</option>
						</select>
					</fieldset>
					<fieldset>
						<label for="items-per-page">Items per page</label>
						<select name="items-per-page">
							<option value="10" <?php echo ( ( ( ( $_GET['items-per-page'] ?? '' ) == 10 ) || empty( $_GET['sort-by'] ) ) ? 'selected' : '' ); ?>>10</option>
							<option value="25" <?php echo ( ( ( $_GET['items-per-page'] ?? '' ) == '25' ) ? 'selected' : '' ); ?>>25</option>
							<option value="50" <?php echo ( ( ( $_GET['items-per-page'] ?? '' ) == '50' ) ? 'selected' : '' ); ?>>50</option>
							<option value="100" <?php echo ( ( ( $_GET['items-per-page'] ?? '' ) == '100' ) ? 'selected' : '' ); ?>>100</option>
						</select>
					</fieldset>
				</form>
			</header>
			<?php
			if ( ! empty( $descriptions ) ) {
				?>
				<div class="description-gallery">
					<?php
					descriptions_grid_html( $descriptions['content'][0], $description_page );
					?>
				</div>
				<nav class="pagination">
					<?php
					for ( $page = 1; $page < $descriptions['pages'] + 1; $page++ ) {
						$activePage = $_GET['descriptions-page'] ?? 1;
						echo '<button class="page-selector ' . ( ( $activePage == ( $page ) ) ? 'active' : '' ) . '" data-page-number="' . $page . '">' . ( $page ) . '</button>';
					}
					?>
				</nav>
				<?php
			} else {
				show_alert( 'No descriptions found.', 'notice' );
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
 * @param PolicyMS_Description|null $description
 * @return void
 */
function description_editor_html( PolicyMS_Description $description = null, array $permissions = null ): void {
	// Print the main editor HTML.
	?>
	<div class="policyms description editor <?php echo ! empty( $description ) ? 'modalize' : ''; ?>">
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
				<input required name="title" placeholder="Insert a title" type="text" value="<?php echo $description->information['title'] ?? ''; ?>" />
				<label for="type">
					Primary collection type *
				</label>
				<select name="type" required>
					<option value="algorithms" <?php echo ( ( $description->type ?? '' ) == 'algorithms' ) ? 'selected' : ''; ?>>Algorithms</option>
					<option value="tools" <?php echo ( ( $description->type ?? '' ) == 'tools' ) ? 'selected' : ''; ?>>Tools</option>
					<option value="policies" <?php echo ( ( $description->type ?? '' ) == 'policies' ) ? 'selected' : ''; ?>>Policies</option>
					<option value="datasets" <?php echo ( ( $description->type ?? '' ) == 'datasets' ) ? 'selected' : ''; ?>>Datasets</option>
					<option value="webinars" <?php echo ( ( $description->type ?? '' ) == 'webinars' ) ? 'selected' : ''; ?>>Webinars</option>
					<option value="tutorials" <?php echo ( ( $description->type ?? '' ) == 'tutorials' ) ? 'selected' : ''; ?>>Tutorials</option>
					<option value="documents" <?php echo ( ( $description->type ?? '' ) == 'documents' ) ? 'selected' : ''; ?>>Documents</option>
					<option value="externals" <?php echo ( ( $description->type ?? '' ) == 'externals' ) ? 'selected' : ''; ?>>Externals</option>
					<option value="other" <?php echo ( ( $description->type ?? '' ) == 'other' ) ? 'selected' : ''; ?>>Other</option>
				</select>
				<label for="subtype">
					Secondary collection type
				</label>
				<input name="subtype" placeholder="Insert a secondary category" type="text" value="<?php echo empty( $description->information['subtype'] ) ? '' : $description->information['subtype']; ?>" />
				<label for="owner">
					Legal owner *
				</label>
				<input required name="owner" placeholder="Insert the legal owner of the object" type="text" value="<?php echo empty( $description->information['owner'] ) ? '' : $description->information['owner']; ?>" />
				<label for="description">
					Description *
				</label>
				<textarea name="description" placeholder="Insert a detailed description" style="resize:vertical"><?php echo empty( $description->information['description'] ) ? '' : $description->information['description']; ?></textarea>
				<label for="fields-of-use">
					Fields of usage
				</label>
				<textarea name="fields-of-use" placeholder="Separate multiple fields of usage using a comma (lorem, ipsum, etc.)"><?php echo empty( $description->information['fieldOfUse'] ) ? '' : implode( ', ', $description->information['fieldOfUse'] ); ?></textarea>
				<label for="links">Related links</label>
				<div class="links">
					<div>
						<?php
						if ( ! empty( $description->links ) ) {
							foreach ( $description->links as $link ) {
								$link_title = explode( ':', $link, 2 )[0];
								$link_url   = explode( ':', $link, 2 )[1];
								?>
								<div>
									<input type="text" name="links-title[]" placeholder="Example" value="<?php echo $link_title; ?>" />
									<input type="url" name="links-url[]" placeholder="https://www.example.org/" value="<?php echo $link_url; ?>" />
									<button class="remove-field" title="Remove this link.">
										<span class="fas fa-times"></span>
									</button>
								</div>
								<?php
							}
						} else {
							?>
							<div>
								<input type="text" name="links-title[]" placeholder="Example" />
								<input type="url" name="links-url[]" placeholder="https://www.example.org/" />
								<button class="remove-field" title="Remove this link."><span class="fas fa-times"></span></button>
							</div>
							<?php
						}
						?>
					</div>
					<button class="add-field" title="Add another link."><span class="fas fa-plus"></span> Add link</button>
				</div>
			</fieldset>
			<fieldset name="internal-information">
				<h2>Additional information</h2>
				<p>You can include additional comments for authorized visitors. This field is optional.</p>
				<label for="comments">Comments</label>
				<textarea name="comments" placeholder="Insert any additional comments"><?php echo empty( $description->information['comments'] ) ? '' : $description->information['comments']; ?></textarea>
			</fieldset>
			<?php
			if ( ! empty( $description ) ) {
				?>
				<fieldset name="uploads">
					<h2>Uploads</h2>
					<p>Manage your content and upload new files, images and videos up to <?php echo ( $permissions['administrator'] ?? false ) ? '1GB' : '100MB'; ?> in size.</p>
					<?php
					foreach ( $description->assets as $category => $assets ) {
						$upload_notice = ( $category == 'images' ) ? ' (supported file types: jpg, png)' : '';
						$upload_notice = ( $category == 'videos' ) ? ' (supported file types: mp4, ogg, webm)' : $upload_notice;
						switch ( $category ) {
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
							if ( $category == 'images' || $category == 'videos' ) {
								echo ucfirst( $category ) . ' (Gallery)';
							} else {
								echo ucfirst( $category );
							}
							?>
						</h3>
						<?php
						if ( $category == 'videos' ) {
							echo '
                                <p>
                                    Uploaded gallery videos are publicly accessible. Please do not include sensitive or protected information.
                                </p>
                                ';
						}
						?>
						<?php
						if ( ! empty( $assets ) ) {
							foreach ( $assets as $asset ) {
								?>
								<div class="asset-editor" data-asset-type="<?php echo $category; ?>" data-asset-id="<?php echo $asset->id; ?>">
									<div>
										<button class="delete" data-asset-category="<?php echo $category; ?>" data-asset-id="<?php echo $asset->id; ?>" data-action="delete">
											<span class="fas fa-times"></span>
										</button>
										<?php echo $asset->filename . ' (' . $asset->size . ')'; ?>
									</div>
									<label for="<?php echo $category . '-' . $asset->id; ?>">
										Replace file<?php echo $upload_notice; ?>:
									</label>
									<input type="file" name="<?php echo $category . '-' . $asset->id; ?>" accept="<?php echo $allowed_mimetypes; ?>" multiple />
								</div>
								<?php
							}
						}
						?>
						Upload new files<?php echo $upload_notice; ?>:
						<div class="chooser">
							<input type="file" name="<?php echo $category; ?>[]" accept="<?php echo $allowed_mimetypes; ?>" multiple />
						</div>
						<?php
					}
					?>
				</fieldset>
			<?php } ?>
			<div class="error"></div>
			<div class="actions">
				<?php
				if ( ! empty( $description ) ) {
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

function description_reviews_list_html( array $reviews, string $author_id = null, bool $administrator = false ) {
	?>
	<ul>
		<?php
		foreach ( $reviews[0] as $review ) {
			?>
			<li class="review">
				<div class="rating">
					<?php echo $review->rating; ?>
					<span class="stars">
						<?php
						for ( $i = 0; $i < $review->rating; $i++ ) {
							?>

							<span class="fas fa-star"></span>
							<?php
						}
						?>
					</span>
				</div>
				<div class="comment">
					<?php echo $review->comment; ?>
				</div>
				<div class="metadata">
					<span>
						<?php echo time_elapsed_string( date( 'Y-m-d H:i:s', strtotime( $review->update_date ) ) ); ?>
					</span>
					<span>
						by <a href="<?php echo PolicyMS_Public::get_plugin_setting( false, 'account_page' ) . '?user=' . $review->uid; ?>"><?php echo $review->reviewer; ?></a>
						<?php
						if ( ! empty( $author_id ) || $administrator ) {
							if ( $review->uid == $author_id || $administrator ) {
								?>
								| <button class="action destructive minimal" data-action="delete-review" data-author-id="<?php echo $review->uid; ?>">Delete</button>
								<?php
							}
						}
						?>
					</span>
				</div>
			</li>
		<?php } ?>
	</ul>
	<?php
}

function description_reviews_html( array $reviews = null, ?int $pages = 0, PolicyMS_Review $existing_review = null, array $permissions ) {

	if ( ! empty( $existing_review ) ) {
		$author_id = $existing_review->uid;
	}

	?>
	<div class="policyms reviews">
		<?php
		if ( ! empty( $reviews ) ) {
			description_reviews_list_html( $reviews, $author_id ?? null, $permissions['administrator'] );
			?>
			<nav class="pagination">
				<?php
				for ( $page = 1; $page < $pages; $page++ ) {
					$activePage = $_GET['reviews-page'] ?? 1;
					echo '<button class="page-selector ' . ( ( $activePage == ( $page ) ) ? 'active' : '' ) . '" data-page-number="' . $page . '" data-action="change-review-page">' . ( $page ) . '</button>';
				}
				?>
			</nav>
			<?php
		} else {
			show_alert( 'No reviews yet.', 'notice' );
		}

		if ( ! $permissions['provider'] ) {
			?>
			<form>
				<label for="comment">Comment</label>
				<textarea name="comment" placeholder="Insert your comment here.."><?php echo ! empty( $existing_review ) ? $existing_review->comment : null; ?></textarea>
				<label for="rating">Rating</label>
				<div class="stars">
					<?php
					for ( $i = 0; $i < 5; $i++ ) {
						$rating = $i + 1;
						?>
						<label>
							<input type="radio" name="rating" value="<?php echo $rating; ?>" class="<?php echo ( $rating <= ( $existing_review->rating ?? 0 ) ) ? 'checked' : ''; ?>" <?php echo ( $rating == ( $existing_review->rating ?? 0 ) ) ? 'checked' : ''; ?> required />
							<span class="fas fa-star"></span>
						</label>
						<?php
					}
					?>
				</div>
				<?php echo ! empty( $existing_review ) ? '<input style="display:none" type="checkbox" name="update" checked/>' : ''; ?>
				<?php
				if ( ! empty( $existing_review ) ) {
					?>
					<p>
						Last submitted <?php echo time_elapsed_string( date( 'Y-m-d H:i:s', strtotime( $existing_review->update_date ) ) ); ?>
					</p>
				<?php } ?>
				<div class="actions">
					<?php
					if ( ! empty( $existing_review ) ) {
						?>
						<button class="action destructive" data-action="delete-review" data-author-id="<?php echo $existing_review->uid; ?>">Delete</button>
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
 * @param   array $asset The PolicyMS API asset.
 * @param   array $args Various printing arguments.
 *
 * @since   1.0.0
 * @author  Alexandros Raikos <araikos@unipi.gr>
 * @author  Eleftheria Kouremenou <elkour@unipi.gr>
 */
function description_html( $description, $image_blobs, $pages, $reviews, $permissions ) {
	/**
	 * Print the file viewer table.
	 *
	 * @param   string $title The title of the collection.
	 * @param   string $id The id of the collection.
	 * @param   array  $files A collection of files.
	 * @param   bool   $collapsed Whether the view is collapsed by default.
	 *
	 * @since   1.0.0
	 * @author  Alexandros Raikos <araikos@unipi.gr>
	 * @author  Eleftheria Kouremenou <elkour@unipi.gr>
	 */
	function asset_viewer( string $title, string $category, array $assets, bool $collapsed = false ): void {
		?>
		<div class="policyms file-viewer <?php echo ( $collapsed ) ? 'collapsed' : ''; ?>">
			<button data-files-category="<?php echo $category; ?>" class="action" <?php echo empty( $assets ) ? 'disabled' : ''; ?>><?php echo $title; ?></button>
			<table>
				<tr>
					<th>Name</th>
					<th>Version</th>
					<th>Size</th>
					<th>Added</th>
				</tr>
				<?php
				if ( ! empty( $assets ) ) {
					foreach ( $assets as $asset ) {
						?>
						<tr data-file-id="<?php echo $asset->id; ?>">
							<td><a class="download" data-file-id="<?php echo $asset->id; ?>" data-type="<?php echo $category; ?>"><?php echo $asset->filename; ?></a></td>
							<td><?php echo $asset->version; ?></td>
							<td><?php echo $asset->size; ?></td>
							<td><?php echo time_elapsed_string( date( 'Y-m-d H:i:s', strtotime( $asset->update_date ) ) ); ?></td>
						</tr>
						<?php
					}
				} else {
					echo '<tr><td colspan="4">';
					show_alert( 'No ' . $category . ' found.', 'notice' );
					echo '</td></tr>';
				}
				?>
			</table>
		</div>
		<?php
	}

	if ( ! empty( $description ) ) {
		?>
		<div class="policyms description">
			<?php
			if ( $permissions['administrator'] && $description->metadata['approved'] == '0' ) {
				?>
				<div class="policyms-notice" id="policyms-description-approval">
					<p>This asset is not yet accessible from other authorized Marketplace users.</p>
					<button class="action destructive" data-response="disapprove">Delete</button>
					<button class="action" data-response="approve">Approve</button>
				</div>
				<?php
			}
			?>
			<header>
				<div class="title">
					<h1><?php echo $description->information['title']; ?>
						<?php
						if ( $permissions['provider'] || $permissions['administrator'] ) {
							?>
							<span class="status label <?php echo ( $description->metadata['approved'] == '1' ) ? 'success' : 'notice'; ?>">
								<?php echo ( $description->metadata['approved'] == '1' ) ? 'Approved' : 'Pending'; ?>
							</span>
							<?php
						}
						?>
					</h1>
					<?php
					if ( $permissions['provider'] || $permissions['administrator'] ) {
						?>
						<button class="outlined" data-action="edit">
							<span class="fas fa-pen"></span> Edit
						</button>
						<?php
					}
					?>
				</div>
				<div class="metadata">
					<?php if ( $permissions['authenticated'] ) { ?>
						<span class="provider">
							<a href="<?php echo $pages['account_page'] . '?user=' . $description->metadata['provider']; ?>">
								<?php echo $description->metadata['provider_name']; ?>
							</a>
						</span>
						<?php
					}

					if ( ! empty( $description->information['owner'] ) ) {
						?>
						<span class="owner">
							&copy; <?php echo $description->information['owner']; ?>
						</span>
					<?php } ?>
					<span class="type pill">
						<a href="<?php echo $pages['archive_page'] . '?type=' . $description->type; ?>">
							<?php echo $description->type; ?>
						</a>
					</span>
					<?php
					if ( ! empty( $description->information['subtype'] ) ) {
						if ( $description->information['subtype'] != '-' ) {
							?>
							<span class="sub-type pill">
								<a href="<?php echo $pages['archive_page'] . '?subtype=' . $description->information['subtype']; ?>">
									<?php echo $description->information['subtype']; ?>
								</a>
							</span>
							<?php
						}
					}
					if ( ! empty( $description->information['fieldOfUse'] ) ) {
						?>
						<span class="fields-of-use">
							<?php
							foreach ( $description->information['fieldOfUse'] as $field_of_use ) {
								if ( ! empty( $field_of_use ) ) {
									echo '<span>' . $field_of_use . '</span>';
								}
							}
							?>
						</span>
					<?php } ?>
					<a href="#reviews" class="reviews">
						<span class="fas fa-star"></span> <?php echo $description->metadata['reviews']['average_rating'] . ' (' . $description->metadata['reviews']['no_reviews'] . ' reviews)'; ?>
					</a>
					&nbsp;
					<span class="views">
						<span class="fas fa-eye"></span>
						<?php echo $description->metadata['views']; ?> views
					</span>
					<span class="last-update-date">
						Last updated <?php echo time_elapsed_string( date( 'Y-m-d H:i:s', strtotime( $description->metadata['updateDate'] ) ) ); ?>
					</span>
				</div>
			</header>
			<div class="content">
				<div class="files <?php echo ( $permissions['authenticated'] ) ? '' : 'locked'; ?>">
					<h2>Uploads</h2>
					<?php
					if ( $permissions['authenticated'] ) {
						asset_viewer( 'Files', 'files', $description->assets['files'], ( empty( $description->assets['files'] ) ) );
						asset_viewer( 'Videos (Gallery)', 'videos', $description->assets['videos'], ( empty( $description->assets['videos'] ) ) );
						asset_viewer( 'Images (Gallery)', 'images', $description->assets['images'], ( empty( $description->assets['images'] ) ) );
					} else {
						show_lock( $pages['login_page'], 'view and download files' );
					}
					if ( $permissions['authenticated'] ) {
						?>
						<div class="comments">
							<h2>Additional information</h2>
							<?php
							if ( ! empty( $description->information['comments'] ) ) {
								echo '<p>' . $description->information['comments'] . '</p>';
							} else {
								show_alert( 'No additional information.', 'notice' );
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
							if ( $permissions['authenticated'] ) {
								echo $description->information['description'];
							} else {
								echo $description->information['short_desc'];
							}
							?>
						</p>
						<?php
						if ( ! empty( $description->links[0] ) ) {
							?>
							<ul>
								<?php
								foreach ( $description->links as $link ) {
									echo '<li><a class="button outlined" href="' . explode( ':', $link, 2 )[1] . '" target="blank">' . explode( ':', $link, 2 )[0] . '</a></li>';
								}
								?>
							</ul>
						<?php } ?>
					</div>
					<div class="gallery">
						<h2>Gallery</h2>
						<div class="slider">
							<?php
							if ( $permissions['authenticated'] ) {
								if ( ! empty( $description->assets['videos'] ) ) {
									foreach ( $description->assets['videos'] as $video ) {
										?>
										<div class="item" data-asset-category="videos" data-asset-id="<?php echo $video->id; ?>">
											<img class="play-icon" src="<?php echo get_site_url( null, '/wp-content/plugins/policyms/public/assets/svg/play.svg' ); ?>" />
											<img class="video-thumbnail" src="
											<?php
											echo ( PolicyMS_Public::get_plugin_setting( true, 'marketplace_host' )
												. '/videos/' . $video->id . '?thumbnail=1'
											)
											?>
											">
											<?php
											if ( $permissions['provider'] ) {
												?>
												<div class="toolbar">
													<span>
														<?php echo $video->filename; ?>
														(<?php echo $video->size; ?>)
													</span>
													<div class="tools">
														<button data-action="delete" data-asset-category="images" data-asset-id="<?php echo $video->id; ?>" class="action outlined">
															Delete
														</button>
													</div>
												</div>
												<?php
											}
											?>
										</div>
										<?php
									}
								}
								if ( ! empty( $image_blobs ) ) {
									foreach ( $image_blobs as $key => $image_blob ) {
										?>
										<div class="item" data-asset-id="<?php echo $description->assets['images'][ $key ]->id; ?>" data-asset-category="images">
											<?php
											echo '<img src="data:image/*;base64,' . base64_encode( $image_blob ) . '" data-asset-category="images" data-asset-id="' . $description->assets['images'][ $key ]->id . '" draggable="false" />';
											if ( $permissions['provider'] || $permissions['administrator'] ) {
												?>
												<div class="toolbar">
													<span>
														<?php echo $description->assets['images'][ $key ]->filename; ?>

														(<?php echo $description->assets['images'][ $key ]->size; ?>)
													</span>
													<div class="tools">
														<?php if ( $description->assets['images'][ $key ]->id == $description->image_id ) { ?>
															<button data-action="remove-default" data-asset-id="<?php echo $description->assets['images'][ $key ]->id; ?>" class="action outlined">Remove cover image</button>
														<?php } else { ?>
															<button data-action="set-default" data-asset-id="<?php echo $description->assets['images'][ $key ]->id; ?>" class="action outlined">Set as cover image</button>
														<?php } ?>
														<button data-action="delete" data-asset-category="images" data-asset-id="<?php echo $description->assets['images'][ $key ]->id; ?>" class="action outlined">
															Delete
														</button>
													</div>
												</div>
												<?php
											}
											?>
										</div> 
										<?php
									}
								}
								if ( empty( $description->assets['videos'] ) && empty( $description->assets['images'] ) ) {
									show_alert( 'No images or videos were found.', 'notice' );
								}
							} else {
								show_lock( $pages['login_page'], 'view the gallery' );
							}
							?>
						</div>
					</div>
				</div>
			</div>

			<div class="reviews" id="reviews">
				<h2>Reviews</h2>
				<?php
				if ( $permissions['authenticated'] ) {
					description_reviews_html(
						$reviews['content'] ?? array(),
						$reviews['pages'] ?? null,
						$description->user_review ?? null,
						$permissions
					);
				} else {
					show_lock( $pages['login_page'], 'view reviews for this description' );
				}
				?>
			</div>
			<?php
			if ( $permissions['provider'] || $permissions['administrator'] ) {
				description_editor_html( $description, $permissions );
			}
			?>
		<?php
	}
}

/**
 * Display a list of assets with filtering, sorting and custom pagination.
 *
 * @param string   $id The identifier for the viewer.
 * @param array    $content The asset structure to be displayed.
 * @param callable $inner_html The callback that prints the list item HTML.
 * @param array    $args The arguments of the parent function
 *
 * @since   1.0.0
 * @author  Alexandros Raikos <araikos@unipi.gr>
 */
function entity_list_html( string $id, array $content, bool $visitor, callable $inner_html, ?string $create_page_url = null ) {
	?>
		<header>
			<h3><?php echo ucfirst( $id ); ?></h3>
			<div class="actions">
				<form action="" class="selector">
					<label for="sort-by">Sort by</label>
					<select name="sort-by" data-category="<?php echo $id; ?>">
						<option value="newest" <?php echo ( ( ( $_GET['sort-by'] ?? '' == 'newest' ) || empty( $_GET['sort-by'] ) ) ? 'selected' : '' ); ?>>Newest</option>
						<option value="oldest" <?php echo ( ( $_GET['sort-by'] ?? '' == 'oldest' ) ? 'selected' : '' ); ?>>Oldest</option>
						<option value="rating-asc" <?php echo ( ( $_GET['sort-by'] ?? '' == 'rating-asc' ) ? 'selected' : '' ); ?>>Highest rated</option>
						<option value="rating-desc" <?php echo ( ( $_GET['sort-by'] ?? '' == 'rating-desc' ) ? 'selected' : '' ); ?>>Lowest rated</option>
						<?php
						if ( $id == 'descriptions' ) {
							?>
							<option value="views-asc" <?php echo ( ( $_GET['sort-by'] ?? '' == 'views-asc' ) ? 'selected' : '' ); ?>>Most viewed</option>
							<option value="views-desc" <?php echo ( ( $_GET['sort-by'] ?? '' == 'views-desc' ) ? 'selected' : '' ); ?>>Least viewed</option>
						<?php } ?>
						<option value="title" <?php echo ( ( $_GET['sort-by'] ?? '' == 'title' ) ? 'selected' : '' ); ?>>Title</option>
					</select>
					<label for="items-per-page">Items per page</label>
					<select name="items-per-page" data-category="<?php echo $id; ?>">
						<option value="5" <?php echo ( ( ( $_GET['items-per-page'] ?? '' == '5' ) ) ? 'selected' : '' ); ?>>5</option>
						<option value="10" <?php echo ( ( $_GET['items-per-page'] ?? '' == '10' || empty( $_GET['items-per-page'] ) ) ? 'selected' : '' ); ?>>10</option>
						<option value="25" <?php echo ( ( $_GET['items-per-page'] ?? '' == '25' ) ? 'selected' : '' ); ?>>25</option>
						<option value="50" <?php echo ( ( $_GET['items-per-page'] ?? '' == '50' ) ? 'selected' : '' ); ?>>50</option>
						<option value="100" <?php echo ( ( $_GET['items-per-page'] ?? '' == '100' ) ? 'selected' : '' ); ?>>100</option>
					</select>
				</form>
				<?php
				if ( ! $visitor && $id == 'descriptions' && ! empty( $create_page_url ) ) {
					?>
					<a id="policyms-upload" href="<?php echo $create_page_url; ?>" title="Create new"><span class="fas fa-plus"></span> Create new</a>
				<?php } ?>
			</div>
		</header>
		<div class="collection-filters" data-category="<?php echo $id; ?>">
			<div>Filter by type:</div>
		</div>
		<div class="paginated-list" data-category="<?php echo $id; ?>">
			<?php
			if ( ! empty( $content ) ) {
				foreach ( $content as $page => $page_items ) {
					echo '<ul data-page="' . ( $page + 1 ) . '" class="page ' . $id . ' ' . ( $page == 0 ? 'visible' : '' ) . '">';
					if ( ! empty( $content ) ) {
						foreach ( $page_items as $item ) {
							$inner_html( $item );
						}
					} else {
						show_alert( "You don't have any " . $id . ' yet.' );
					}
					echo '</ul>';
				}
			} else {
				show_alert( 'This user does not have any ' . $id . '.', 'notice' );
			}
			?>
			<nav class="pagination">
				<?php
				if ( count( $content ?? array() ) > 1 ) {
					foreach ( $content as $page => $page_items ) {
						echo '<button data-category="' . $id . '" class="page-selector ' . ( ( $page == ( $_GET['page'] ?? 0 ) ) ? 'active' : '' ) . '" data-' . $id . '-page="' . ( $page + 1 ) . '">' . ( $page + 1 ) . '</button>';
					}
				}
				?>
			</nav>
		</div>
	<?php
}
