![The PolicyMS logotype.](docs/img/policyms-repo.png)

# PolicyMS for WordPress

PolicyMS for WordPress enables front-end access to the PolicyMS API for managing policy-related data.

## Getting started

Read the sections below to get started with PolicyMS for WordPress.

### Requirements

- PHP >= 8.0
- Set PHP's `upload_max_filesize` directive to the desired Marketplace limit, up to **500MB**.
- Set PHP's `post_max_size` directive to match the previous value.
- WordPress >= 5.0

### Installation & Setup

Clone the repository, zip and install via the WordPress dashboard.

Head to the Dashboard > Settings > PolicyMS to enter your PolicyMS API credentials as provided by the API administrator.

### Shortcodes

Use the shortcodes below for all PolicyMS API functionality.

#### Log In

To add a log in form to a requested page, add the following shortcode:

`[policyms-user-authentication]`

#### Registration

To add a registration form to a requested page, add the following shortcode:

`[policyms-user-registration]`

#### Show available Descriptions

To view Descriptions, add the following shortcodes:

`[policyms-descriptions-featured]`

`[policyms-description-archive]`

`[policyms-description]`

#### Upload Description Object

To view the upload form for creating a Description Object for authorized users, add the following shortcode:

`[policyms-description-creation]`

#### PolicyMS account page

To view the account page for an authorized user, add the following shortcode:

`[policyms-user]`

### Other features

This plugin also enables the following features:

1. Automatic "Log In/Log Out" menu item.

## Credits

This work extends, and is schematically based upon, the PolicyMS API, created and maintained by [@vkoukos](https://github.com/vkoukos). Special thanks to [@elefkour](https://github.com/elefkour) for co-authoring and co-maintaining this repository.
