<?php
/**
 * Creates the Redirection Scheduler table.
 *
 * @return void
 */
function redirection_scheduler_create_table()
{
    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix . REDIRECTION_SCHEDULER_TABLE_NAME;

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        source_url varchar(255) NOT NULL,
        target_url varchar(255) NOT NULL,
		http_code varchar(3) NOT NULL,
        scheduled datetime NOT NULL,
        status varchar(255) NOT NULL,
		meta longtext NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    include_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}

/**
 * Clears the cache for the Redirection Scheduler table database queries.
 *
 * @return void
 */
function redirection_scheduler_clear_cache()
{
    wp_cache_delete('redirection_scheduler_cron_query');
    wp_cache_delete('redirection_scheduler_admin_query');
}
