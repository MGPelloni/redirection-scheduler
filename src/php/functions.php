<?php
/**
 * Adds a redirect to the Redirection plugin.
 *
 * @link https://redirection.me/developer/rest-api/#api-Redirect-CreateRedirect
 *
 * @param string $source_url The source URL.
 * @param string $target_url The target URL.
 *
 * @return int|WP_Error The redirect ID if successful, WP_Error otherwise.
 */
function redirection_scheduler_add_redirect($source_url, $target_url, $http_code = 301)
{
    global $wpdb;
    $table_name = $wpdb->prefix . REDIRECTION_SCHEDULER_TABLE_NAME;

    if (class_exists('Red_Item')) {
        include_once WP_PLUGIN_DIR . '/redirection/models/group.php'; // @link https://wordpress.org/support/topic/call-to-red_itemcreate-results-in-fatal-error/

        $id = Red_Item::create(
            [
            'status' => 'enabled',
            'position' => 0,
            'match_data' => [
            'source' => [
            'flag_regex' => false,
            ]
            ],
            'regex' => false,
            'url' => $source_url,
            'match_type' => 'url',
            'title' => 'Scheduled Redirect',
            'group_id' => 1,
            'action_type' => 'url',
            'action_code' => $http_code,
            'action_data' => [
            'url' => $target_url
            ]
            ]
        );
    } else {
        $id = new WP_Error('500', 'Red_Item class does not exist.');
    }

    if (is_wp_error($id) || empty($id)) {
        $wpdb->update(
            $table_name,
            [
                'status' => $id->get_error_message(),
            ],
            [
                'source_url' => $source_url,
                'target_url' => $target_url,
            ]
        );

        return $id;
    }

    $wpdb->update(
        $table_name,
        [
            'status' => 'Live',
        ],
        [
            'source_url' => $source_url,
            'target_url' => $target_url,
        ]
    );

    redirection_scheduler_clear_cache();
    return $id;
}

/**
 * Deletes a redirect from the Redirection plugin.
 *
 * @param string $source_url The source URL.
 * @param string $target_url The target URL.
 *
 * @return void
 */
function redirection_scheduler_delete_redirect($source_url, $target_url)
{
	global $wpdb;
	$table_name = $wpdb->prefix . REDIRECTION_SCHEDULER_TABLE_NAME;

	$status = "Not Found";

	if (class_exists('Red_Item')) {
		$redirects = Red_Item::get_for_matched_url($source_url);

		if (!empty($redirects)) {
			foreach ($redirects as $redirect) {
				if ( $redirect->get_action_data() == $target_url) {
					if (defined('WP_CLI') && WP_CLI) {
						WP_CLI::log("Successfully found redirect.");
					}

					$redirect->delete();
					$status = "Deleted";
				}
			}
		}
	} else {
		$status = "Error";
	}

	$wpdb->update(
		$table_name,
		[
			'status' => $status,
		],
		[
			'source_url' => $source_url,
			'target_url' => $target_url,
		]
	);

	redirection_scheduler_clear_cache();
}

/**
 * Activation callback for Redirection Scheduler.
 *
 * @return void
 */
function redirection_scheduler_activate()
{
    if (!wp_next_scheduled('redirection_scheduler_cron')) {
        wp_schedule_event(time(), 'one_minute', 'redirection_scheduler_cron');
    }

    redirection_scheduler_create_table();
}

/**
 * Deactivation callback for Redirection Scheduler.
 *
 * @return void
 */
function redirection_scheduler_deactivate()
{
    wp_unschedule_event(wp_next_scheduled('redirection_scheduler_cron'), 'redirection_scheduler_cron');
}
