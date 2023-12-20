<?php
/*
Plugin Name: Redirection Scheduler
Plugin URI:  https://github.com/MGPelloni/redirection-scheduler/
Description: Extends the Redirection plugin by allowing administrators to schedule redirects at a later time.
Author:      Marco Pelloni
Author URI:  https://github.com/MGPelloni/
License:     GPL3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Text Domain: redirection-scheduler
Tested up to: 6.4

Version number is automatically adjusted by semantic-release-bot on release, do not adjust manually:
Version: 1.0.0

*/

if (! defined('ABSPATH') ) {
    die();
}

// Definitions
define('REDIRECTION_SCHEDULER_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('REDIRECTION_SCHEDULER_PLUGIN_URL', plugin_dir_url(__FILE__));
define('REDIRECTION_SCHEDULER_ADMIN_URL', get_admin_url(null, '/tools.php?page=redirection-scheduler'));
define('REDIRECTION_SCHEDULER_TABLE_NAME', 'redirection_scheduler');

// Functions
require_once REDIRECTION_SCHEDULER_PLUGIN_PATH . 'src/php/admin.php';
require_once REDIRECTION_SCHEDULER_PLUGIN_PATH . 'src/php/cron.php';
require_once REDIRECTION_SCHEDULER_PLUGIN_PATH . 'src/php/database.php';
require_once REDIRECTION_SCHEDULER_PLUGIN_PATH . 'src/php/functions.php';
require_once REDIRECTION_SCHEDULER_PLUGIN_PATH . 'src/php/routes.php';

// Hooks [Activation, Deactivation]
register_activation_hook(__FILE__, 'redirection_scheduler_activate'); // Registers cron event.
register_deactivation_hook(__FILE__, 'redirection_scheduler_deactivate'); // Unregisters cron event.

// Admin (src/php/admin.php)
add_action('admin_menu', 'redirection_scheduler_admin_custom_menu'); // Adds option page to Tools.
add_action('admin_enqueue_scripts', 'redirection_scheduler_admin_styles'); // Enqueue styles for the option page.
add_action('admin_init', 'redirection_scheduler_check_dependency'); // Check if Redirection plugin is active.

// Cron (src/php/cron.php)
add_filter('cron_schedules', 'redirection_scheduler_cron_schedules'); // Add custom cron schedule to check every minute.
add_action('redirection_scheduler_cron', 'redirection_scheduler_cron_callback'); // Loops through scheduled redirects and adds them to Redirection.

// Routes (src/php/routes.php)
add_action('rest_api_init', 'redirection_scheduler_register_routes'); // Register routes for setting and deleting redirects.

// Updates
require_once(REDIRECTION_SCHEDULER_PLUGIN_PATH . 'lib/plugin-update-checker-5.0/plugin-update-checker.php');
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$myUpdateChecker = PucFactory::buildUpdateChecker(
	'https://marcopelloni.com/releases/redirection-scheduler.json',
	__FILE__,
	'redirection-scheduler'
);
