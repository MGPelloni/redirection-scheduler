<?php
/**
 * Registers an REST route to submit the Client ID and Client Secret from WordPress admin.
 *
 * @return void
 */
function redirection_scheduler_register_routes()
{
    register_rest_route(
        'redirection-scheduler', '/set/', [
        'methods' => 'POST',
        'callback' => 'redirection_scheduler_set',
        'permission_callback' => function () {
            return current_user_can('manage_options');
        }
        ]
    );

    register_rest_route(
        'redirection-scheduler', '/delete/(?P<id>\d+)', [
        'methods' => 'GET',
        'callback' => 'redirection_scheduler_delete',
        'permission_callback' => function () {
            return current_user_can('manage_options');
        }
        ]
    );
}

/**
 * Callback function for /wp-json/redirection-scheduler/set/ route.
 * Sets a redirect to be scheduled with an input of date, time, source URL and target URL.
 *
 * @return void
 */
function redirection_scheduler_set()
{
    if (empty($_POST['_wpnonce'])) {
        wp_send_json_error(new WP_Error('500', 'Missing nonce.'));
    }

    if (empty($_POST['redirection_scheduler_date'])) {
        wp_send_json_error(new WP_Error('500', 'Missing scheduled date.'));
    }

    if (empty($_POST['redirection_scheduler_time'])) {
        wp_send_json_error(new WP_Error('500', 'Missing scheduled time.'));
    }

    if (empty($_POST['redirection_scheduler_source_url'])) {
        wp_send_json_error(new WP_Error('500', 'Missing source URL.'));
    }

    if (empty($_POST['redirection_scheduler_target_url'])) {
        wp_send_json_error(new WP_Error('500', 'Missing target URL.'));
    }

    // Sanitize inputs
    $date = sanitize_text_field($_POST['redirection_scheduler_date']);
    $time = sanitize_text_field($_POST['redirection_scheduler_time']);
    $source_url = sanitize_text_field($_POST['redirection_scheduler_source_url']);
    $target_url = sanitize_text_field($_POST['redirection_scheduler_target_url']);
	$http_code = sanitize_text_field($_POST['redirection_http_code']) ?? '301';
    $nonce = sanitize_text_field($_POST['_wpnonce']);

    // Nonce handling
    if (!wp_verify_nonce($nonce, 'wp_rest')) {
        wp_send_json_error(new WP_Error('500', 'Invalid nonce.'));
    }

	// Check to ensure the source URL and target URL follow the correct format, don't require a trailing slash. Check the target URL to see if it's a valid URL.
	if (!preg_match('/^\/[a-zA-Z0-9\-\/]*\/?$/', $source_url)) {
		wp_send_json_error(new WP_Error('500', 'Invalid source URL format.'));
	}

	if (!preg_match('/^\/[a-zA-Z0-9\-\/]*\/?$/', $target_url) && !filter_var($target_url, FILTER_VALIDATE_URL)) {
		wp_send_json_error(new WP_Error('500', 'Invalid target URL format.'));
	}

    // Retrieve the scheduled time in the user's timezone, then convert to UTC and store
    $scheduled = DateTime::createFromFormat('Y-m-d H:i', $date . ' ' . $time, wp_timezone());
    $scheduled->setTimezone(new DateTimeZone('UTC'));
    $scheduled = $scheduled->format('Y-m-d H:i:s');

    global $wpdb;
    $table_name = $wpdb->prefix . REDIRECTION_SCHEDULER_TABLE_NAME;

    $wpdb->insert(
        $table_name,
        [
            'source_url' => $source_url,
            'target_url' => $target_url,
			'http_code' => $http_code,
            'scheduled' => $scheduled, // UTC datetime
            'status' => 'Scheduled',
        ]
    );

    redirection_scheduler_clear_cache();
    wp_send_json_success(new WP_REST_Response('Redirect successfully scheduled.', 200));
    exit;
}

/**
 * Callback function for /wp-json/redirection-scheduler/delete/ route.
 * Deletes a redirect from the database.
 *
 * @param  array $data The request data.
 * @return void
 */
function redirection_scheduler_delete($data)
{
    if (empty($data['id'])) {
        wp_send_json_error(new WP_Error('500', 'Missing ID.'));
    }

    $id = sanitize_text_field($data['id']);

    // Delete the redirect from the database
    global $wpdb;
    $table_name = $wpdb->prefix . REDIRECTION_SCHEDULER_TABLE_NAME;

    $wpdb->delete(
        $table_name,
        [
            'id' => $id,
        ]
    );

    redirection_scheduler_clear_cache();
    wp_redirect(REDIRECTION_SCHEDULER_ADMIN_URL);
    exit;
}
