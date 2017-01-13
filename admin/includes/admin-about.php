<?php
if(!defined('WPINC')) {
	die;
}

require_once(LV_PATH.'includes/options.php');

// This class handles all data for the admin about page
class LV_Admin_About {
	private static $instance;
	private $options;

	private function __construct() {
		$this->options = &LV_Options::get_instance();
	}

	public static function &get_instance() {
		// singleton setup
		if(!isset(self::$instance)) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	// show the admin about page
	public function show_page() {
		// check required privilegs
		if(!current_user_can($this->options->get('lv_req_cap'))) {
			wp_die(__('You do not have sufficient permissions to access this page.'));
		}
		// create content
		$out ='
			<div class="wrap nosubsub">
			<div id="icon-link-manager" class="icon32"><br /></div><h2>About LinkView</h2></div>
			<h3>Help and Instructions</h3>
			<h4>Create a page or post with links</h4>
			<div class="help-content">
				<p>"LinkView" works by using a "shortcode" in a page or post.</p>
				<p>Shortcodes are snippets of pseudo code that are placed in blog posts or pages to easily render HTML output.<br />
				To create a link page or post add the shortcode <code>[linkview]</code> in the text field of any page or post.</p>
				<p>There are many shortcode attributes available which let you change the listed links and their styling.<br />
				To get the correct result you can combine as much attributes as you want.<br />
				E.g. the shortcode including the attributes "cat_name" and "show_img" would look like this:<br />
				<code>[linkview cat_name=Sponsors show_img=1]</code><br />
				Below you can find a list with all supported attributes, their descriptions and available options.</p>
			</div>
			<h4>LinkView Widget</h4>
			<div class="help-content">
				With the LinkView Widget you can add links in sidebars and widget areas.<br />
				Goto <a href="'.admin_url('widgets.php').'">Appearance &rarr; Widgets</a> and add the "LinkView"-Widget in one of your sidebars.<br />
				You can enter a title for the widget and add all the required shortcode attributes in the appropriate field.<br />
				You can use all available shortcode attributes of the linkview-shortcode in the widget too.<br />
				Press "Save" to activate the changes.
			</div>
			<h4>Settings</h4>
			<div class="help-content">
				In the linkview settings page, available under <a href="'.admin_url('options-general.php?page=lv_admin_options').'">Settings &rarr; LinkView</a>, you can find some options to modify the plugin.
			</div>
			<h3>About</h3>
			<div class="help-content">
				<p>This plugin is developed by mibuthu, you can find more information about the plugin on the <a href="http://wordpress.org/plugins/link-view">wordpress plugin site</a>.</p>
				<p>If you like the plugin please give me a good rating on the <a href="http://wordpress.org/support/view/plugin-reviews/link-view">wordpress plugin review site</a>.<br />
				<p>If you want to support the plugin I would be happy to get a small donation:<br />
				<a class="donate" href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=4ZHXUPHG9SANY" target="_blank" rel="noopener"><img src="'.LV_URL.'admin/images/paypal_btn_donate.gif" alt="PayPal Donation" title="Donate with PayPal" border="0"></a>
				<a class="donate" href="https://flattr.com/submit/auto?user_id=mibuthu&url=https%3A%2F%2Fwordpress.org%2Fplugins%2Flink-view" target="_blank" rel="noopener"><img src="'.LV_URL.'admin/images/flattr-badge-large.png" alt="Flattr this" title="Flattr this" border="0"></a></p>
			</div>';
			$out .= $this->html_atts();
		echo $out;
	}

	private function html_atts() {
		require_once(LV_PATH.'includes/sc_linkview.php');
		$shortcode = &SC_Linkview::get_instance();
		$shortcode->load_sc_linkview_helptexts();
		$out = '
			<h3>Shortcode Attributes</h3>
			<div class="help-content">
				In the following tables you can find all available shortcode attributes for <code>[linkview]</code>:
				';
		$out .= '<h4 class="atts-section-title">General:</h4>';
		$out .= $this->html_atts_table($shortcode->get_atts('general'));
		$out .= '<h4 class="atts-section-title">Link List:</h4>';
		$out .= $this->html_atts_table($shortcode->get_atts('list'));
		$out .= '<h4 class="atts-section-title">Link Slider:</h4>';
		$out .= $this->html_atts_table($shortcode->get_atts('slider'));
		$out .= '<br />
				<h4 class="atts-section-title">Multi-column layout types and options:</h4><a id="multicol"></a>
				There are 3 different types of multiple column layouts available for category or link multi-column view. Each type has some advantages and disadvantages compared to the others.
				<p>Additionally the available layouts can be modified with their options:</p>
				<table class="atts-table">
				<tr><th>layout type</th><th>type description</th></tr>
				<tr><td>Number</td><td>Use a single number to specify a static number of columns.<br />
					This is a short form of the static layout type (see below).</td></tr>
				<tr><td>static</td><td>Set a static number of columns. The categories or links will be arranged in rows.
					<h5>available options:</h5>
					<em>num_columns</em>: Provide a single number which specifys the number of columns. If no value is given 3 will be used by default.</td></tr>
				<tr><td>css</td><td>This type uses the <a href="http://www.w3schools.com/css/css3_multiple_columns.asp" target="_blank" rel="noopener">multi-column feature of CSS</a> to arrange the columns.
					<h5>available options:</h5>
					You can use all available properties for CSS3 Multi-column Layout (see <a href="http://www.w3schools.com/css/css3_multiple_columns.asp" target="_blank" rel="noopener">this link</a> for detailed information).<br />
					The given attributes will be added to the wrapper div element. Also the prefixed browser specific attributes will be added.</td></tr>
				<tr><td>masonry</td><td>This type uses the <a href="http://masonry.desandro.com/" target="_blank" rel="noopener">Masonry grid layout javascript library</a> to arrange the columns.
					<h5>available options:</h5>
					You can use all Options which are available for the Masonry library (see <a href="http://masonry.desandro.com/options.html" target="_blank" rel="noopener">masonry options</a> for detailed information).<br />
					The given options will be provided to the Masonry javascript library.</td></tr>
				</table>
				<div class="help-content">
					<h5>Usage:</h5>
					For the most types and options it is recommended to define a fixed width for the categories and/or links. This width must be set manually e.g. via the css entry: <code>.lv-multi-column { width: 32%; }</code><br />
					Depending on the type and options there are probably more css modifications required for a correct multi-column layout.<br />
					There are different ways to add required css code, one method is the link-view setting "CSS-code for linkview" which can be found in <a href="'.admin_url('options-general.php?page=lv_admin_options').'">Settings &rarr; LinkView</a>.<br />
					The optional type options must be added in brackets in the format "option_name=value", multiple options can be added seperated by a pipe ("|").
					<h5>Examples:</h5>
					<p><code>[linkview cat_columns=3]</code> &hellip; show the categories in 3 static columns</p>
					<p><code>[linkview link_columns="static(num_columns=2)"]</code> &hellip; show the link-lists in 2 static columns</p>
					<p><code>[linkview cat_columns="css(column-width=4)"</code> &hellip; show the categories in columns with the css column properties with a fixed width per category</p>
					<p><code>[linkview links_columns="css(column-count=4|column-rule=4px outset #ff00ff|column-gap=40px)"</code> &hellip; show the link-lists in 4 columns with multiple css column properties</p>
					<p><code>[linkview cat_columns="masonry(masonry(isOriginTop=false|isOriginLeft=false)"</code> &hellip; show the categories in columns with the masonry script (with some specific masonry options)</p>
				</div>
			</div>';
		return $out;
	}

	private function html_atts_table($atts) {
		$out = '
			<table class="atts-table">
				<tr>
					<th class="atts-table-name">Attribute name</th>
					<th class="atts-table-options">Value options</th>
					<th class="atts-table-default">Default value</th>
					<th class="atts-table-desc">Description</th>
				</tr>';
		foreach($atts as $aname => $a) {
			$val = is_array($a['val']) ? implode('<br />', $a['val']) : $a['val'];
			$out .= '
				<tr>
					<td>'.$aname.'</td>
					<td>'.$val.'</td>
					<td>'.$a['std_val'].'</td>
					<td>'.$a['desc'].'</td>
				</tr>';
		}
		$out .= '
			</table>
			';
		return $out;
	}
} // end class LV_Admin_About
?>
