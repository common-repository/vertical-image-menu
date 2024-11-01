<?php
/*
Plugin Name: Vertical Image Menu
Plugin URI: http://software.bmo-design.de/wordpress-plugins/wordpress-plugin-vertical-image-menu.html
Description: This wordpress plugin provides a shortcode to add a vertical scrolling image or icon menu. 
Author: Benedikt Morschheuser
Author URI: http://bmo-design.de/
Version: 1.0.1

#################################################################

                    
#################################################################
*/ 
// Restrictions
  if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

//###############################################################
  
  define('VIM_SITEBASE_URL'  , get_option('siteurl'));
  define('VIM_PLUGINNAME', trim(plugin_basename(dirname(__FILE__))));
  define('VIM_URL', get_option('siteurl').'/wp-content/plugins/' . dirname(plugin_basename(__FILE__))); // get_bloginfo('wpurl')
  define('VIM_BASEPATH', WP_PLUGIN_DIR.'/'. dirname(plugin_basename(__FILE__)));

//###############################################################
	
require_once (VIM_BASEPATH . '/admin/admin.php');
	
class verticalImageMenu {
	
	 private $vimversion = "1.0.1"; //Version
	  
	 public function vimReplaceShortcode($atts, $content = '') { //new Version, see http://codex.wordpress.org/Shortcode_API
		
		if(!is_array($atts))
			$atts=array();
	
		$vimconfig = $this->get_VIMConfiguration($atts);
		$out = $this->vimBuildHTML($vimconfig);
			
		return $out.$content;
	 }
	 public function vimBuildHTML($vimconfig, $pictures = null) {
		$html = '';
		
		extract($vimconfig);
		$images_html = "";
		/*html in variable $images in korrektes HTML umwandeln, nach Wordpress Art. Individuell anpassen!!!*/
		if(isset($images)&&$images!=''){
			/*$output = $images;
			$output = apply_filters('wptexturize', $output);
			$output = apply_filters('convert_smilies', $output);
			$output = apply_filters('convert_chars', $output);
			$output = apply_filters('wpautop', $output);
			$output = apply_filters('shortcode_unautop', $output);
			$output = apply_filters('prepend_attachment', $output);*/
			$images_html .= "<html><body>".apply_filters('the_content', $images)."</body></html>";
		
			//image-html in Array wandeln durch coole PHP5 DOMDocument class
			$doc = new DOMDocument();
			$doc->loadHTML($images_html);
			$linkTags = $doc->getElementsByTagName('a');
			if ($linkTags->length>0) {
				$html = '<div class="vertical_imagemenu" style="width:'.$width.';height:'.$height.';"><table>';
				foreach ($linkTags as $linkTag) {
					$imageTag = $linkTag->getElementsByTagName('img')->item(0);//erstes img im a
					$html .= '<tr><td><a href="'.$linkTag->getAttribute('href').'" target="'.$linkTag->getAttribute('target').'"  title="'.$linkTag->getAttribute('title').'"><img style="width:'.$width.';" src="'.$imageTag->getAttribute('src').'" alt="'.$imageTag->getAttribute('alt').'" title="'.$imageTag->getAttribute('title').'"/></a></td></tr>';
				}
				$html .= '</table></div>';
			}
			
		}
		return $html;
	 }
	 
	 public function get_VIMConfiguration($atts) {
		//build vimconfig from parameter and options
		$options = get_option("VIM_Options");
		$vimconfig = array();
		$vimconfig["speed"] = (int)    ( (array_key_exists("speed"     , $atts))? $atts["speed"] : $options["VIM_speed"] );
		$vimconfig["width"] = (string) ( (array_key_exists("width"     , $atts))? $atts["width"] : $options["VIM_width"] );
		$vimconfig["height"]= (string) ( (array_key_exists("height"    , $atts))? $atts["height"]: $options["VIM_height"]);
		$vimconfig["images"]= (string) ( (array_key_exists("images"    , $atts))? $atts["images"]: $options["VIM_images"]);
		
		return $vimconfig;
	 }
	 public function vimEnqueueScripts(){
		 wp_deregister_script(array('scrollTo'));
		 wp_deregister_script(array('verticalImageMenu'));
		 wp_register_script( 'scrollTo', VIM_URL.'/js/jquery.scrollTo.js', false, '1.4.2');
		 wp_register_script( 'verticalImageMenu', VIM_URL.'/js/verticalImageMenu.js', array('scrollTo'), '1.0');
		 wp_register_style('verticalImageMenu', VIM_URL.'/verticalImageMenu.css',false,$this->vimversion,'screen');
		  
		 
		 wp_enqueue_script('jquery');
		 wp_enqueue_script('scrollTo');
		 wp_enqueue_script('verticalImageMenu');
		 wp_enqueue_style('verticalImageMenu');
	 }
	 public function vimHead(){
		 $options = get_option("VIM_Options");
		 echo '
		 	   <!-- Vertical Image Menu '.$this->vimversion.' -->
			   <script type="text/javascript">var vimSpeed = '.$options["VIM_speed"].';</script>';
	 }
	 //install
	 public function activation() {
		$options = array(
			'VIM_speed'  =>'20',
			'VIM_width'  => '148px',
			'VIM_height'  =>'400px',
			'VIM_images' => ''
			);
		add_option("VIM_Options",$options);//It does nothing if the option already exists. 
		
	 }
	 public function deactivation() {
		wp_deregister_script(array('scrollTo'));
		wp_deregister_script(array('verticalImageMenu'));
	 }
	 
	 public function VIMRegisterPluginLinks($links, $file){
		$plugin = plugin_basename(__FILE__);
		if ($file == $plugin) {
			return array_merge(
				$links,
				array( sprintf( '<a href="admin.php?page=%s">%s</a>', VIM_PLUGINNAME."-admin", __('Settings') ), '<g:plusone size="small"  href="http://software.bmo-design.de/wordpress-plugins/wordpress-plugin-vertical-image-menu.html"></g:plusone>' )
			);
		}
		return $links;
	} 
}



$verticalImageMenu = new verticalImageMenu();
$verticalImageMenuAdmin = new verticalImageMenuAdmin($verticalImageMenu);

if (isset($verticalImageMenu)&&isset($verticalImageMenuAdmin)) {
	// Plugin installieren bei aktivate
	register_activation_hook( __FILE__,  array($verticalImageMenu, 'activation'));
	register_deactivation_hook(__FILE__, array($verticalImageMenu, 'deactivation'));
	
	add_action('admin_menu' , array($verticalImageMenuAdmin, 'admin_menu'));//add menu
	add_action('admin_init', array($verticalImageMenuAdmin, 'admin_init'));//init settings for Admin Page
	add_filter('plugin_row_meta', array($verticalImageMenu,'VIMRegisterPluginLinks'), 10, 2 );
	//
	
	add_shortcode('verticalImageMenu', array($verticalImageMenu, 'vimReplaceShortcode'));
	add_shortcode('verticalimagemenu', array($verticalImageMenu, 'vimReplaceShortcode'));
	
	// Hook wp_head to add css
	add_action('wp_enqueue_scripts', array($verticalImageMenu,'vimEnqueueScripts'));
	add_action('wp_head'   , array($verticalImageMenu,'vimHead'),1);
}

//Deinstall (outside Class)
if ( function_exists('register_uninstall_hook') )
	register_uninstall_hook(__FILE__, 'deinstallVIM');

function deinstallVIM() {
	delete_option("VIM_Options");
}
?>