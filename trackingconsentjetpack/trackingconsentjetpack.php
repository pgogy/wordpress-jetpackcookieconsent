<?php

/*

Plugin Name: Tracking Consent Jetpack
Description: Prevents Jetpack tracking without first gaining consent
Author: Pgogy
Version: 0.1

*/

class trackingconsentjetpack{

	public function __construct(){
		if(is_admin()){
			add_action('admin_menu', array($this,'options'));
			add_action('admin_init', array($this,'settings_api_init') );
		}
		add_action("wp_ajax_trackingcookiesforjetpackpermission", array($this, "permission"));
		add_action("wp_ajax_nopriv_trackingcookiesforjetpackpermission", array($this, "permission"));
		add_action("wp_ajax_trackingcookiesforjetpackpermission", array($this, "permission"));
		add_action("wp_ajax_nopriv_trackingcookiesforjetpackcss", array($this, "css"));
		add_action("wp_ajax_trackingcookiesforjetpackcss", array($this, "css"));
		add_action("wp_enqueue_scripts", array($this, "scripts"), 10000000000000000000000);
		add_action("wp_footer", array($this, "footer"), 1);

	}

	function permission(){
		setcookie("trackingcookiesforjetpackpermission", "trackingcookiesforjetpackpermission", get_option( 'trackingconsentjetpacksettings_cookiedays', 30) * DAYS_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN );
		stats_footer();
		die();

	}

	function css(){
		
		header("Content-type: text/css");

		if(trim(get_option("trackingconsentjetpacksettings_customcss"))!=""){
			echo get_option("trackingconsentjetpacksettings_customcss");				
		}
		?>


#trackingcookies{
	position:fixed;
	z-index:100000000000000000000000000000000000000000000000;
	width:40%;
	
	<?PHP 

		switch(get_option("trackingconsentjetpacksettings_noticelocationhorizontal")){
			case "left" : echo "left: 10px; "; break;
			case "middle" : echo "left: 30%; "; break;
			case "right" : echo " right: 10px; "; break;
		}
	?>
<?PHP

		switch(get_option("trackingconsentjetpacksettings_noticelocationvertical")){
			case "top" : echo "top: 10px; "; break;
			case "middle" : echo "top: 45%; "; break;
			case "bottom" : echo " bottom: 10px "; break;
		}
	?>
}<?PHP
		die();

	}

	function footer(){
		if(!isset($_COOKIE['trackingcookiesforjetpackpermission'])){
			echo "<div id='trackingcookies'>";
			echo "<p>" . get_option( 'trackingconsentjetpacksettings_text', __("Accept tracking cookies?") ) . "</p>";
			echo "<button id='trackingcookiesforjetpackpermissionaccept'>" . get_option( 'trackingconsentjetpacksettings_buttontextaccept', __("Yes") ) . "</button>";
			echo "<button id='trackingcookiesforjetpackpermissionreject'>" . get_option( 'trackingconsentjetpacksettings_buttontextreject', __("No") ) . "</button>";
			echo "</div>";
			$GLOBALS['wp_filter']['wp_footer']->callbacks[101] = array();
			$GLOBALS['wp_filter']['shutdown']->callbacks[101] = array();
		}
	}

	function scripts(){

		if(!isset($_COOKIE['trackingcookiesforjetpackpermission'])){		

			global $wp_scripts, $wp_styles;

			foreach($wp_scripts->registered as $key => $script){

				if(strpos($script->src, "wp.com")!==FALSE){
					wp_dequeue_script($key);
				}
				if(strpos($script->src, "gravatar.com")!==FALSE){
					wp_dequeue_script($key);
				}
				if($key=="wpgroho"){
					wp_dequeue_script($key);
				}

			}

			foreach($wp_styles->registered as $key => $style){

				if(strpos($style->src, "wp.com")!==FALSE){
					wp_dequeue_script($key);
				}
				
				if(strpos($style->src, "gravatar.com")!==FALSE){
					wp_dequeue_script($key);
				}

			}

		}
	
		wp_register_script("trackingcookiesforjetpackpermission", plugins_url( '/js/jetpackmenuconsent.js', __FILE__ ), array("jquery"));
		wp_localize_script("trackingcookiesforjetpackpermission","trackingcookiesforjetpackpermission", array(
															"ajaxurl" => admin_url("admin-ajax.php"),
															"hidescroll" => (get_option("trackingconsentjetpacksettings_noticescrollhide") == "yes" ) ? "true" : "false", 
														));
		wp_enqueue_script("trackingcookiesforjetpackpermission");	
	
		wp_enqueue_style("trackingcookiesforjetpackpermission_css", admin_url( '/admin-ajax.php?action=trackingcookiesforjetpackcss'));

	}

		
	function settings_api_init() {
		
		add_settings_section(
			'trackingconsentjetpacksettings_section',
			__('Tracking Consent for Jetpack settings'),
			array($this,'trackingconsentjetpacksettings_intro_function'),
			'trackingconsentjetpacksettings'
		);
		
		add_settings_field(
			'trackingconsentjetpacksettings_text',
			__('Text to display on pop up, it can be HTML'),
			array($this,'text'),
			'trackingconsentjetpacksettings',
			'trackingconsentjetpacksettings_section'
		);
		
		add_settings_field(
			'trackingconsentjetpacksettings_buttontextaccept',
			__('Text to display on the accept pop up button'),
			array($this,'buttontextaccept'),
			'trackingconsentjetpacksettings',
			'trackingconsentjetpacksettings_section'
		);

		add_settings_field(
			'trackingconsentjetpacksettings_buttontextreject',
			__('Text to display on the reject pop up button'),
			array($this,'buttontextreject'),
			'trackingconsentjetpacksettings',
			'trackingconsentjetpacksettings_section'
		);

		add_settings_field(
			'trackingconsentjetpacksettings_cookiedays',
			__('Days for cookie to last for'),
			array($this,'cookiedays'),
			'trackingconsentjetpacksettings',
			'trackingconsentjetpacksettings_section'
		);

		add_settings_field(
			'trackingconsentjetpacksettings_customcss',
			__('CSS for the message'),
			array($this,'customcss'),
			'trackingconsentjetpacksettings',
			'trackingconsentjetpacksettings_section'
		);

		add_settings_field(
			'trackingconsentjetpacksettings_noticelocationvertical',
			__('Where will the message display vertically?'),
			array($this,'noticelocationvertical'),
			'trackingconsentjetpacksettings',
			'trackingconsentjetpacksettings_section'
		);

		add_settings_field(
			'trackingconsentjetpacksettings_noticelocationhorizontal',
			__('Where will the message display horizontally?'),
			array($this,'noticelocationhorizontal'),
			'trackingconsentjetpacksettings',
			'trackingconsentjetpacksettings_section'
		);

		add_settings_field(
			'trackingconsentjetpacksettings_noticescrollhide',
			__('Hide the message on scroll?'),
			array($this,'noticehidescroll'),
			'trackingconsentjetpacksettings',
			'trackingconsentjetpacksettings_section'
		);

		register_setting( 'trackingconsentjetpacksettings', 'trackingconsentjetpacksettings_text' );
		register_setting( 'trackingconsentjetpacksettings', 'trackingconsentjetpacksettings_buttontextaccept' );
		register_setting( 'trackingconsentjetpacksettings', 'trackingconsentjetpacksettings_buttontextreject' );
		register_setting( 'trackingconsentjetpacksettings', 'trackingconsentjetpacksettings_cookiedays' );
		register_setting( 'trackingconsentjetpacksettings', 'trackingconsentjetpacksettings_customcss' );
		register_setting( 'trackingconsentjetpacksettings', 'trackingconsentjetpacksettings_noticelocationvertical' );
		register_setting( 'trackingconsentjetpacksettings', 'trackingconsentjetpacksettings_noticelocationhorizontal' );
		register_setting( 'trackingconsentjetpacksettings', 'trackingconsentjetpacksettings_noticescrollhide' );


	}
 
	function trackingconsentjetpacksettings_intro_function() {
		echo '<p>' . __("This page is where you can configure the options re tracking consent with jetpack") . '</p>';
	}
 
	function text() {
		$text = get_option( 'trackingconsentjetpacksettings_text', __("Are you ok to accept tracking cookies?") );
		
		echo "<p>" . __("Pop Up Text") . "</p>";
		echo '<textarea width="100" rows="10" name="trackingconsentjetpacksettings_text" id="trackingconsentjetpacksettings_text" placeholder="' . __("Enter text here to display on the pop up") . '">' . $text . '</textarea>';
	}

	function buttontextaccept() {
		$text = get_option( 'trackingconsentjetpacksettings_buttontextaccept', __("Accept") );
		
		echo "<p>" . __("Pop Up Button Text") . "</p>";
		echo '<textarea width="100" rows="10" name="trackingconsentjetpacksettings_buttontextaccept" id="trackingconsentjetpacksettings_buttontextaccept" placeholder="' . __("Enter text here to display on the accept pop up button") . '">' . $text . '</textarea>';
	}

	function buttontextreject() {
		$text = get_option( 'trackingconsentjetpacksettings_buttontextreject', __("Reject") );
		
		echo "<p>" . __("Pop Up Button Text") . "</p>";
		echo '<textarea width="100" rows="10" name="trackingconsentjetpacksettings_buttontextreject" id="trackingconsentjetpacksettings_buttontextreject" placeholder="' . __("Enter text here to display on the reject pop up button") . '">' . $text . '</textarea>';
	}

	function cookiedays() {
		$days = get_option( 'trackingconsentjetpacksettings_cookiedays', 30);
		
		echo "<p>" . __("Days for the cookie to live for") . "</p>";
		echo '<input type="text" name="trackingconsentjetpacksettings_cookiedays" id="trackingconsentjetpacksettings_cookiedays" value="' . $days . '" />';
	}

	function customcss() {
		$css = get_option( 'trackingconsentjetpacksettings_customcss');
		
		echo "<p>" . __("Days for the cookie to live for") . "</p>";
		echo '<<textarea width="100" rows="10" name="trackingconsentjetpacksettings_customcss" id="trackingconsentjetpacksettings_customcss">' . $css . '</textarea>';
	}

	function noticelocationvertical() {
		$value = get_option('trackingconsentjetpacksettings_noticelocationvertical');
		
		echo "<p>" . __("Where to place the message?") . "</p>";
		echo __("Top") . "<input type='radio' id='trackingconsentjetpacksettings_noticelocationvertical' name='trackingconsentjetpacksettings_noticelocationvertical' value='top' ";
		if($value=="top"){
			echo " checked='checked' ";
		} 	
		
		echo " />";

		echo __("Middle") . "<input type='radio' id='trackingconsentjetpacksettings_noticelocationvertical' name='trackingconsentjetpacksettings_noticelocationvertical' value='middle' ";
		if($value=="middle"){
			echo " checked='checked' ";
		} 	
		
		echo " />";


		echo __("Bottom") . "<input type='radio' id='trackingconsentjetpacksettings_noticelocationvertical' name='trackingconsentjetpacksettings_noticelocationvertical' value='bottom' ";
		if($value=="bottom"){
			echo " checked='checked' ";
		} 	
		
		echo " />";
	}

	function noticelocationhorizontal() {
		$value = get_option('trackingconsentjetpacksettings_noticelocationhorizontal');
		
		echo "<p>" . __("Where to place the message?") . "</p>";
		echo __("Left") . "<input type='radio' id='trackingconsentjetpacksettings_noticelocationhorizontal' name='trackingconsentjetpacksettings_noticelocationhorizontal' value='left' ";
		if($value=="left"){
			echo " checked='checked' ";
		} 	
		
		echo " />";

		echo __("Middle") . "<input type='radio' id='trackingconsentjetpacksettings_noticelocationhorizontal' name='trackingconsentjetpacksettings_noticelocationhorizontal' value='middle' ";
		if($value=="middle"){
			echo " checked='checked' ";
		} 	
		
		echo " />";


		echo __("Right") .  "<input type='radio' id='trackingconsentjetpacksettings_noticelocationhorizontal' name='trackingconsentjetpacksettings_noticelocationhorizontal' value='right' ";
		if($value=="right"){
			echo " checked='checked' ";
		} 	
		
		echo " />";
	}
	
	function noticehidescroll() {

		$value = get_option('trackingconsentjetpacksettings_noticescrollhide');

		echo "<p>" . __("Hide the message on a scroll?") . "</p>";
		echo __("Yes") . "<input type='radio' id='trackingconsentjetpacksettings_noticescrollhide' name='trackingconsentjetpacksettings_noticescrollhide' value='yes' ";
		if($value=="yes"){
			echo " checked='checked' ";
		} 	
		
		echo " />";

		echo __("No") . "<input type='radio' id='trackingconsentjetpacksettings_noticescrollhide' name='trackingconsentjetpacksettings_noticescrollhide' value='no' ";
		if($value=="no"){
			echo " checked='checked' ";
		} 	
		
		echo " />";

	}
	

	function options_page() {
		?><form method="POST" action="options.php">
		<?php 
			settings_fields("trackingconsentjetpacksettings");	
			do_settings_sections("trackingconsentjetpacksettings"); 	//pass slug name of page
			submit_button();
		?>
		</form><?PHP
	}
			
	function options() {
		add_menu_page( "trackingconsentjetpack", __("Tracking Consent for JetPack"), "manage_options", 'trackingconsentjetpacksettings', array($this,"options_page"));
	}

}

$trackingconsentjetpack = new trackingconsentjetpack();