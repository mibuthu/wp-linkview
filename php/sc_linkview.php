<?php

// This class handles the shortcode [linkview]
class sc_linkview {

	// All available attributes
	public static $attr = array(

		'view_type'		=> array(	'val'		=> 'list<br />slider',
									'std_val'	=> 'list',
									'desc'		=> 'This attribute specifies how the links are displayed. The standard is to show the links in a list.<br />
													The second option is to show the links in a slider. This normally only make sense if you show the images, but it is also possible to show the link name with this option.'),

		'cat_name' 		=> array(	'val'		=> 'Name',
									'std_val'	=> '',
									'desc'		=> 'This attribute specifies what category should be shown. If you leave the attribute empty all categories are shown.<br />
													If the cat_name has spaces, simply wrap the name in quotes.<br />
													Example: <code>[linkview cat_name="Social Media"]</code>' ),

		'show_img'		=> array(	'val'		=> '0 ... false<br />1 ... true',
									'std_val'	=> '0',
									'desc'		=> 'This attribute specifies if the image is displayed instead of the name. This attribute is only considered for links where an image was set.' ),

		'show_cat_name'	=> array(	'val'		=> '0 ... false<br />1 ... true',
									'std_val'	=> '1',
									'desc'		=> 'This attribute specifies if the category name is shown as a headline.'),

		'target'		=> array(	'val'		=> 'blank<br />top<br />none',
									'std_val'	=> '',
									'desc'		=> 'Enter "blank", "top" or "none" to overwrite the standard value which was set for the link. Leave this field empty if you don´t want to overwrite the standard.')
		);

	// main function to show the rendered HTML output
	public static function show_html( $atts ) {
		
		// check attributes
		$std_values = array();
		foreach( sc_linkview::$attr as $aname => $attribute ) {
			$std_values[$aname] = $attribute['std_val'];
		}
		$a = shortcode_atts( $std_values, $atts );

		// set categories
		$categories = sc_linkview::categories( $a );
		
		foreach( $categories as $cat ) {
			// get links
			$args = array(
				'orderby'        => 'name',
				'limit'          => -1,
				'category_name'  => $cat->name);
			$links = get_bookmarks( $args );

			// generate output
			if( !empty( $links ) ) {
				$out .= sc_linkview::html_category( $cat, $a );
				if( $a['view_type'] == 'slider' ) {
					$out .= sc_linkview::html_link_slider( $links, $a );
				}
				else {
					$out .= sc_linkview::html_link_list( $links, $a );
				}
			}
		}
		return $out;
	}

	public static function categories( $a ) {
		if( empty( $a['cat_name'] ) ) {
			return get_terms('link_category', 'orderby=count&hide_empty=0');
		}
		else {
			return array( get_term_by( 'name', $a['cat_name'], 'link_category', 'orderby=count&hide_empty=0' ) );
		}
	}

	public static function img_width( $a ) {
		return 500;
	}

	public static function img_height( $a ) {
		return 300;
	}

	public static function html_category( $cat, $a ) {
		$out = '';
		if( $a['show_cat_name'] > 0 ) {
			$out .= '
					<h2>'.$cat->name.'</h2>';
		}
		return $out;
	}

	public static function html_link_list( $links, $a ) {
		$out .= '
			<ul>';
		foreach( $links as $link ) {
			$out .= '<li'.sc_linkview::html_link( $link, $a ).'</li>';
		}
		$out .= '
			</ul>';
		return $out;
	}

	public static function html_link_slider( $links, $a ) {
		// javascript
		$out = '
			<script type="text/javascript" src="'.LV_URL.'js/jquery.js"></script>
			<script type="text/javascript" src="'.LV_URL.'js/easySlider.js"></script>
			<script type="text/javascript">
				$(document).ready(function(){	
					$("#slider").easySlider({
						auto: true,
						speed: 1000,
						pause: 5000,
						continuous: true,
						controlsShow: false
					});
				});	
			</script>';
		// styles
		$out .= '
			<style>
				#slider ul, #slider li {
					margin:0;
					padding:0;
					list-style:none;
				}
				#slider li { 
					width:'.sc_linkview::img_width( $a ).'px;
					height:'.sc_linkview::img_height( $a ).'px;
					overflow:hidden; 
				}
			</style>';
		// html
		$out .= '
			<div id="slider">
				<ul>';
		// links
		foreach( $links as $link ) {
			$out .= '<li>'.sc_linkview::html_link( $link, $a ).'</li>';
		}
		$out .= '	
				</ul>
			</div>';
		return $out;
	}

	public static function html_link( $l, $a ) {
		$out = '
					<a href="'.$l->link_url;
		
		switch( $a['target'] ) {
			case 'blank':
				$target = '_blank';
				break;
			case 'top':
				$target = '_top';
				break;
			case 'none':
				$target = '_none';
				break;
			default:
				$target = $l->link_target;
		}
		$out .= '" target="'.$target.'">';

		if( $a['show_img'] > 0 && $l->link_image != null ) {
			$out .= '<img src="'.$l->link_image./*'" width="'.sc_linkview::img_width( $a ).'px" height="'.sc_linkview::img_height( $a ).'px"*/'" alt="'.$l->link_name.'" />';
		}
		else {
			$out .= $l->link_name;
		}

		$out .= '</a>';
	return $out;
	}
}
?>