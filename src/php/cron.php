<?php
/**
 * WordPress cron event for Redirection Scheduler.
 *
 * @return void
 */
function redirection_scheduler_cron_callback()
{
    global $wpdb;
    $table_name = $wpdb->prefix . REDIRECTION_SCHEDULER_TABLE_NAME;
    $cache_key = 'redirection_scheduler_cron_query';
    $cached_results = wp_cache_get($cache_key);

    if ($cached_results === false) {
        $redirects = $wpdb->get_results($wpdb->prepare("SELECT * FROM %i WHERE %i = %s", $table_name, 'status', 'Scheduled'), ARRAY_A);
        wp_cache_set($cache_key, $redirects);
    } else {
        $redirects = $cached_results;
    }

    foreach ($redirects as $redirect) {
        $schedule = DateTime::createFromFormat('Y-m-d H:i:s', $redirect['scheduled'], new DateTimeZone('UTC'));
        $schedule->setTimezone(wp_timezone());

        if ($schedule->getTimestamp() > wp_date('U')) {
            continue;
        }

        redirection_scheduler_add_redirect($redirect['source_url'], $redirect['target_url'], $redirect['http_code']);
    }
}

/**
 * Filter function for adding a minute cron time interval.
 *
 * @param  array $schedules Current WordPress cron schedules.
 * @return array Updated WordPress cron schedules.
 */
function redirection_scheduler_cron_schedules( $schedules )
{
    $schedules['one_minute'] = array(
        'interval' => 60,
        'display'  => esc_html__('Every Minute'), );
    return $schedules;
}
