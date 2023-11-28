<img src="assets/banner-772x250.png" alt="A human-like clock contemplating what the future holds while sitting next to white flowers on a hill" width="100%">

![PHP version](https://img.shields.io/badge/PHP-7.4+-4F5B93.svg?logo=php)
![WP version](https://img.shields.io/badge/WordPress-6.0+-0073aa.svg?&logo=wordpress)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![semantic-release: angular](https://img.shields.io/badge/semantic--release-angular-e10079?logo=semantic-release)](https://github.com/angular/angular/blob/main/CONTRIBUTING.md#-commit-message-format)

# Redirection Scheduler

The Redirection Scheduler plugin allows administrators to schedule future redirects for the [Redirection](https://wordpress.org/plugins/redirection/) plugin.
## Installation

[Download the Redirection Scheduler plugin](https://marcopelloni.com/releases/redirection-scheduler.zip) and install it on your WordPress site. Redirection Scheduler will automatically detect if the [Redirection plugin](https://wordpress.org/plugins/redirection/) is installed and active. If it is not, you will be prompted to install it.

## Usage

The Redirection Scheduler plugin enqueues a redirect record to be added to the Redirection plugin at a future time and date. Once the date is reached, the redirect will be enabled automatically using WordPress Cron. You can schedule redirects by navigating to **Tools -> Redirection Scheduler** within the WordPress administration bar.

## WordPress Cron Configuration

The Redirection Scheduler plugin uses the WordPress Cron to check if a redirect should be enabled. The default configuration of WordPress Cron [does not run constantly as the system cron does; it is only triggered on page load](https://developer.wordpress.org/plugins/cron/). This means that if no one visits the site, the cron will not be triggered and the redirect may not be enabled at the exact time specified.

To solve this issue, you can disable the [WordPress Cron and enable a system cron](https://developer.wordpress.org/plugins/cron/hooking-wp-cron-into-the-system-task-scheduler/). Check with your hosting provider to see how cron is handled within your WordPress instance and if they can assist you in setting up a system cron for WordPress.
