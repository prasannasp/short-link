<?php
/*
Plugin Name: Short Link
Plugin URI: http://www.prasannasp.net/wordpress-plugins/short-link/
Description: This plugin automatically adds a Short Link for all blog posts after post content. Short Links are like http://example.com/?p=123. They are easy to remember and share!
Version: 1.0
Author: Prasanna SP
Author URI: http://www.prasannasp.net/
*/

/*  This file is part of Short Link plugin. Copyright Prasanna SP (email: prasanna[AT]prasannasp.net)

    Short Link plugin is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Short Link plugin is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Short Link plugin.  If not, see <http://www.gnu.org/licenses/>.
*/
function short_link_main_function($showshortlink) {
	$options = get_option('short_link_options');
	$shortlinkposttitle = $options['short_link_post_title'];
	$shortlinkpagetitle = $options['short_link_page_title'];

	if ( is_single() && ! isset($options['hide_on_posts']) )
 {

	$showshortlink .= '<p class="short-link" id="short-link"><strong>'.$shortlinkposttitle.'</strong><br /><input type="text" id="shortlink" onclick="ShortLinkSelectAll(\'shortlink\');" size="18" value="'.get_site_url().'/?p='.get_the_ID().'" /></p>';
	}
	
	elseif ( is_page() && isset($options['show_on_pages']) )
 {
 	$showshortlink .= '<p class="short-link" id="short-link"><strong>'.$shortlinkpagetitle.'</strong><br /><input type="text" id="shortlink" onclick="ShortLinkSelectAll(\'shortlink\');" size="18" value="'.get_site_url().'/?p='.get_the_ID().'" /></p>';
	}

return $showshortlink;
}
add_filter('the_content', 'short_link_main_function');

function short_link_select_script() {
	$options = get_option('short_link_options');
	if ( is_single() && ! isset($options['hide_on_posts']) || is_page() && isset($options['show_on_pages']) ) {
?>
<script type="text/javascript">
function ShortLinkSelectAll(id)
{
    document.getElementById(id).focus();
    document.getElementById(id).select();
}
</script>
<?php
	}
}
add_action('wp_footer', 'short_link_select_script');

// Set-up Action and Filter Hooks
register_activation_hook(__FILE__, 'short_link_add_defaults');
register_uninstall_hook(__FILE__, 'short_link_delete_plugin_options');
add_action('admin_init', 'short_link_init' );
add_action('admin_menu', 'short_link_add_options_page');
add_filter('plugin_action_links', 'short_link_plugin_action_links', 10, 2 );

// Delete options table entries ONLY when plugin deactivated AND deleted
function short_link_delete_plugin_options() {
	delete_option('short_link_options');
}

function short_link_add_defaults() {
	$tmp = get_option('short_link_options');
	if(($tmp['short_link_default_options_db']=='1')||(!is_array($tmp))) {
		$arr = array(	"short_link_post_title" => "Short Link:",
				"short_link_page_title" => "Short Link:",
				"short_link_copy_text" => "Copy", // Will add the copy option later
				"short_link_default_options_db" => ""

		);
		update_option('short_link_options', $arr);
	}
   }

function short_link_init(){
	register_setting( 'short_link_plugin_options', 'short_link_options', 'short_link_validate_options' );
}

function short_link_add_options_page() {
	add_options_page('Short Link Options Page', 'Short Link', 'manage_options', __FILE__, 'short_link_options_page_form');
}

/*
** Thanks to David Gwyer for Plugin Options Starter Kit plugin! wordpress.org/extend/plugins/plugin-options-starter-kit/
*/

function short_link_options_page_form() {
	?>
	<div class="wrap">

		<div class="icon32" id="icon-options-general"><br></div>
		<h2>Short Link Options</h2>
		<h3>Set your options for Short Link plugin.</h3>

		<form method="post" action="options.php">
			<?php settings_fields('short_link_plugin_options'); ?>
			<?php $options = get_option('short_link_options');
			      $shortlinkadminpagetitle = $options['short_link_post_title']; ?>

			<table class="form-table">
			
				<h4>Title Settings</h4>
				<tr>
					<th scope="row">Short Link title for posts:</th>
					<td>
						<input type="text" size="50" name="short_link_options[short_link_post_title]" value="<?php echo $options['short_link_post_title']; ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row">Short Link for pages:</th>
					<td>
						<input type="text" size="50" name="short_link_options[short_link_page_title]" value="<?php echo $options['short_link_page_title']; ?>" />
					</td>
				</tr>
			</table>
			
			<table class="form-table">

				<h4>Short Link Appearance</h4>
				<tr>
					<th scope="row">Show Short Link on pages</th>
					<td>
						<label><input name="short_link_options[show_on_pages]" type="checkbox" value="1" <?php if (isset($options['show_on_pages'])) { checked('1', $options['show_on_pages']); } ?> /> <br />
					</td>
				</tr>
			<tr valign="top">
					<th scope="row">Hide Short Link on blog posts</th>
					<td>
						<label><input name="short_link_options[hide_on_posts]" type="checkbox" value="1" <?php if (isset($options['hide_on_posts'])) { checked('1', $options['hide_on_posts']); } ?> /> <br />
					</td>
				</tr>
				<tr><td colspan="2"><div style="margin-top:10px;"></div></td></tr>
				<tr valign="top" style="border-top:#dddddd 1px solid;">
					<th scope="row">Database Options:</th>
					<td>
						<label><input name="short_link_options[short_link_default_options_db]" type="checkbox" value="1" <?php if (isset($options['short_link_default_options_db'])) { checked('1', $options['short_link_default_options_db']); } ?> /> Restore defaults upon plugin deactivation/reactivation</label>
						<br /><span style="color:#666666;margin-left:2px;">Only check this if you want to reset plugin settings upon Plugin reactivation</span>
					</td>
				</tr>
			</table>
			<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		<div style="border:6px ridge orange;">
			<table class="form-table">
				<tr>
					<th scope="row"><h3>Live example</h3></th>
					<td>
						<p class="short-link-admin" id="short-link-admin"><strong><?php echo $shortlinkadminpagetitle; ?></strong><br /><input type="text" class="short-link" id="short-link" size="18" value="<?php echo get_site_url(); ?>/?p=123" /></p>
					</td>
				</tr>
			</table>
		</div>
		</form>
<hr />
<p style="margin-top:15px;font-size:12px;">If you have found this plugin is useful, please consider making a <a href="http://prasannasp.net/donate/" target="_blank">donation</a> to support the further development of this plugin. Thank you!</p>
	</div>
	<?php	
}

// Sanitize and validate input
function short_link_validate_options($input) {
	 // strip html from textboxes
	$input['short_link_post_title'] =  wp_filter_nohtml_kses($input['short_link_post_title']); // strip html tags, and escape characters
	$input['short_link_page_title'] =  wp_filter_nohtml_kses($input['short_link_page_title']);
	return $input;
}

// Display a Settings link on the main Plugins page
function short_link_plugin_action_links( $links, $file ) {

	if ( $file == plugin_basename( __FILE__ ) ) {
		$short_link_links1 = '<a href="'.get_admin_url().'options-general.php?page=short-link/short-link.php" title="Short Link Settings Page">'.__('Settings').'</a>';
		$short_link_links2 = '<a href="http://forum.prasannasp.net/forum/plugin-support/short-link/" title="Short Link Support Forum" target="_blank">'.__('Support').'</a>';
		
		// make the 'Settings' link appear first
		array_unshift( $links, $short_link_links1, $short_link_links2 );
	}

	return $links;
}

// Donate link on manage plugin page
function short_link_pluginspage_links( $links, $file ) {

$plugin = plugin_basename(__FILE__);

// create links
if ( $file == $plugin ) {
return array_merge(
$links,
array( '<a href="http://www.prasannasp.net/donate/" target="_blank" title="Donate for this plugin via PayPal">Donate</a>',
'<a href="http://www.prasannasp.net/wordpress-plugins/" target="_blank" title="View more plugins from the developer">More Plugins</a>',
'<a href="http://twitter.com/prasannasp" target="_blank" title="Follow me on twitter!">twitter!</a>'
 )
);
			}
return $links;

	}
add_filter( 'plugin_row_meta', 'short_link_pluginspage_links', 10, 2 );
?>
