<?php
/*
Plugin Name: Insert Estimated Reading Time
Plugin URI: https://www.nigauri.me/tech/wordpress/plugin_insert_estimated_reading_time
Description: This plugin inserts estimated reading time before contents.
Author: nigauri
Version: 1.2
Author URI: https://www.nigauri.me/
Domain Path: /languages
Text Domain: insert-estimated-reading-time
*/

define("IERT_DOMAIN", "insert-estimated-reading-time");
load_plugin_textdomain(IERT_DOMAIN, false, basename( dirname( __FILE__ ) ) . '/languages' );

/**
 * Edit estimated reading time string.
 * @param string $content post content.
 * @return string Edited content or original content.
 */
function iert_content($content) {

	if (is_home()) {
		if (get_option('iert_is_show_home')) {
			return get_estimated_reading_time($content).$content;
		}
		return $content;
	}

	if (is_front_page()) {
		if (get_option('iert_is_show_front_page')) {
			return get_estimated_reading_time($content).$content;
		}
		return $content;
	}

	if (is_singular( 'post' )) {
		if (get_option('iert_is_show_single')) {
			return get_estimated_reading_time($content).$content;
		} else {
			return $content;
		}

	} else if (is_page()) {
		if (get_option('iert_is_show_page')) {
			return get_estimated_reading_time($content).$content;
		} else {
			return $content;
		}

	} else {
		$args = array(
			'public'   => true,
			'_builtin' => false
		);
		$output = 'objects';
		$operator = 'and';
		$post_types = get_post_types( $args, $output, $operator );
		foreach ( $post_types as $post_type ) {
			if (is_singular( $post_type->name )) {
				if (get_option('iert_is_show_' . $post_type->name)) {
					return get_estimated_reading_time($content).$content;
				} else {
					return $content;
				}
			}
		}
	}

	return $content;
}

/**
 * Edit estimated reading time string.
 * @param string $content post content.
 * @return string Estimated reading time.
 */
function get_estimated_reading_time($content) {
	$iert_format = html_entity_decode(stripslashes(get_option('iert_format')), ENT_QUOTES, mb_internal_encoding());
	$iert_time_output = get_option('iert_time_output');
	$iert_chars_per_minute = get_option('iert_chars_per_minute');

	$iert_format = str_replace('%num%', $iert_chars_per_minute, $iert_format);

	$words = mb_strlen(strip_tags($content));

	if ($iert_time_output == 2) {
		$minutes = floor($words / $iert_chars_per_minute);
		$seconds = floor($words % $iert_chars_per_minute / ($iert_chars_per_minute / 60));
		return str_replace('%sec%', $seconds, str_replace('%min%', $minutes, $iert_format));

	} else {
		$minutes = ceil($words / $iert_chars_per_minute);
		return str_replace('%min%', $minutes, $iert_format);
	}
}

/**
 * Run when activating this plugin.
 */
function iert_install() {
	add_option('iert_chars_per_minute',   '400');
	add_option('iert_format',             __('<p class="estimated-reading-time">Estimated reading time: %min% minute(s)</p>', IERT_DOMAIN));
	add_option('iert_time_output',        '1');
	add_option('iert_is_show_home',       null);
	add_option('iert_is_show_front_page', null);
	add_option('iert_is_show_single',     true);
	add_option('iert_is_show_page',       true);
	add_option('iert_updater_1_2',        true);

	$args = array(
		'public'   => true,
		'_builtin' => false
	);
	$output = 'objects';
	$operator = 'and';
	$post_types = get_post_types( $args, $output, $operator );
	foreach ( $post_types as $post_type ) {
		add_option('iert_is_show_' . $post_type->name, null);
	}
}

/**
 * Update to 1.1.x -> 1.2
 */
function iert_plugin_update() {
	if (!get_option('iert_updater_1_2')) {
		update_option('iert_is_show_home',       !get_option('iert_is_hide_home'));
		update_option('iert_is_show_front_page', !get_option('iert_is_hide_front_page'));
		update_option('iert_is_show_single',     !get_option('iert_is_hide_single'));
		update_option('iert_is_show_page',       !get_option('iert_is_hide_page'));
		delete_option('iert_is_hide_home');
		delete_option('iert_is_hide_front_page');
		delete_option('iert_is_hide_single');
		delete_option('iert_is_hide_page');
		add_option('iert_updater_1_2', true);
	}
}

/**
 * Run when deactivating this plugin. (delete options from DB)
 */
function iert_uninstall() {
	delete_option('iert_is_show_home');
	delete_option('iert_is_show_front_page');
	delete_option('iert_is_show_single');
	delete_option('iert_is_show_page');
	delete_option('iert_chars_per_minute');
	delete_option('iert_format');
	delete_option('iert_time_output');
	delete_option('iert_updater_1_2');

	$args = array(
		'public'   => true,
		'_builtin' => false
	);
	$output = 'objects';
	$operator = 'and';
	$post_types = get_post_types( $args, $output, $operator );
	foreach ( $post_types as $post_type ) {
		delete_option('iert_is_show_' . $post_type->name);
	}
}

/**
 * Options page.
 */
function iert_options_page() {
	$args = array(
		'public'   => true,
		'_builtin' => false
	);
	$output = 'objects';
	$operator = 'and';
	$post_types = get_post_types( $args, $output, $operator );

	if (isset($_POST['iert_options_submit'])) {
		check_admin_referer('iert-update-options');
		update_option('iert_chars_per_minute',   $_POST['iert_chars_per_minute']);
		update_option('iert_format',             $_POST['iert_format']);
		update_option('iert_time_output',        $_POST['iert_time_output']);
		iert_update_option('iert_is_show_home',       $_POST);
		iert_update_option('iert_is_show_front_page', $_POST);
		iert_update_option('iert_is_show_single',     $_POST);
		iert_update_option('iert_is_show_page',       $_POST);
		foreach ( $post_types as $post_type ) {
			iert_update_option('iert_is_show_' . $post_type->name, $_POST);
		}
		echo '<div class="updated fade"><p><strong>'.__('Options saved.', IERT_DOMAIN).'</strong></p></div>';
	}

	$iert_is_show_home       = get_option('iert_is_show_home');
	$iert_is_show_front_page = get_option('iert_is_show_front_page');
	$iert_is_show_single     = get_option('iert_is_show_single');
	$iert_is_show_page       = get_option('iert_is_show_page');
	$iert_chars_per_minute   = get_option('iert_chars_per_minute');
	$iert_format             = htmlentities(stripslashes(get_option('iert_format')), ENT_QUOTES, mb_internal_encoding());
	$iert_time_output        = get_option('iert_time_output');
	$iert_is_show_custom_post_types = array();
	foreach ( $post_types as $post_type ) {
		$iert_is_show_custom_post_types[$post_type->name] = get_option('iert_is_show_' . $post_type->name);
	}
?>

	<div class="wrap">
		<div id="icon-options-general" class="icon32"><br /></div>
		<h2><?php _e('Insert Estimated Reading Time Options', IERT_DOMAIN) ?></h2>

		<form method="post">
			<?php wp_nonce_field('iert-update-options'); ?>
			<input type="hidden" id="iert_options_submit" name="iert_options_submit" value="true" />

			<table class="form-table">

				<tr valign="top">
					<th scope="row">
						<?php _e('Number of characters per minute:', IERT_DOMAIN); ?>
					</th>
					<td>
						<input type="text" id="iert_chars_per_minute" name="iert_chars_per_minute" value="<?php echo get_option('iert_chars_per_minute'); ?>" size="5" />
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">
						<?php _e('Format:', IERT_DOMAIN); ?>
					</th>
					<td>
						<textarea  id="iert_format" name="iert_format" rows="2" style="width: 90%;"><?php echo $iert_format; ?></textarea>
						<small>
							<ul>
								<li>
									<?php _e('<code>%min%</code> will replace minute(s).', IERT_DOMAIN); ?><br />
									<?php _e('<code>%sec%</code> will replace second(s).', IERT_DOMAIN); ?><br />
									<?php _e('<code>%num%</code> will replace Number of characters per minute.', IERT_DOMAIN); ?>
								</li>
								<li>
									<?php _e('If you select "Minutes" in "Time output", please include "%min%".', IERT_DOMAIN); ?><br />
									<?php _e('If you select "Minutes and seconds" in "Time output", please include "%min%" and "%sec%".', IERT_DOMAIN); ?>
								</li>
							</ul>
						</small>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">
						<?php _e('Time output:', IERT_DOMAIN); ?>
					</th>
					<td>
						<select id="iert_time_output" name="iert_time_output">
							<option value="1" <?php echo ($iert_time_output == '1' ? 'selected="selected"' : '') ?>><?php _e('Minutes', IERT_DOMAIN); ?></option>
							<option value="2" <?php echo ($iert_time_output == '2' ? 'selected="selected"' : '') ?>><?php _e('Minutes and seconds', IERT_DOMAIN); ?></option>
						</select>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">
						<label for="iert_is_show_home"><?php _e('Show in Home:', IERT_DOMAIN); ?></label>
					</th>
					<td>
						<input type="checkbox" id="iert_is_show_home" name="iert_is_show_home" value="true" <?php echo ($iert_is_show_home ? 'checked="checked"' : '') ?> />
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">
						<label for="iert_is_show_front_page"><?php _e('Show in Front Page:', IERT_DOMAIN); ?></label>
					</th>
					<td>
						<input type="checkbox" id="iert_is_show_front_page" name="iert_is_show_front_page" value="true" <?php echo ($iert_is_show_front_page ? 'checked="checked"' : '') ?> />
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">
						<label for="iert_is_show_single"><?php _e('Show in Post:', IERT_DOMAIN); ?></label>
					</th>
					<td>
						<input type="checkbox" id="iert_is_show_single" name="iert_is_show_single" value="true" <?php echo ($iert_is_show_single ? 'checked="checked"' : '') ?> />
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">
						<label for="iert_is_show_page"><?php _e('Show in Page:', IERT_DOMAIN); ?></label>
					</th>
					<td>
						<input type="checkbox" id="iert_is_show_page" name="iert_is_show_page" value="true" <?php echo ($iert_is_show_page ? 'checked="checked"' : '') ?> />
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">
						<?php _e('Show in custom post types (available only when "content" exists):', IERT_DOMAIN); ?>
					</th>
					<td>
						<?php
						foreach ( $post_types as $post_type ) { ?>
							<input type="checkbox" id="iert_is_show_<?php echo $post_type->name ?>" name="iert_is_show_<?php echo $post_type->name ?>" value="true" <?php echo ($iert_is_show_custom_post_types[$post_type->name] ? 'checked="checked"' : '') ?> />
							<label for="iert_is_show_<?php echo $post_type->name ?>"><?php echo $post_type->label ?></label>
							<br/>
						<?php } ?>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">
						<?php _e('Shortcode', IERT_DOMAIN); ?>
					</th>
					<td>
						<?php _e('Add <code>[estimated_reading_time]</code> to your post content.', IERT_DOMAIN); ?><br />
						<?php _e('Estimated reading time is displayed regardless of the state of checkbox.', IERT_DOMAIN); ?>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">
						<?php _e('Tag', IERT_DOMAIN); ?>
					</th>
					<td>
						<?php _e('Add <code>&lt;?php estimated_reading_time(); ?&gt;</code> to your theme/template.', IERT_DOMAIN); ?><br />
						<?php _e('Estimated reading time is displayed regardless of the state of checkbox.', IERT_DOMAIN); ?>
					</td>
				</tr>

			</table>

			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>

		</form>
	</div>

<?php

}

/**
 * Update option (checkbox)
 */
function iert_update_option($name, $post) {
	if (isset($post[$name])) {
		update_option($name, $post[$name]);
	} else {
		update_option($name, null);
	}
}

/**
 * Add options page.
 */
function iert_add_options_page() {
	add_options_page(
		__('Insert Estimated Reading Time Options', IERT_DOMAIN),
		__('Insert Estimated Reading Time', IERT_DOMAIN),
		'administrator',
		__FILE__,
		'iert_options_page');
}

/**
 * Shortcode.
 * @return string Edited content or original content.
 */
function iert_shortcode() {
	return get_estimated_reading_time(get_the_content());
}

/**
 * Echo edited content or original content.
 */
function estimated_reading_time() {
	echo get_estimated_reading_time(get_the_content());
}

/**
 * Echo edited content or original content.
 * @return string Edited content or original content.
 * @deprecated 1.1.1 Use estimated_reading_time instead.
 */
function estimatedReadingTime() {
	estimated_reading_time();
}

register_activation_hook(__FILE__, 'iert_install');

register_deactivation_hook(__FILE__, 'iert_uninstall');
add_action('admin_menu', 'iert_add_options_page');
add_filter('the_content', 'iert_content', 1);
add_shortcode('estimated_reading_time', 'iert_shortcode');
add_action('upgrader_process_complete', 'iert_plugin_update');

?>