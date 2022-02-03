# PolicyCLOUD Data Marketplace for WordPress

The official plugin for the PolicyCLOUD Data Marketplace, enabling front-end access to the PolicyCLOUD Data Marketplace API.

## Getting started

Read the sections below to get started with PolicyCLOUD Data Marketplace for WordPress.

### Requirements

- PHP >= 7.4
- Set PHP's `upload_max_filesize` directive to the desired Marketplace limit, up to **500MB**.
- Set PHP's `post_max_size` directive to match the previous value.
- WordPress >= 5.0

### Installation & Setup

Clone the repository, zip and install via the WordPress dashboard.

Head to the Dashboard > Settings > PolicyCLOUD Data Marketplace to enter your PolicyCLOUD Data Marketplace API credentials as provided by the API administrator.

### Shortcodes

Use the shortcodes below for all PolicyCLOUD Data Marketplace API functionality.

#### Log In

To add a log in form to a requested page, add the following shortcode:

`[policycloud-marketplace-user-authentication]`

#### Registration

To add a registration form to a requested page, add the following shortcode:

`[policycloud-marketplace-user-registration]`

#### Show available Descriptions

To view Descriptions, add the following shortcodes:

`[policycloud-marketplace-descriptions-featured]`

`[policycloud-marketplace-description-archive]`

`[policycloud-marketplace-description]`

#### Upload Description Object

To view the upload form for creating a Description Object for authorized users, add the following shortcode:

`[policycloud-marketplace-description-creation]`

#### PolicyCLOUD Data Marketplace account page

To view the account page for an authorized user, add the following shortcode:

`[policycloud-marketplace-user]`

### Other features

This plugin also enables the following features:

1. Automatic "Log In/Log Out" menu item.

## Credits

This work extends, and is schematically based upon, the PolicyCLOUD Data Marketplace API, created and maintained by [@vkoukos](https://github.com/vkoukos). Special thanks to [@elefkour](https://github.com/elefkour) for co-authoring and co-maintaining this repository.
