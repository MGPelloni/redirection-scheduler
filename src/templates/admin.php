<section class="redirection-scheduler-admin _container">
    <div class="redirection-scheduler-grid">
        <div class="redirection-scheduler-form">
            <header class="redirection-scheduler-header">
                <h1>🕒 <?php esc_html_e("Redirection Scheduler", "redirection-scheduler") ?></h1>
                <p><?php esc_html_e("Extends the Redirection plugin by allowing administrators to schedule redirects at a later time.", "redirection-scheduler") ?></p>
            </header>
            <form id="redirection-scheduler-set-form" method="post" action="<?php echo esc_url(rest_url('/redirection-scheduler/set')) ?>">
                <?php wp_nonce_field('wp_rest', '_wpnonce', false); ?>
                <div>
                    <label><?php esc_html_e("Start Date", "redirection-scheduler") ?>:</label>
                    <input type="date" name="redirection_scheduler_date" value="<?php esc_attr_e(wp_date('Y-m-d')); ?>" required />
                </div>
                <div>
                    <label><?php esc_html_e("Start Time", "redirection-scheduler") ?> (<a href="<?php echo esc_url(get_admin_url() . "/options-general.php#timezone_string") ?>"><?php esc_html_e(wp_timezone_string()) ?></a>):</label>
                    <input type="time" name="redirection_scheduler_time" required />
                </div>
                <div>
                    <label><?php esc_html_e("Source URL", "redirection-scheduler") ?>:</label>
                    <input type="text" name="redirection_scheduler_source_url" placeholder="/example-source/" required />
                </div>
                <div>
                    <label><?php esc_html_e("Target URL", "redirection-scheduler") ?>:</label>
                    <input type="text" name="redirection_scheduler_target_url" placeholder="/example-target/" required />
                </div>
				<div>
					<label><?php esc_html_e("Redirect Type", "redirection-scheduler") ?>:</label>
					<select name="redirection_http_code">
						<option value="301" selected>301 - Moved Permanently</option>
						<option value="302">302 - Found</option>
						<option value="303">303 - See Other</option>
						<option value="304">304 - Not Modified</option>
						<option value="307">307 - Temporary Redirect</option>
						<option value="308">308 - Permanent Redirect</option>
					</select>
				</div>
                <input type="submit" value="<?php esc_attr_e("Schedule", "redirection-scheduler") ?>">
            </form>
        </div>
        <div class="redirection-scheduler-info">
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th scope="col" id="source_url" class="manage-column column-source_url column-primary"><?php esc_html_e("Source URL", "redirection-scheduler") ?></th>
                        <th scope="col" id="target_url" class="manage-column column-target_url"><?php esc_html_e("Target URL", "redirection-scheduler") ?></th>
						<th scope="col" id="http_code" class="manage-column column-http_code"><?php esc_html_e("Redirect Type", "redirection-scheduler") ?></th>
                        <th scope="col" id="scheduled" class="manage-column column-start_date"><?php esc_html_e("Schedule", "redirection-scheduler") ?></th>
                        <th scope="col" id="status" class="manage-column column-status"><?php esc_html_e("Status", "redirection-scheduler") ?></th>
                        <th scope="col" id="actions" class="manage-column column-actions"><?php esc_html_e("Actions", "redirection-scheduler") ?></th>
                    </tr>
                </thead>
                <tbody id="redirection-scheduler-tbody">
                    <?php
                    global $wpdb;
                    $table_name = $wpdb->prefix . REDIRECTION_SCHEDULER_TABLE_NAME;
                    $cache_key = 'redirection_scheduler_admin_query';
                    $cached_results = wp_cache_get($cache_key);

                    if ($cached_results === false) {
                        $redirects = $wpdb->get_results($wpdb->prepare("SELECT * FROM %i ORDER BY %i DESC", $table_name, 'scheduled'), ARRAY_A);
                        wp_cache_set($cache_key, $redirects);
                    } else {
                        $redirects = $cached_results;
                    }

                    $nonce = wp_create_nonce('wp_rest');
                    foreach ($redirects as $redirect): ?>
                        <tr class="-<?php esc_attr_e(strtolower($redirect['status'])) ?>">
                            <td><?php esc_html_e($redirect['source_url']) ?></td>
                            <td><?php esc_html_e($redirect['target_url']) ?></td>
							<td><?php esc_html_e($redirect['http_code']) ?></td>
                            <td><?php esc_html_e(wp_date('F jS, Y @ g:ia T', strtotime($redirect['scheduled']))); ?></td>
                            <td><?php esc_html_e($redirect['status']) ?></td>
                            <td>
                        <?php $label = ($redirect['status'] === "Live") ? "Clear" : "Delete"; ?>
                                <a href="<?php echo esc_url(rest_url("/redirection-scheduler/delete/{$redirect['id']}/?_wpnonce=$nonce")) ?>" class="button button-secondary"><?php esc_html_e($label) ?></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
