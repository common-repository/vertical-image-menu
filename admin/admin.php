<?php
/*
The Admin Interface

#################################################################
*/
class verticalImageMenuAdmin {
	
	private $vimAdminversion = "1.0.0"; //Version
	private $vimObj = "";
	 
	function __construct($vimObjcet) {
     	$this->vimObj=$vimObjcet;
  	}
	 
	public function admin_menu(){
		add_menu_page('Vertical Image Menu Admin', 'Vertical Image Menu', 'manage_options', VIM_PLUGINNAME."-admin", array($this, 'admin_general_page')); // add Admin Menu
	}
	public function admin_general_page() {
		global $wpdb;
		?>    
		<div class="wrap">
		  <h2>Vertical Image Menu Admin</h2>  
           <form action="options.php" method="post" id="post" name="post">  
           <div class="metabox-holder has-right-sidebar" id="poststuff">
				<div class="inner-sidebar" id="side-info-column">
          			<?php  do_meta_boxes( VIM_PLUGINNAME, 'side', NULL); ?>
                </div>
         		<div id="post-body">
					<div id="post-body-content">
                    	<div id="normal-sortables" class="meta-box-sortables">
                        	<div id="scrollGallery_main_box" class="postbox ">
								<?php settings_fields('vim_options'); ?>
                                <?php do_settings_sections('vim_options_section_el'); ?>
                                <div class="inside">
                                    <p><input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" style="margin-left:220px"/></p>
                                    <p>&nbsp;</p>
                                    <p>To add the menu to your post/page enter the tag <table style="background-color:#6CF; padding:4px;"><tr><td>[verticalImageMenu]</td></tr></table><br/> in your text.</p>
                                    <p>The options can be overridden in the post/page tag. For example: <table style="background-color:#6CF; padding:4px;"><tr><td>[verticalImageMenu speed=50 width="300px"]</td></tr></table></p>
                                    <p>If you like to use the shortcode in php, try: &lt;?php echo do_shortcode('[verticalImageMenu]') ?&gt;</p>
                      
                                    <p>That's it ... Have fun</p>
                                </div>
                        	</div>
               			 </div>
                     </div>
                     <br class="clear"/>
                </div>
          </div>
		  </form>
		</div>
        <div class="clear"></div>
		<?php
	 }  
	 
	public function admin_init(){
		
		//admin Header  
	   	wp_deregister_script(array('plusone'));
	   	wp_register_script( 'plusone', 'https://apis.google.com/js/plusone.js');
	   	if (function_exists('wp_enqueue_script')) {
			wp_enqueue_script('plusone');
		}
		//meta boxes
		add_meta_box('vim_meta_box', 'Do you like this Plugin?', array($this, 'VIM_like_MetaBox'), VIM_PLUGINNAME, 'side', 'core');//add_meta_box('scrollGallery_meta_box', 'Do you like this Plugin?', array($scrollGallery, 'nggSG_like_MetaBox'), 'nextgen-scrollgallery', 'right', 'core');
		//form
		register_setting( 'vim_options', 'VIM_Options', array($this,'VIM_options_validate') );
		add_settings_section('vim_options_section', 'Vertical Image Menu options', array($this,'VIM_options_section_html'), 'vim_options_section_el');
		add_settings_field('vim_options_field0', 'speed: (number) image scroll speed', array($this,'vim_options_field_html_speed'), 'vim_options_section_el', 'vim_options_section');
		add_settings_field('vim_options_field1', 'width: (number with px or %)', array($this,'vim_options_field_html_width'), 'vim_options_section_el', 'vim_options_section');
		add_settings_field('vim_options_field2', 'height: (number with px or %)', array($this,'vim_options_field_html_height'), 'vim_options_section_el', 'vim_options_section');
		add_settings_field('vim_options_field3', 'images: <br/><br/>Upload all your images here. Please do not write text or html tags. Only upload your images and insert them. Do not change the generated html code.<br/><br/>In the image insert dialog you can specify the menu url (in the visual mode).', array($this,'vim_options_field_html_images'), 'vim_options_section_el', 'vim_options_section');
		
	 }
	 public function VIM_options_section_html() {
	  // echo '<p>Here you can change the global VIM options:</p>';
	 }
	 public  function vim_options_field_html_speed() {
		$options = get_option("VIM_Options");
		echo "<input  name='VIM_Options[VIM_speed]' size='5' type='text' value='{$options['VIM_speed']}' />";
	 }
	 public function vim_options_field_html_width() {
		$options = get_option("VIM_Options");
		echo "<input  name='VIM_Options[VIM_width]' size='5' type='text' value='{$options['VIM_width']}' />";
	 }
	 public function vim_options_field_html_height() {
		$options = get_option("VIM_Options");
		echo "<input  name='VIM_Options[VIM_height]' size='5' type='text' value='{$options['VIM_height']}' />";
	 }
	 public function vim_options_field_html_images() {
		$options = get_option("VIM_Options");
		wp_editor( $options['VIM_images'], 'vim_imageeditor', $settings = array('textarea_name'=>'VIM_Options[VIM_images]','tinymce'=>true,'teeny'=>false,'editor_css'=>"<style>#vim_imageeditor_toolbargroup{display:none}</style>",'textarea_rows'=>25) );
	 }
	 public function VIM_options_validate($input) {
		$input['VIM_speed']  = (int) (is_numeric( $input['VIM_speed'] ) ? $input['VIM_speed'] : 20);
		$input['VIM_width']  = (string) (isset( $input['VIM_width'] ) ? htmlspecialchars(stripslashes($input['VIM_width']), ENT_QUOTES, 'UTF-8') :  "148px");
		$input['VIM_height']  = (string) (isset( $input['VIM_height'] ) ? htmlspecialchars(stripslashes($input['VIM_height']), ENT_QUOTES, 'UTF-8') :  "400px");
		// sehr wichtig, dass man nicht string draus macht! Sondern wp funktion dafür nimmt.
		// Say our textarea option must be safe text with the allowed tags for posts
		$input['VIM_images'] = wp_kses_post( $input['VIM_images']);
		return $input;
	 }
	 
	public function VIM_like_MetaBox(){
		 echo '<p>This plugin is developed by <br/><a href="http://www.BMo-Design.de" target="_blank">Benedikt Morschheuser</a>.<br/>Any kind of contribution would be highly appreciated. Thank you!</p>
		 <ul>
		 	<li>If you like it, please...</li>
		 	<li><a href="http://wordpress.org/extend/plugins/vertical-image-menu/" target="_blank">rate it at wordpress.org</a> &diams;</li>
			<li><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=4AWSR2J4DK2FU" target="_blank">donate my work</a> &hearts;</li>
			<li><a href="http://bmo-design.de" target="_blank">set a link to my website</a> &rarr;</li>
			<li>or give me a <g:plusone size="small"  href="http://software.BMo-design.de/wordpress-plugins/wordpress-plugin-vertical-image-menu.html"></g:plusone></li>
			<li>&nbsp;</li>
		</ul>';
	 } 
	 
}

?>