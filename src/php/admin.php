<?php
/**
 * Enqueues styles for Redirection Scheduler admin pages.
 *
 * @link   https://developer.wordpress.org/themes/basics/including-css-javascript/
 * @return void
 */
function redirection_scheduler_admin_styles()
{
    wp_register_style('redirection-scheduler', REDIRECTION_SCHEDULER_PLUGIN_URL . 'dist/redirection-scheduler.min.css', [], filemtime(REDIRECTION_SCHEDULER_PLUGIN_PATH . 'dist/redirection-scheduler.min.css'));
    wp_enqueue_style('redirection-scheduler');

	wp_register_script('redirection-scheduler', REDIRECTION_SCHEDULER_PLUGIN_URL . 'dist/redirection-scheduler.min.js', [], filemtime(REDIRECTION_SCHEDULER_PLUGIN_PATH . 'dist/redirection-scheduler.min.js'));
	wp_enqueue_script('redirection-scheduler');
}

/**
 * Creates an option page.
 *
 * @link   https://codex.wordpress.org/Creating_Options_Pages
 * @return void
 */
function redirection_scheduler_admin_custom_menu()
{
    add_submenu_page('tools.php', 'Redirection Scheduler', 'Redirection Scheduler', 'manage_options', 'redirection-scheduler', 'redirection_scheduler_admin_options_page', 58);
}

/**
 * Callback function for the options page.
 *
 * @link   https://codex.wordpress.org/Creating_Options_Pages
 * @return void
 */
function redirection_scheduler_admin_options_page()
{
    include_once REDIRECTION_SCHEDULER_PLUGIN_PATH . 'src/templates/admin.php';
}

/**
 * Checks if the Redirection plugin is active, and if not, deactivates the Redirection Scheduler plugin.
 *
 * @return void
 */
function redirection_scheduler_check_dependency()
{
    if (! is_plugin_active('redirection/redirection.php') && is_plugin_active('redirection-scheduler/redirection-scheduler.php') ) {
        deactivate_plugins('redirection-scheduler/redirection-scheduler.php');

        add_action(
            'admin_notices', function () {
                ?>
          <div class="notice notice-error">
              <p><strong>The Redirection Scheduler has been deactivated</strong>. You must first install and activate the <a target="_blank" href="https://wordpress.org/plugins/redirection">Redirection</a> plugin.</p>
          </div>
                <?php
            }
        );
    }
}
