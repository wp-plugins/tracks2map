<?php
/**
 * Plugin Name: Tracks2Map
 * Description: Collect GPX track points into one map.
 * Version: 1.2
 * Author: Giorgio25b from the original version of Die Wanderer
 * Author URI: http://giorgioriccardi.com
 * License: GPL2
 */

/*  Copyright 2015  WorkingDesign  (email : me@giorgioriccardi.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/* 
 * Creates a custom post type for ELC publications
 * Also sets custom taxonomies
 */

class gr80_tracks2map
{
	
	const cache_dir = 'cachegpx';
	
	static function init()
	{
		add_action('admin_menu', array(__CLASS__, 'admin_menu'));
		//add_action('wp', array(__CLASS__, 'wp'));
		add_action('wp_print_footer_scripts', array(__CLASS__, 'scripts'));
		
		add_action('publish_post', array(__CLASS__, 'search_and_convert'));
		
		add_filter('upload_mimes', array(__CLASS__, 'upload_mimes'));
		
		add_shortcode('tracks2map', array(__CLASS__, 'display'));
	}

	// Original Snippet

	static function admin_menu()
	{
		add_menu_page('Tracks2Map Configuration', 'Tracks2Map', 'manage_options', 'tracks2map', array(__CLASS__, 'options'));
	}

	// grc

	// static function admin_menu() {

	// 	$debug = false;
		
	// 	{
	// 		if ( $debug == true ) //run the original code
	// 		{
	// 			add_menu_page('Tracks2Map Configuration', 'Tracks2Map', 'manage_options', 'tracks2map', array(__CLASS__, 'options'));
	// 		}

	// 		else //run the debug code
	// 		{
	// 			add_menu_page('grc-Tracks2Map Configuration', 'grc-Tracks2Map', 'manage_options', 'tracks2map', array(__CLASS__, 'options'));
	// 		}
	// 	}
	// }
	
	static function upload_mimes($mimes = array())
	{
		$mimes['gpx'] = 'text/xml';
		return $mimes;
	}
	
	static function options()
	{
		if($_POST)
			static::options_save();
		
		if($_POST['rebuild'])
			static::clear_cache();
			
		if($_POST['update'] || $_POST['rebuild'])
			static::search_and_convert();
		
		?>
		<div class="wrap">
			
			<?php screen_icon('edit') ?>
			<h2><?php _e('Tracks2Map Configuration') ?></h2>
			
			<form action="" class="form-table" method="post">
				
				<table class="widefat">
					<tr>
						<th scope="row">
							Tolerance
						</th>
						<td>
							<input type="text" name="t2m_tolerance" id="" value="<?php echo get_site_option('t2m_tolerance', 0.005) ?>" />
						</td>
					</tr>
					<tr>
						<th scope="row">
							Post Query String (Advanced)
						</th>
						<td>
							<!-- Original string -->

							<input type="text" name="t2m_post_query" id="" value="<?php echo get_site_option('t2m_post_query', 'post_type=post&posts_per_page=-1') ?>" />

							
							<!-- Implemented new WP_Query https://codex.wordpress.org/Class_Reference/WP_Query -->

							<!-- <input type="text" name="t2m_post_query" id="" value="<?php //echo get_site_option( 't2m_post_query', "$wp_query = new WP_Query( array('paged'=> get_query_var('paged') ? get_query_var('paged') : 1,'post_type'=> 'post','post_status'=> 'publish','posts_per_page'=> -1) );" ) ?>" /> -->

<!-- global $wp_query, $wp_the_query; -->

<!-- $wp_query = new WP_Query( array(
    'paged'             => get_query_var('paged') ? get_query_var('paged') : 1,
    'post_type'         => 'post',
    'post_status'       => 'publish',
    'posts_per_page'    => -1
) ); -->

						</td>
					</tr>


    

 					<tr>
						<th scope="row">
							tag_ID for icon: 1.png
						</th>
						<td>
							<input type="text" name="t2m_icon1" id="" value="<?php echo get_site_option('t2m_icon1', '212') ?>" /> Insert here the tag_ID for icon: 1.png
						</td>
					</tr>
					<tr>
						<th scope="row">
							tag_ID for icon: 2.png
						</th>
						<td>
							<input type="text" name="t2m_icon2" id="" value="<?php echo get_site_option('t2m_icon2', '11') ?>" /> Insert here the tag_ID for icon: 2.png
						</td>
					</tr>
					<tr>
						<th scope="row">
							tag_ID for icon: 3.png
						</th>
						<td>
							<input type="text" name="t2m_icon3" id="" value="<?php echo get_site_option('t2m_icon3', '199') ?>" /> Insert here the tag_ID for icon: 3.png
						</td>
					</tr>
					<tr>
						<th scope="row">
							tag_ID for icon: 4.png
						</th>
						<td>
							<input type="text" name="t2m_icon4" id="" value="<?php echo get_site_option('t2m_icon4', '211') ?>" /> Insert here the tag_ID for icon: 4.png
						</td>
					</tr>
					<tr>
						<th scope="row">
							tag_ID for icon: 5.png
						</th>
						<td>
							<input type="text" name="t2m_icon5" id="" value="<?php echo get_site_option('t2m_icon5', '762') ?>" /> Insert here the tag_ID for icon: 5.png
						</td>
					</tr>                           
                    
                    
                    
                    
					<tr>
						<th scope="row">
							Credit link
						</th>
						<td>
							<input type="text" name="t2m_creditlink" id="" value="<?php echo get_site_option('t2m_creditlink', 'no') ?>" /> This Plugin by default does not display a credit link. Please consider inserting a dofollow link to www.diewanderer.it or to www.diewanderer.it/tracks2map/ on your website, or activate our credit link below the map by writing "yes", without quotation marks, in this field.
						</td>
					</tr>
                    
                    
                                                                                                                    
                    
					<tr>
						<td colspan="2">
							<input type="submit" class="button-primary" value="Save" />
							<input type="submit" class="button-primary" name="refresh" value="Update Cache" />
							<input type="submit" class="button-primary" name="rebuild" value="Rebuild Cache" />
						</td>
					</tr>
				</table>
				
			</form>
			
		</div>
		
		<?php
		
	}
	
	static function options_save()
	{
		$keys = array('t2m_post_query', 't2m_tolerance', 't2m_creditlink', 't2m_icon1', 't2m_icon2', 't2m_icon3', 't2m_icon4', 't2m_icon5');
		$data = array_intersect_key($_POST, array_flip($keys));
		
		foreach($data as $k => $v)
			update_site_option($k, $v);
	}
	
	static function search_and_convert()
	{
		$post_query = get_site_option('t2m_post_query', 'post_type=post&posts_per_page=-1');

		// $post_query = get_site_option( 't2m_post_query', "$wp_query = new WP_Query( array('paged'=> get_query_var('paged') ? get_query_var('paged') : 1,'post_type'=> 'post','post_status'=> 'publish','posts_per_page'=> -1) );" );

// global $wp_query, $wp_the_query;

// $wp_query = new WP_Query( array(
//     'paged'             => get_query_var('paged') ? get_query_var('paged') : 1,
//     'post_type'         => 'post',
//     'post_status'       => 'publish',
//     'posts_per_page'    => -1
// ) );
		
		$atts = array();
		$posts = get_posts($post_query);
		foreach($posts as $post)
			$atts = array_merge($atts, get_children($post->ID));
		
		$atts = array_filter($atts, function($att){
			return false !== strpos($att->guid, ".gpx");
		});
		
		foreach($atts as $att)
			static::convertncache($att);
	}
	
	static function convertncache($att)
	{
		include_once dirname(__FILE__).'/inc/PolylineReducer.inc';
		include_once dirname(__FILE__).'/inc/Tracks2Map.inc';
		
		$uploaddir = wp_upload_dir();
		
		$filename = get_attached_file($att->ID);
		
		$cache_file_all = $att->ID . '.json';
		$cache_file_red = $att->ID . '.reduced.json';
		$cache_dir = $uploaddir['basedir'] . '/'.static::cache_dir.'/';
		
		if(file_exists($cache_dir . $cache_file_all))
			return;
		
		$t = new Tracks2Map;
		$t->tolerance = get_site_option('t2m_tolerance', 0.005);
		
		$t->convert($filename);
		$tracks['all'] = $t->points;
		$tracks['red'] = $t->reduced_points;
		
		$hJSON = fopen( $cache_dir . $cache_file_all, 'w');
		fwrite($hJSON, json_encode($tracks['all']));
		fclose($hJSON);
		
		$hJSON = fopen( $cache_dir . $cache_file_red, 'w');
		fwrite($hJSON, json_encode($tracks['red']));
		fclose($hJSON);
	}
	
	static function display($atts, $content)
	{
		global $wpdb;
		
		$atts = shortcode_atts(array(
			'height' => '650',
			'width' => '600',
			'zoom' => '8',
		), $atts);
		
		$uploaddir = wp_upload_dir();
		$cache_dir = $uploaddir['basedir'] . '/'.static::cache_dir.'/';
		
		$files = glob($cache_dir . '*.reduced.json');
		
		$icons = array(
			1 => '1.png',
			2 => '2.png',
			3 => '3.png',
			4 => '4.png',
			5 => '5.png',
			0 => '0.png',
		);
		
		$poly = array();
		
		foreach($files as $idx => $filename)
			$poly[$idx]['content'] = file_get_contents($filename);
		
		foreach($files as $idx => $filename){
			$att_id = preg_match('/\/([^\/]+).reduced.json/', $filename, $match) ? $match[1] : null;
			$parent = $wpdb->get_row("SELECT p.ID id, p.post_title title, p.guid url, a.ID attid, a.guid atturl, GROUP_CONCAT(tx.term_id SEPARATOR '|') txid 
			FROM $wpdb->posts a 
			JOIN $wpdb->posts p ON a.post_parent = p.ID AND p.post_status = 'publish' 
			LEFT JOIN $wpdb->term_relationships tr ON tr.object_id = p.ID
			LEFT JOIN $wpdb->term_taxonomy tx ON tx.taxonomy = 'category' AND tx.term_taxonomy_id = tr.term_taxonomy_id
			WHERE a.ID = ".intval($att_id)."
			GROUP BY a.ID
			", ARRAY_A);
			
			if(empty($parent))
				unset($poly[$idx]);
			else
				$poly[$idx] = array_merge($poly[$idx], $parent);
			
			unset($poly[$idx]['txid']);
			
			$cats = explode('|', $parent['txid']);
			if(in_array(get_site_option('t2m_icon5', '762'), $cats))
				$icon = 5;
			elseif(in_array(get_site_option('t2m_icon4', '211'), $cats))
				$icon = 4;
			elseif(in_array(get_site_option('t2m_icon3', '199'), $cats))
				$icon = 3;
			elseif(in_array(get_site_option('t2m_icon2', '11'), $cats))
				$icon = 2;
			elseif(in_array(get_site_option('t2m_icon1', '212'), $cats))
				$icon = 1;
			else
				$icon = 0;
			
			$poly[$idx]['icon'] = $icon;
		}
		
		?>
		<script>
		var poly = [];
		var markerIcons = <?php echo json_encode($icons) ?>;
		<?php foreach($poly as $i => $d): ?>
		poly[<?php echo $i ?>] = {
			id: <?php echo $poly[$i]['id'] ?>,
			url: "<?php echo htmlentities($poly[$i]['url']) ?>",
			attid: <?php echo $poly[$i]['attid'] ?>,
			atturl: "<?php echo htmlentities($poly[$i]['atturl']) ?>",
			title: "<?php echo htmlspecialchars($poly[$i]['title']) ?>",
			icon: <?php echo $poly[$i]['icon'] ?>,
			content: <?php echo $poly[$i]['content'] ?>
		};
		<?php endforeach; ?>
		var mapZoom = <?php echo $atts['zoom'] ?>;
		var iconBase = "<?php echo plugin_dir_url(__FILE__).'icons/'; ?>";
		</script><?php
		
		$content = "<div id='map_canvas' style='height:{$atts['height']}px;width:{$atts['width']}px;'></div><div id='overlay_map'></div>";
		
		if (get_site_option('t2m_creditlink', 'no')!="no")
		$content = $content . '<p>Credits:<a href="http://www.diewanderer.it/" target="_blank" title="Die Wanderer"> www.diewanderer.it</a></p>' ;
		
		
		static::scripts();
		
		return $content;
	}
	
	/*
	static function wp()
	{
		global $post;
		if( !is_page() || false === strpos($post->post_content, '[tracks2map'))
			return;
		
		wp_enqueue_script('gmaps3', 'http://maps.googleapis.com/maps/api/js?sensor=false', array(), null);
		wp_enqueue_script('infoBubble', plugin_dir_url(__FILE__).'infoBubble.js', 'gmaps3');
		wp_enqueue_script('tracks2map', plugin_dir_url(__FILE__).'Tracks2Map.js' , 'gmaps3');
	}
	*/
	
	static function scripts()
	{
		?>
		<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false"></script>
		<script type="text/javascript" src="<?php echo plugin_dir_url(__FILE__).'infoBubble.js' ?>"></script>
		<script type="text/javascript" src="<?php echo plugin_dir_url(__FILE__).'Tracks2Map.js' ?>"></script>

		<!-- grc added 2 scripts from online map:  http://www.diewanderer.it/wanderungen-suedtirol-karte/ -->
		<!-- this 2 scripts allow google maps Markers Clasterization, grouping icons in bubbles -->

		<script type="text/javascript" src="<?php echo plugin_dir_url(__FILE__).'markerclusterer/markerclusterer.js' ?>"></script>
		<script type="text/javascript" src="<?php echo plugin_dir_url(__FILE__).'markerclusterer/oms.min.js' ?>"></script>
		<?php
	}
	
	static function clear_cache()
	{
		$uploaddir = wp_upload_dir();
		$cache_dir = $uploaddir['basedir'] . '/'.static::cache_dir.'/';
		
		$files = glob($cache_dir.'*.json');
		foreach($files as $file)
			unlink($file);
	}
	
}

gr80_tracks2map::init();
register_activation_hook( __FILE__, array(gr80_tracks2map, 'search_and_convert') );