<?php
/**
 * SnS_Theme_Page
 * 
 * Allows WordPress admin users the ability to edit theme CSS
 * and LESS directly in the admin via CodeMirror.
 */
		
class SnS_Theme_Page
{
	/**
	 * Constants
	 */
	const MENU_SLUG = 'sns_theme';
	
	/**
	 * Initializing method.
	 * @static
	 */
	function init() {
		$hook_suffix = add_submenu_page( SnS_Admin::$parent_slug, __( 'Scripts n Styles', 'scripts-n-styles' ), __( 'Settings' ), 'unfiltered_html', self::MENU_SLUG, array( 'SnS_Form', 'page' ) );
		
		add_action( "load-$hook_suffix", array( __CLASS__, 'admin_load' ) );
		add_action( "load-$hook_suffix", array( 'SnS_Admin', 'help' ) );
		add_action( "load-$hook_suffix", array( 'SnS_Form', 'take_action' ), 49 );
		add_action( "admin_print_styles-$hook_suffix", array( __CLASS__, 'admin_enqueue_scripts' ) );
		
		// Make the page into a tab.
		if ( SnS_Admin::MENU_SLUG != SnS_Admin::$parent_slug ) {
			remove_submenu_page( SnS_Admin::$parent_slug, self::MENU_SLUG );
			add_filter( 'parent_file', array( 'SnS_Admin', 'parent_file') );
		}
	}
	
	function admin_enqueue_scripts() {
		$options = get_option( 'SnS_options' );
		$cm_theme = isset( $options[ 'cm_theme' ] ) ? $options[ 'cm_theme' ] : 'default';
		
		wp_enqueue_style( 'sns-options' );
		wp_enqueue_style( 'codemirror-theme' );
		
		wp_enqueue_script(  'sns-theme-page' );
		wp_localize_script( 'sns-theme-page', '_SnS_options', array( 'theme' => $cm_theme ) );
	}
	/**
	 * Settings Page
	 * Adds Admin Menu Item via WordPress' "Administration Menus" API. Also hook actions to register options via WordPress' Settings API.
	 */
	function admin_load() {
		
		register_setting(
			SnS_Admin::OPTION_GROUP,
			'SnS_options' );
		
		add_settings_section(
			'theme',
			__( 'Scripts n Styles Theme Files', 'scripts-n-styles' ),
			array( __CLASS__, 'global_section' ),
			SnS_Admin::MENU_SLUG );
		
		add_settings_field(
			'less',
			__( '<strong>LESS:</strong> ', 'scripts-n-styles' ),
			array( __CLASS__, 'less_fields' ),
			SnS_Admin::MENU_SLUG,
			'theme',
			array( 'label_for' => 'less' ) );
	}
	
	function less_fields() {
		$options = get_option( 'SnS_options' );
		$less =  isset( $options[ 'theme_less' ] ) ? $options[ 'theme_less' ] : '';
		$compiled =  isset( $options[ 'compiled_theme_less' ] ) ? $options[ 'compiled_theme_less' ] : '';
		?>
		<div style="overflow: hidden;">
			<div style="width: 49%; float: left; overflow: hidden; margin-right: 2%;">
				<textarea id="less" name="SnS_options[theme_less]" style="min-width: 250px; width:47%; float: left" class="code less" rows="5" cols="40"><?php echo esc_textarea( $less ) ?></textarea>
			</div>
			<div style="width: 49%; float: left; overflow: hidden;">
				<textarea id="compiled" name="SnS_options[compiled_theme_less]" style="min-width: 250px; width:47%;" class="code css" rows="5" cols="40"><?php echo esc_textarea( $compiled ) ?></textarea>
				<div id="compiled_error" style="display: none" class="error settings-error below-h2"></div>
			</div>
		</div>
		<?php
	}
	
	/**
	 * Settings Page
	 * Outputs Description text for the Global Section.
	 */
	function global_section() {
		?>
		<div style="max-width: 55em;">
			<p><?php _e( 'Code entered here will be included in <em>every page (and post) of your site</em>, including the homepage and archives. The code will appear <strong>before</strong> Scripts and Styles registered individually.', 'scripts-n-styles' )?></p>
		</div>
		<?php
	}
}
?>