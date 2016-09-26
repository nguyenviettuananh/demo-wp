<?php
defined('ABSPATH') or die;
global $url;

define('MTS_TYPOGRAPHY_DEFAULT_OPT', MTS_THEME_NAME.'_typography_default');
define('MTS_TYPOGRAPHY_COLLECTIONS_OPT', MTS_THEME_NAME.'_typography_collections');
$mts_typography_generate_previews = ( extension_loaded('gd') && function_exists('gd_info') && function_exists('imagettftext') );
define('MTS_TYPOGRAPHY_GENERATE_PREVIEWS', $mts_typography_generate_previews);

require_once('google-typography/google-typography.php');
require_once('php-po/php-po.php'); // for parsing default.po into array

if ( ! class_exists('NHP_Options') ){
	
	if(!defined('NHP_OPTIONS_DIR')){
		define('NHP_OPTIONS_DIR', trailingslashit(dirname(__FILE__)));
	}
	
	if(!defined('NHP_OPTIONS_URL')){
		if( file_exists(STYLESHEETPATH.'/options/options.php') ){
        define('NHP_OPTIONS_URL', trailingslashit(get_stylesheet_directory_uri()).'options/');
    }elseif( file_exists(TEMPLATEPATH.'/options/options.php') ){
        define('NHP_OPTIONS_URL', trailingslashit(get_template_directory_uri()).'options/');
	}
}

class NHP_Options{
	
	public $framework_url = 'http://leemason.github.com/NHP-Theme-Options-Framework/';
	public $framework_version = '1.0.5';
	public $url = NHP_OPTIONS_URL;
	public $dir = NHP_OPTIONS_DIR;
	public $page = '';
	public $args = array();
	public $sections = array();
	public $extra_tabs = array();
	public $errors = array();
	public $warnings = array();
	public $options = array();
	
	

	/**
	 * Class Constructor. Defines the args for the theme options class
	 *
	 * @since NHP_Options 1.0
	 *
	 * @param $array $args Arguments. Class constructor arguments.
	*/
	function __construct($sections = array(), $args = array(), $extra_tabs = array()){
		
		$defaults = array();
		
		$defaults['opt_name'] = '';//must be defined by theme/plugin
		
		$defaults['menu_icon'] = NHP_OPTIONS_URL.'/img/menu_icon.png';
		$defaults['menu_title'] = __('Options', 'mythemeshop');
		$defaults['page_icon'] = 'icon-themes';
		$defaults['page_title'] = __('Options', 'mythemeshop');
		$defaults['page_slug'] = '_options';
		$defaults['page_cap'] = 'manage_options';
		$defaults['page_type'] = 'menu';
		$defaults['page_parent'] = '';
		$defaults['page_position'] = 100;
		
		$defaults['show_import_export'] = true;
        $defaults['show_typography'] = true;
        $defaults['show_translate'] = true;
        $defaults['show_child_theme_opts'] = true;
		$defaults['dev_mode'] = true;
		$defaults['stylesheet_override'] = false;
		
		$defaults['footer_credit'] = __('', 'mythemeshop');
		
		$defaults['help_tabs'] = array();
		$defaults['help_sidebar'] = __('', 'mythemeshop');
		
		//get args
		$this->args = wp_parse_args($args, $defaults);
		$this->args = apply_filters('nhp-opts-args', $this->args);
		$this->args = apply_filters('nhp-opts-args-'.$this->args['opt_name'], $this->args);
		
		//get sections
		$this->sections = apply_filters('nhp-opts-sections', $sections);
		$this->sections = apply_filters('nhp-opts-sections-'.$this->args['opt_name'], $this->sections);
		
		//get extra tabs
		$this->extra_tabs = apply_filters('nhp-opts-extra-tabs', $extra_tabs);
		$this->extra_tabs = apply_filters('nhp-opts-extra-tabs-'.$this->args['opt_name'], $this->extra_tabs);
		
		//set option with defaults
		add_action('init', array(&$this, '_set_default_options'));
		
		//options page
		add_action('admin_menu', array(&$this, '_options_page'));
		
		//register setting
		add_action('admin_init', array(&$this, '_register_setting'));
		
		//add the js for the error handling before the form
		add_action('nhp-opts-page-before-form', array(&$this, '_errors_js'), 1);
		
		//add the js for the warning handling before the form
		add_action('nhp-opts-page-before-form', array(&$this, '_warnings_js'), 2);
		
		//hook into the wp feeds for downloading the exported settings
		add_action('do_feed_nhpopts', array(&$this, '_download_options'), 1, 1);
        
        //ajax for translation panel form
        add_action('wp_ajax_mts_translation_panel',array(&$this,'ajax_mts_translation_panel'));
        add_action('wp_ajax_mts_save_translation',array(&$this,'ajax_mts_save_translation'));

        //child theme creation
        add_action('wp_ajax_mts_child_theme', array(&$this,'mts_child_theme'));
        add_action('wp_ajax_mts_list_child_themes', array(&$this,'mts_list_child_themes'));
		
		//get the options for use later on
		$this->options = get_option($this->args['opt_name']);
		
	}//function
	
	
	/**
	 * ->get(); This is used to return and option value from the options array
	 *
	 * @since NHP_Options 1.0.1
	 *
	 * @param $array $args Arguments. Class constructor arguments.
	*/
	function get($opt_name, $default = null){
		return (!empty($this->options[$opt_name])) ? $this->options[$opt_name] : $default;
	}//function
	
	/**
	 * ->set(); This is used to set an arbitrary option in the options array
	 *
	 * @since NHP_Options 1.0.1
	 * 
	 * @param string $opt_name the name of the option being added
	 * @param mixed $value the value of the option being added
	 */
	function set($opt_name, $value) {
		$this->options[$opt_name] = $value;
		update_option($this->args['opt_name'], $this->options);
	}
	
	/**
	 * ->show(); This is used to echo and option value from the options array
	 *
	 * @since NHP_Options 1.0.1
	 *
	 * @param $array $args Arguments. Class constructor arguments.
	*/
	function show($opt_name){
		$option = $this->get($opt_name);
		if(!is_array($option)){
			echo $option;
		}
	}//function
	
	
	
	/**
	 * Get default options into an array suitable for the settings API
	 *
	 * @since NHP_Options 1.0
	 *
	*/
	function _default_values(){
		
		$defaults = array();
		
		foreach($this->sections as $k => $section){
			
			if(isset($section['fields'])){
		
				foreach($section['fields'] as $fieldk => $field){
					
					if(!isset($field['std'])){$field['std'] = '';}
						
						$defaults[$field['id']] = $field['std'];
     
				}//foreach
			
			}//if
			
		}//foreach
		
		//fix for notice on first page load
		$defaults['last_tab'] = 0;

		return $defaults;
		
	}
	
	
	
	/**
	 * Set default options on admin_init if option doesnt exist (theme activation hook caused problems, so admin_init it is)
	 *
	 * @since NHP_Options 1.0
	 *
	*/
	function _set_default_options(){
		if(!get_option($this->args['opt_name'])){
			add_option($this->args['opt_name'], $this->_default_values());
		}
		$this->options = get_option($this->args['opt_name']);
	}//function
	
	
	/**
	 * Class Theme Options Page Function, creates main options page.
	 *
	 * @since NHP_Options 1.0
	*/
	function _options_page(){
		$this->page = add_theme_page(
						$this->args['page_title'], 
						$this->args['menu_title'], 
						$this->args['page_cap'], 
						$this->args['page_slug'], 
						array(&$this, '_options_page_html'),
						$this->args['menu_icon'],
						$this->args['page_position']
					);

		add_action('admin_print_styles-'.$this->page, array(&$this, '_enqueue'));
		add_action('load-'.$this->page, array(&$this, '_load_page'));
	}//function	
	

	/**
	 * enqueue styles/js for theme page
	 *
	 * @since NHP_Options 1.0
	*/
	function _enqueue(){
		
		wp_register_style(
				'nhp-opts-css', 
				$this->url.'css/options.css',
				array('farbtastic'),
				time(),
				'all'
			);
			
		wp_register_style(
			'nhp-opts-jquery-ui-css',
			apply_filters('nhp-opts-ui-theme', $this->url.'css/aristo.css'),
			'',
			time(),
			'all'
		);
			
		wp_register_style(
			'font-awesome', 
			get_template_directory_uri().'/css/font-awesome.min.css',
			'',
			time(),
			'all'
		);

		if(false === $this->args['stylesheet_override']){
			wp_enqueue_style('nhp-opts-css');
			wp_enqueue_style('font-awesome');
		}
		
		wp_register_script(
			'nhp-history-js', 
			$this->url.'js/history.js', 
			array('jquery'),
			time(),
			true
		);
		
		wp_register_script(
			'nhp-opts-js', 
			$this->url.'js/options.js', 
			array('jquery', 'nhp-history-js'),
			time(),
			true
		);

		// pass $this->args['opt_name'] to js...
        wp_localize_script( 'nhp-opts-js', 'nhpopts', array(
            'opt_name' => $this->args['opt_name'],
            'reset_confirm' => __('Are you sure you want to reset ALL options to default (except theme translation)?', 'mythemeshop'),
            'reset_translations_confirm' => __('Are you sure you want to reset translations? You will lose all translations.', 'mythemeshop'),
            'import_confirm' => __('Are you sure you want to import options? All current options will be lost.', 'mythemeshop'),
            'leave_page_confirm' => __('Settings have changed, you should save them! Are you sure you want to leave this page?', 'mythemeshop'),
            'child_theme_name_empty' => __('Please enter desired child theme name.', 'mythemeshop')
        ) );
		wp_enqueue_script('nhp-opts-js');
        
		do_action('nhp-opts-enqueue');
		
		do_action('nhp-opts-enqueue-'.$this->args['opt_name']);
		
		
		foreach($this->sections as $k => $section){
			
			if(isset($section['fields'])){
				
				foreach($section['fields'] as $fieldk => $field){
					
					if(isset($field['type'])){
					
						$field_class = 'NHP_Options_'.$field['type'];
						
						if(!class_exists($field_class)){
							require_once($this->dir.'fields/'.$field['type'].'/field_'.$field['type'].'.php');
						}//if
				
						if(class_exists($field_class) && method_exists($field_class, 'enqueue')){
							$enqueue = new $field_class('','',$this);
							$enqueue->enqueue();
						}//if
						
					}//if type
					
				}//foreach
			
			}//if fields
			
		}//foreach
			
		
	}//function
	
	/**
	 * Download the options file, or display it
	 *
	 * @since NHP_Options 1.0.1
	*/
	function _download_options(){
		if(!isset($_GET['secret']) || $_GET['secret'] != md5(AUTH_KEY.SECURE_AUTH_KEY)){wp_die('Invalid Secret for options use');exit;}
		if(!isset($_GET['option'])){wp_die('No Option Defined');exit;}
		$backup_options = get_option($_GET['option']);
		$backup_options['nhp-opts-backup'] = '1';
		$content = '###'.serialize($backup_options).'###';
		
		
		if(isset($_GET['action']) && $_GET['action'] == 'download_options'){
			header('Content-Description: File Transfer');
			header('Content-type: application/txt');
			header('Content-Disposition: attachment; filename="'.$_GET['option'].'_options_'.date('d-m-Y').'.txt"');
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			echo $content;
			exit;
		}else{
			echo $content;
			exit;
		}
	}
	
	
	
	
	/**
	 * show page help
	 *
	 * @since NHP_Options 1.0
	*/
	function _load_page(){
		
		//do admin head action for this page
		add_action('admin_head', array(&$this, 'admin_head'));
		
		//do admin footer text hook
		add_filter('admin_footer_text', array(&$this, 'admin_footer_text'));
		
		$screen = get_current_screen();
		
		if(is_array($this->args['help_tabs'])){
			foreach($this->args['help_tabs'] as $tab){
				$screen->add_help_tab($tab);
			}//foreach
		}//if
		
		if($this->args['help_sidebar'] != ''){
			$screen->set_help_sidebar($this->args['help_sidebar']);
		}//if
		
		do_action('nhp-opts-load-page', $screen);
		
		do_action('nhp-opts-load-page-'.$this->args['opt_name'], $screen);
		
	}//function
	
	
	/**
	 * do action nhp-opts-admin-head for theme options page
	 *
	 * @since NHP_Options 1.0
	*/
	function admin_head(){
		
		do_action('nhp-opts-admin-head', $this);
		
		do_action('nhp-opts-admin-head-'.$this->args['opt_name'], $this);
		
	}
	
	
	function admin_footer_text($footer_text){
		return $this->args['footer_credit'];
	}//function
	
	
	
	
	/**
	 * Register Option for use
	 *
	 * @since NHP_Options 1.0
	*/
	function _register_setting(){
		
		register_setting($this->args['opt_name'].'_group', $this->args['opt_name'], array(&$this,'_validate_options'));
		
		foreach($this->sections as $k => $section){
			
			add_settings_section($k.'_section', $section['title'], array(&$this, '_section_desc'), $k.'_section_group');
			
			if(isset($section['fields'])){
			
				foreach($section['fields'] as $fieldk => $field){
					
					if(isset($field['title'])){
					
						$th = (isset($field['sub_desc'])) ? '<span class="field_title">'.$field['title'].'</span><span class="description">'.$field['sub_desc'].'</span>' : '<span class="field_title">'.$field['title'].'</span>';
					}else{
						$th = '';
					}
					
					add_settings_field($fieldk.'_field', $th, array(&$this,'_field_input'), $k.'_section_group', $k.'_section', $field); // checkbox
					
				}//foreach
			
			}//if(isset($section['fields'])){
			
		}//foreach
		
		do_action('nhp-opts-register-settings');
		
		do_action('nhp-opts-register-settings-'.$this->args['opt_name']);
		
        
		
	}//function
	
	
	
	/**
	 * Validate the Options options before insertion
	 *
	 * @since NHP_Options 1.0
	*/
	function _validate_options($plugin_options){
		
        // Google Fonts
        // stored in a separate option for backwards compatibility
        if (!empty($plugin_options['google_typography_collections'])) {
        	// make sure not to save duplicates
        	$collections = array();
        	foreach ($plugin_options['google_typography_collections'] as $coll) {
        		$selector = (!empty($coll['css_selectors']) ? $coll['css_selectors'] : '0');
        		if (!isset($collections[$selector])){
        			$collections[$selector] = $coll;
        		}
        	}
        	$collections = array_values($collections);
            update_option(MTS_TYPOGRAPHY_COLLECTIONS_OPT, $collections);
        }
        unset($plugin_options['google_typography_collections']);
        
        // if translation panel enabled
        // save passed translations
        
        // if (!empty($plugin_options['translations']) && $plugin_options['translate'] == '1') {
        //     update_option('mts_translations_'.MTS_THEME_NAME, $plugin_options['translations']);
        // }
        // unset($plugin_options['translations']);
        
		set_transient('nhp-opts-saved', '1', 1000 );
		
		if(!empty($plugin_options['import'])){
			
			if($plugin_options['import_code'] != ''){
				$import = $plugin_options['import_code'];
			}elseif($plugin_options['import_link'] != ''){
				$import = wp_remote_retrieve_body( wp_remote_get($plugin_options['import_link']) );
			}
			
			$imported_options = unserialize(trim($import,'# '));
			if(is_array($imported_options) && isset($imported_options['nhp-opts-backup']) && $imported_options['nhp-opts-backup'] == '1'){
				$imported_options['imported'] = 1;
                // Fonts
                // If no typography array provided, we don't touch the current setting
                if (!empty($imported_options['google_typography_collections'])) {
                    update_option(MTS_TYPOGRAPHY_COLLECTIONS_OPT, $imported_options['google_typography_collections']);
                }
                unset($imported_options['google_typography_collections']);
                
				// same for Translations
                if (!empty($imported_options['translations'])) {
                    update_option('mts_translations', $imported_options['translations']);
                }
                return $imported_options;
			}
			
			
		}
		
        // reset translation
        if(!empty($plugin_options['reset_translations'])) {
            // don't reset settings - pass saved data
            $saved_options = get_option($this->args['opt_name']);
            delete_option('mts_translations_'.$this->args['opt_name']);
            $saved_options['last_tab'] = 'translation_default';
            return $saved_options;
        }
        
		// reset settings
		if(!empty($plugin_options['defaults'])){
			$plugin_options = $this->_default_values();
            
            // Fonts
            delete_option(MTS_TYPOGRAPHY_DEFAULT_OPT);
            delete_option(MTS_TYPOGRAPHY_COLLECTIONS_OPT);
            
            // don't reset mts_translations
            
			return $plugin_options;
		}//if set defaults

		
		//validate fields (if needed)
		$plugin_options = $this->_validate_values($plugin_options, $this->options);
		
		if($this->errors){
			set_transient('nhp-opts-errors', $this->errors, 1000 );		
		}//if errors
		
		if($this->warnings){
			set_transient('nhp-opts-warnings', $this->warnings, 1000 );		
		}//if errors
		
		do_action('nhp-opts-options-validate', $plugin_options, $this->options);
		
		do_action('nhp-opts-options-validate-'.$this->args['opt_name'], $plugin_options, $this->options);
		
		// no need to store these
		unset($plugin_options['defaults']);
		unset($plugin_options['import']);
		unset($plugin_options['import_code']);
		unset($plugin_options['import_link']);
        
		return $plugin_options;	
	
	}//function
	
	
	
	
	/**
	 * Validate values from options form (used in settings api validate function)
	 * calls the custom validation class for the field so authors can override with custom classes
	 *
	 * @since NHP_Options 1.0
	*/
	function _validate_values($plugin_options, $options){
		foreach($this->sections as $k => $section){
			
			if(isset($section['fields'])){
			
				foreach($section['fields'] as $fieldk => $field){
					$field['section_id'] = $k;
					
					if(isset($field['type']) && $field['type'] == 'multi_text'){continue;}//we cant validate this yet
					
					if(!isset($plugin_options[$field['id']]) || $plugin_options[$field['id']] == ''){
						continue;
					}
					
					//force validate of custom filed types
					
					if(isset($field['type']) && !isset($field['validate'])){
						if($field['type'] == 'color' || $field['type'] == 'color_gradient'){
							$field['validate'] = 'color';
						}elseif($field['type'] == 'date'){
							$field['validate'] = 'date';
						}
					}//if
	
					if(isset($field['validate'])){
						$validate = 'NHP_Validation_'.$field['validate'];
						
						if(!class_exists($validate)){
							require_once($this->dir.'validation/'.$field['validate'].'/validation_'.$field['validate'].'.php');
						}//if
						
						if(class_exists($validate)){
							$validation = new $validate($field, $plugin_options[$field['id']], $options[$field['id']]);
							$plugin_options[$field['id']] = $validation->value;
							if(isset($validation->error)){
								$this->errors[] = $validation->error;
							}
							if(isset($validation->warning)){
								$this->warnings[] = $validation->warning;
							}
							continue;
						}//if
					}//if
					
					
					if(isset($field['validate_callback']) && function_exists($field['validate_callback'])){
						
						$callbackvalues = call_user_func($field['validate_callback'], $field, $plugin_options[$field['id']], $options[$field['id']]);
						$plugin_options[$field['id']] = $callbackvalues['value'];
						if(isset($callbackvalues['error'])){
							$this->errors[] = $callbackvalues['error'];
						}//if
						if(isset($callbackvalues['warning'])){
							$this->warnings[] = $callbackvalues['warning'];
						}//if
						
					}//if
					
					
				}//foreach
			
			}//if(isset($section['fields'])){
			
		}//foreach
		return $plugin_options;
	}//function
	
	
	
	
	
	
	
	
	/**
	 * HTML OUTPUT.
	 *
	 * @since NHP_Options 1.0
	*/
	function _options_page_html(){
		
		echo '<div class="wrap">';
			echo (isset($this->args['intro_text']))?$this->args['intro_text']:'';
			
			do_action('nhp-opts-page-before-form');
			
			do_action('nhp-opts-page-before-form-'.$this->args['opt_name']);

			echo '<form method="post" action="options.php" enctype="multipart/form-data" id="nhp-opts-form-wrapper">';
				settings_fields($this->args['opt_name'].'_group');
				echo '<input type="hidden" id="last_tab" name="'.$this->args['opt_name'].'[last_tab]" value="'.$this->options['last_tab'].'" />';
				
				echo '<div id="nhp-opts-header">';
				
				echo '<a href="http://mythemeshop.com" id="optionpanellogo" class="logo" target="_blank"><img src="'.$this->url.'img/optionpanellogo.png" /></a>';
echo '<span class="headtext">Welcome to your theme\'s mission control center.</span>';

					echo '<a href="https://community.mythemeshop.com/" class="docsupport" target="_blank"><i class="fa fa-medkit"></i> Support</a>';
					echo '<div class="clear"></div><!--clearfix-->';
				echo '</div>';
				
					if(isset($_GET['settings-updated']) && $_GET['settings-updated'] == 'true' && get_transient('nhp-opts-saved') == '1'){
						if(isset($this->options['imported']) && $this->options['imported'] == 1){
							echo '<div id="nhp-opts-imported">'.__('<strong>Settings Imported!</strong>', 'mythemeshop').'</div>';
						}else{
							echo '<div id="nhp-opts-save">'.__('<strong>Settings Saved!</strong>', 'mythemeshop').'</div>';
						}
						delete_transient('nhp-opts-saved');
					}
					echo '<div id="nhp-opts-save-warn">'.__('<strong>Settings have changed, you should save them!</strong>', 'mythemeshop').'</div>';
					echo '<div id="nhp-opts-field-errors">'.__('<strong><span></span> error(s) were found!</strong>', 'mythemeshop').'</div>';
					
					echo '<div id="nhp-opts-field-warnings">'.__('<strong><span></span> warning(s) were found!</strong>', 'mythemeshop').'</div>';
				
				echo '<div class="clear"></div><!--clearfix-->';
				
				echo '<div id="nhp-opts-sidebar">';
					echo '<ul id="nhp-opts-group-menu">';
						/* foreach($this->sections as $k => $section){
                            // Accordion: tab 3-14 are homepage items
                            if ($k == 3) {
                                echo '<li id="accordion_section_group_li" class="nhp-opts-group-tab-link-li"><a href="javascript:void(0);" id="accordion_section_group_li_a" class="nhp-opts-group-tab-link-a" data-rel="3" title="Homepage"><i class="fa fa-home"></i> <span class="section_title">Homepage</span></a></li>';
                                echo '<div id="nhp-opts-homepage-accordion">';
                            }
                            if ($k == 10) {
                                echo '<li id="accordion_section_group_li" class="nhp-opts-group-tab-link-li"><a href="javascript:void(0);" id="accordion_section_group_li_a_2" class="nhp-opts-group-tab-link-a" data-rel="3" title="Blog Settings"><i class="fa fa-file-text"></i> <span class="section_title">Blog Settings</span></a></li>';
                                echo '<div id="nhp-opts-blog-accordion">';
                            }
							$icon = (!isset($section['icon']))?'<i class="fa fa-cogs"></i> ':'<i class="fa '.$section['icon'].'"></i> ';
							echo '<li id="'.$k.'_section_group_li" class="nhp-opts-group-tab-link-li">';
								echo '<a href="javascript:void(0);" id="'.$k.'_section_group_li_a" class="nhp-opts-group-tab-link-a" data-rel="'.$k.'" title="'.$section['title'].'">'.$icon.'<span class="section_title">'.$section['title'].'</span></a>';
							echo '</li>';
                            if ($k == 9 || $k == 13) {
                                echo '</div>';
                            }
						} */

						foreach($this->sections as $k => $section){
							$icon = (!isset($section['icon']))?'<i class="fa fa-cogs"></i> ':'<i class="'.$section['icon'].'"></i> ';
							echo '<li id="'.$k.'_section_group_li" class="nhp-opts-group-tab-link-li">';
								echo '<a href="javascript:void(0);" id="'.$k.'_section_group_li_a" class="nhp-opts-group-tab-link-a" data-rel="'.$k.'" title="'.$section['title'].'">'.$icon.'<span class="section_title">'.$section['title'].'</span></a>';
							echo '</li>';
						}
						
						
						
						do_action('nhp-opts-after-section-menu-items', $this);
						
						do_action('nhp-opts-after-section-menu-items-'.$this->args['opt_name'], $this);
						
                        // Typography link
						if(true === $this->args['show_typography']){
							echo '<li id="typography_default_section_group_li" class="nhp-opts-group-tab-link-li">';
									echo '<a href="javascript:void(0);" id="typography_default_section_group_li_a" class="nhp-opts-group-tab-link-a" data-rel="typography_default"><i class="fa fa-text-width"></i> '.'<span class="section_title">'.__('Typography', 'mythemeshop').'</span></a>';
							echo '</li>';
							
						}//if
                        
                        // Translation link
                        if(true === $this->args['show_translate']){
							echo '<li id="translation_default_section_group_li" class="nhp-opts-group-tab-link-li">';
									echo '<a href="javascript:void(0);" id="translation_default_section_group_li_a" class="nhp-opts-group-tab-link-a" data-rel="translation_default"><i class="fa fa-flag-o"></i> '.'<span class="section_title">'.__('Translate', 'mythemeshop').'</span></a>';
							echo '</li>';
							
						}//if
                        
                        // Import/export link
						if(true === $this->args['show_import_export']){
							echo '<li id="import_export_default_section_group_li" class="nhp-opts-group-tab-link-li">';
									echo '<a href="javascript:void(0);" id="import_export_default_section_group_li_a" class="nhp-opts-group-tab-link-a" data-rel="import_export_default"><i class="fa fa-sign-in"></i> '.'<span class="section_title">'.__('Import / Export', 'mythemeshop').'</span></a>';
							echo '</li>';
							
						}//if
						
						foreach($this->extra_tabs as $k => $tab){
							$icon = (!isset($tab['icon']))?'<i class="fa fa-cogs" /> ':'<i class="'.$tab['icon'].'" /> ';
							echo '<li id="'.$k.'_section_group_li" class="nhp-opts-group-tab-link-li">';
								echo '<a href="javascript:void(0);" id="'.$k.'_section_group_li_a" class="nhp-opts-group-tab-link-a custom-tab" data-rel="'.$k.'">'.$icon.$tab['title'].'</a>';
							echo '</li>';
						}

						
						if(true === $this->args['dev_mode']){
							echo '<li id="dev_mode_default_section_group_li" class="nhp-opts-group-tab-link-li">';
									echo '<a href="javascript:void(0);" id="dev_mode_default_section_group_li_a" class="nhp-opts-group-tab-link-a custom-tab" data-rel="dev_mode_default"><img src="'.$this->url.'img/glyphicons/glyphicons_195_circle_info.png" /> '.__('Dev Mode Info', 'mythemeshop').'</a>';
							echo '</li>';
						}//if
						
					echo '</ul>';
				echo '</div>';
				
				echo '<div id="nhp-opts-main">';
				
					foreach($this->sections as $k => $section){
						echo '<div id="'.$k.'_section_group'.'" class="nhp-opts-group-tab">';
							do_settings_sections($k.'_section_group');
						echo '</div>';
					}					
					
					
					if(true === $this->args['show_import_export']){
						echo '<div id="import_export_default_section_group'.'" class="nhp-opts-group-tab">';
							echo '<h3>'.__('Import / Export Options', 'mythemeshop').'</h3>';
                            
							// presets
                            if (!empty($this->args['presets'])) {
                                echo '<h4>'.__('Preset Options', 'mythemeshop').'</h4>';
                                
                                echo '<div id="presets" class="nhp-opts-field-wrapper">';
                                foreach ($this->args['presets'] as $preset) {
                                    echo '<div class="preset">';
                                        echo '<h5>'.$preset['title'].'</h5>';
                                        echo '<div class="preset-thumb-wrap">';
                                            echo '<img src="'.$preset['thumbnail'].'" />';
                                        echo '</div>';
                                        
                                        if ( ! empty( $preset['demo'] ) )
                                            echo '<a href="'.$preset['demo'].'" target="_blank" class="button button-secondary demo-button">'.__('Demo', 'mythemeshop').'</a>';
                                        
                                        echo '<a href="#" class="button button-primary activate-button">'.__('Import', 'mythemeshop').'</a>';
                                        echo '<input type="hidden" class="preset-data" value="'.esc_attr($preset['data']).'" />';
                                    echo '</div>';
                                }
                                echo '</div>';
                                echo '<div id="import_divide"></div>';
                            }
                            
						
                            echo '<h4>'.__('Import Options', 'mythemeshop').'</h4>';
							
							echo '<p><a href="#" id="nhp-opts-import-code-button" class="button-secondary">Import Code</a></p>';
							
							echo '<div id="nhp-opts-import-code-wrapper">';
							
								echo '<div class="nhp-opts-section-desc">';
				
									echo '<p class="description" id="import-code-description">'.apply_filters('nhp-opts-import-file-description',__('Insert your backup code below and hit Import to restore your site options from a backup.', 'mythemeshop')).'</p>';
								
								echo '</div>';
								
                                echo '<div class="nhp-opts-field-wrapper">';
                                
								    echo '<textarea id="import-code-value" name="'.$this->args['opt_name'].'[import_code]" class="large-text" rows="8"></textarea><br />';
                                    
                                    echo '<input type="submit" id="nhp-opts-import" name="'.$this->args['opt_name'].'[import]" class="button-primary" value="'.__('Import', 'mythemeshop').'">';
                                    
                                echo '</div>';
                                
							echo '</div>';
							
							echo '<div id="import_divide"></div>';
							
							echo '<h4>'.__('Export Options', 'mythemeshop').'</h4>';
							echo '<p><a href="#" id="nhp-opts-export-code-copy" class="button-secondary">'.__('Show Export Code', 'mythemeshop').'</a></p>';
							echo '<div class="nhp-opts-section-desc">';
								//echo '<p class="description">'.apply_filters('nhp-opts-backup-description', __('Here, you can export your current theme options.  Keep this safe, as you can use it for backup in case of an emergency. You can also use this to restore settings on this site, or on any other site using this theme.  You can also copy the link to your settings, and duplicate it to another site, which is useful if you have a network of blogs that all need the same settings.', 'mythemeshop')).'</p>';
							echo '</div>';
							
								$backup_options = $this->options;
                                // google typography
                                $typography = get_option(MTS_TYPOGRAPHY_COLLECTIONS_OPT);
                                if (!empty($typography)) {
                                    $backup_options['google_typography_collections'] = $typography;
                                }
                                
                                $mts_translations = get_option('mts_translations_'.MTS_THEME_NAME);
                                if (!empty($this->options['translate']) && !empty($mts_translations)) {
                                    $backup_options['translations'] = $mts_translations;
                                }
                                
								$backup_options['nhp-opts-backup'] = '1';
                                
								$encoded_options = '###'.serialize($backup_options).'###';
								echo '<div class="nhp-opts-field-wrapper">';
                                    echo '<textarea class="large-text" id="nhp-opts-export-code" rows="8">';print_r($encoded_options);echo '</textarea>';
								echo '</div>';
                                echo '<input type="text" class="large-text" id="nhp-opts-export-link-value" value="'.add_query_arg(array('feed' => 'nhpopts', 'secret' => md5(AUTH_KEY.SECURE_AUTH_KEY), 'option' => $this->args['opt_name']), site_url()).'" />';

							echo '<div id="import_divide"></div>';
							
							// Child Theme
							if( true === $this->args['show_child_theme_opts'] && !is_child_theme() ) {

								echo '<h4>'.__('Child Theme', 'mythemeshop').'</h4>';
								echo '<div class="nhp-opts-section-desc">';
									echo '<p class="description">'. __('To create child theme for current theme, enter desired name and hit "Create Child Theme" button', 'mythemeshop').'</p>';
								echo '</div>';

								echo '<div class="nhp-opts-field-wrapper">';
									//echo '<label for="nhp-opts-child-name">'.__( 'Child theme name', 'mythemeshop' ).'</label><br />';
									echo '<input type="text" id="nhp-opts-child-name" name="nhp-opts-child-name" value="" />';
									echo '<input type="submit" id="nhp-opts-child-button" name="nhp-opts-child-button" class="button-primary" value="'.__('Create Child Theme', 'mythemeshop').'">';
								echo '</div>';

								echo '<div id="child-theme-list-wrap">';
									$this->mts_list_child_themes();
								echo '</div>';
							}

							
						echo '</div>';
					}
					
                    if(true === $this->args['show_typography']){
						echo '<div id="typography_default_section_group'.'" class="nhp-opts-group-tab">';
							//echo '<h3>'.__('Theme Typography', 'mythemeshop').'</h3>';
							
							$typography = new mtsGoogleTypography();
                            $typography->options_ui();
						echo '</div>';
					}
                    
					if(true === $this->args['show_translate']){
						echo '<div id="translation_default_section_group'.'" class="nhp-opts-group-tab">';
							echo '<h3>'.__('Theme Translation Panel', 'mythemeshop').'</h3>';
                            echo '<div class="nhp-opts-section-desc">';
								echo '<p class="description">'. __('The theme translation panel provides an easy way to translate text appearing on your site, without the hassle of handling .po .mo files. Translations are saved instantly after typing in.', 'mythemeshop').'</p>';
                                echo '<p id="nhp-opts-reset-translations-action"><input type="submit" id="nhp-opts-reset-translations" name="'.$this->args['opt_name'].'[reset_translations]" class="button-primary" value="'.__('Reset translations', 'mythemeshop').'"></p>';
                            echo '</div>';
							
                            // here comes the translation panel
                            $translate_enabled = false;
                            if (!empty($this->options['translate'])) {
                                $translate_enabled = true;
                            }
                            echo '<div class="nhp-opts-field-wrapper">';
                                echo '<p><label for="nhp-opts-translate"><input type="checkbox" name="'.$this->args['opt_name'].'[translate]" id="nhp-opts-translate" value="1" '.checked($translate_enabled, true, false).' />'.__('Enable translation panel', 'mythemeshop').'</label></p>';
                            echo '</div>';
                            
                            echo '<div class="nhp-opts-field-wrapper" id="translate_search_wrapper">';
                                echo '<p><input type="text" val="" id="translate_search" placeholder="'.__('Search Translations', 'mythemeshop').'" />';
                            echo '</div>';
                            
                            echo '<div class="nhp-opts-field-wrapper translate-strings">';
                                // ajaxed content
                            echo '</div>';
						echo '</div>';
					}
                    
					foreach($this->extra_tabs as $k => $tab){
						echo '<div id="'.$k.'_section_group'.'" class="nhp-opts-group-tab">';
						echo '<h3>'.$tab['title'].'</h3>';
						echo $tab['content'];
						echo '</div>';
					}

					
					
					if(true === $this->args['dev_mode']){
						echo '<div id="dev_mode_default_section_group'.'" class="nhp-opts-group-tab">';
							echo '<h3>'.__('Dev Mode Info', 'mythemeshop').'</h3>';
							echo '<div class="nhp-opts-section-desc">';
							echo '<textarea class="large-text" rows="24">'.print_r($this, true).'</textarea>';
							echo '</div>';
						echo '</div>';
					}
					
					
					do_action('nhp-opts-after-section-items', $this);
					
					do_action('nhp-opts-after-section-items-'.$this->args['opt_name'], $this);
				
					echo '<div class="clear"></div><!--clearfix-->';
				echo '</div>';
				echo '<div class="clear"></div><!--clearfix-->';
				
				echo '<div id="nhp-opts-footer">';
				
					if(isset($this->args['share_icons'])){
						echo '<div id="nhp-opts-share">';
						foreach($this->args['share_icons'] as $link){
							echo '<a href="'.$link['link'].'" title="'.$link['title'].'" target="_blank"><i class="'.$link['img'].'"></i></a>';
						}
						echo '</div>';
					}
					
					
					echo '<input type="submit" name="'.$this->args['opt_name'].'[defaults]" value="'.__('Reset to Defaults', 'mythemeshop').'" class="button-secondary" />';
					echo '<input type="submit" name="save" id="savechanges" value="'.__('Save Changes', 'mythemeshop').'" class="button-primary" disabled="disabled" />';
					echo '<div class="clear"></div><!--clearfix-->';
				echo '</div>';
                
                
			
			echo '</form>';
            
            // Floating buttons
            echo '<div id="nhp-opts-bottom"></div>';
			
			do_action('nhp-opts-page-after-form');
			
			do_action('nhp-opts-page-after-form-'.$this->args['opt_name']);
			
			echo '<div class="clear"></div><!--clearfix-->';	
		echo '</div><!--wrap-->';

	}//function
	
	
	
	/**
	 * JS to display the errors on the page
	 *
	 * @since NHP_Options 1.0
	*/	
	function _errors_js(){
		
		if(isset($_GET['settings-updated']) && $_GET['settings-updated'] == 'true' && get_transient('nhp-opts-errors')){
				$errors = get_transient('nhp-opts-errors');
				$section_errors = array();
				foreach($errors as $error){
					$section_errors[$error['section_id']] = (isset($section_errors[$error['section_id']]))?$section_errors[$error['section_id']]:0;
					$section_errors[$error['section_id']]++;
				}
				
				
				echo '<script type="text/javascript">';
					echo 'jQuery(document).ready(function(){';
						echo 'jQuery("#nhp-opts-field-errors span").html("'.count($errors).'");';
						echo 'jQuery("#nhp-opts-field-errors").show();';
						
						foreach($section_errors as $sectionkey => $section_error){
							echo 'jQuery("#'.$sectionkey.'_section_group_li_a").append("<span class=\"nhp-opts-menu-error\">'.$section_error.'</span>");';
						}
						
						foreach($errors as $error){
							echo 'jQuery("#'.$error['id'].'").addClass("nhp-opts-field-error");';
							echo 'jQuery("#'.$error['id'].'").closest("td").append("<span class=\"nhp-opts-th-error\">'.$error['msg'].'</span>");';
						}
					echo '});';
				echo '</script>';
				delete_transient('nhp-opts-errors');
			}
		
	}//function
	
	
	
	/**
	 * JS to display the warnings on the page
	 *
	 * @since NHP_Options 1.0.3
	*/	
	function _warnings_js(){
		
		if(isset($_GET['settings-updated']) && $_GET['settings-updated'] == 'true' && get_transient('nhp-opts-warnings')){
				$warnings = get_transient('nhp-opts-warnings');
				$section_warnings = array();
				foreach($warnings as $warning){
					$section_warnings[$warning['section_id']] = (isset($section_warnings[$warning['section_id']]))?$section_warnings[$warning['section_id']]:0;
					$section_warnings[$warning['section_id']]++;
				}
				
				
				echo '<script type="text/javascript">';
					echo 'jQuery(document).ready(function(){';
						echo 'jQuery("#nhp-opts-field-warnings span").html("'.count($warnings).'");';
						echo 'jQuery("#nhp-opts-field-warnings").show();';
						
						foreach($section_warnings as $sectionkey => $section_warning){
							echo 'jQuery("#'.$sectionkey.'_section_group_li_a").append("<span class=\"nhp-opts-menu-warning\">'.$section_warning.'</span>");';
						}
						
						foreach($warnings as $warning){
							echo 'jQuery("#'.$warning['id'].'").addClass("nhp-opts-field-warning");';
							echo 'jQuery("#'.$warning['id'].'").closest("td").append("<span class=\"nhp-opts-th-warning\">'.$warning['msg'].'</span>");';
						}
					echo '});';
				echo '</script>';
				delete_transient('nhp-opts-warnings');
			}
		
	}//function

	
	
	/**
	 * Section HTML OUTPUT.
	 *
	 * @since NHP_Options 1.0
	*/	
	function _section_desc($section){
		
		$id = rtrim($section['id'], '_section');
		
		if(isset($this->sections[$id]['desc']) && !empty($this->sections[$id]['desc'])) {
			echo '<div class="nhp-opts-section-desc">'.$this->sections[$id]['desc'].'</div>';
		}
		
	}//function
	
	
	
	
	/**
	 * Field HTML OUTPUT.
	 *
	 * Gets option from options array, then calls the speicfic field type class - allows extending by other devs
	 *
	 * @since NHP_Options 1.0
	*/
	function _field_input($field, $group_id = '', $index = 0){
		
		
		if(isset($field['callback']) && function_exists($field['callback'])){
			$value = (isset($this->options[$field['id']]))?$this->options[$field['id']]:'';
			do_action('nhp-opts-before-field', $field, $value);
			do_action('nhp-opts-before-field-'.$this->args['opt_name'], $field, $value);
			call_user_func($field['callback'], $field, $value);
			do_action('nhp-opts-after-field', $field, $value);
			do_action('nhp-opts-after-field-'.$this->args['opt_name'], $field, $value);
			return;
		}
		
		if(isset($field['type'])){
			
			$field_class = 'NHP_Options_'.$field['type'];
			
			if(class_exists($field_class)){
				require_once($this->dir.'fields/'.$field['type'].'/field_'.$field['type'].'.php');
			}//if
			
			if(class_exists($field_class)){
				$value = (isset($this->options[$field['id']]))?$this->options[$field['id']]:'';
                if (!empty($group_id)) $value = (@isset($this->options[$group_id][$index][$field['id']]))?$this->options[$group_id][$index][$field['id']]:'';
                do_action('nhp-opts-before-field', $field, $value);
				do_action('nhp-opts-before-field-'.$this->args['opt_name'], $field, $value);
				$render = '';
				$render = new $field_class($field, $value, $this);
				$render->render();
				do_action('nhp-opts-after-field', $field, $value);
				do_action('nhp-opts-after-field-'.$this->args['opt_name'], $field, $value);
			}//if
			
		}//if $field['type']
		//if (!empty($group_id)) return $value;
	}//function
    
    function ajax_mts_translation_panel() {
        $poparser = new PoParser();
        $mts_translations = get_option('mts_translations_'.MTS_THEME_NAME);//$this->options['translations'];
        $entries = $poparser->read(get_template_directory().'/lang/default.po');
        $i = 0;
        
        $page = (empty($_POST['page']) ? 1 : (int) $_POST['page']);
        $search_query = (empty($_POST['search']) ? '' : $_POST['search']);
        $strings_per_page = 20;
        $strings_tmp = array();
        if ($search_query) {
            foreach ($entries as $string_id => $object) {
                $message = '';
                foreach ($object['msgid'] as $line) {
                    $message .= $line;
                }
                $value = (empty($mts_translations[$message]) ? '' : $mts_translations[$message]);
                if (stristr($value, $search_query) !== false || stristr($message, $search_query) !== false) {
                    $strings_tmp[$string_id] = $object;
                }
            }
            $entries = $strings_tmp;
        }
        $number = count($entries);
        $number_translated = 0;

        $this->mts_translation_pagination($number, $strings_per_page, $page);
        
        $form = '';
        foreach ($entries as $string_id => $object) {
            $i++;
            $message = '';
            foreach ($object['msgid'] as $line) {
                $message .= $line;
            }
            
            if (!empty($mts_translations[$message]))
                $number_translated++;
                
            if ($i > ($page-1)*$strings_per_page && $i <= $page*$strings_per_page) {
                
                $reference = implode(' ', $object['reference']);
                $reference = implode(', ', explode(' ', $reference));
                
                $value = (empty($mts_translations[$message]) ? '' : $mts_translations[$message]);
                $form .= '<div class="translate-string-wrapper">';
                // debug
                //echo '<!-- '.print_r($object,1).' -->';
                $form .= '<label for="translate-string-'.$i.'">'.esc_html($message).' <span>('.$reference.')</span></label>';
                //echo '<input type="text" name="'.$this->args['opt_name'].'[translations]['._wp_specialchars( $message, ENT_QUOTES, false, true ).']" id="translate-string-'.$i.'" value="'._wp_specialchars( $value, ENT_QUOTES, false, true ).'">';
                $form .= '<textarea id="translate-string-'.$i.'" data-id="'._wp_specialchars( $message, ENT_QUOTES, false, true ).'" class="mts_translate_textarea">';
                    $form .= esc_textarea($value);
                $form .= '</textarea>';
                $form .= '</div>';
            }
        }
        
        echo $form;
        
        if ($number == 0) 
            $percent = 0;
        else
            $percent = $number_translated / $number * 100;
            
        echo '<div class="translation_info">'.sprintf(__('Translated <span class="translated">%1$d</span> strings out of <span class="total">%2$d</span> <span class="percent">(%3$.2f%%)</span>', 'mythemeshop'), $number_translated, $number, $percent).'</div>';
        $this->mts_translation_pagination($number, $strings_per_page, $page);
        
        exit; // required for AJAX in WP
    }
    
    function mts_translation_pagination( $items_number, $items_per_page, $current = 1 ) {
        $max_page = ceil($items_number / $items_per_page);
        echo '<div class="mts_translation_pagination">';
        for ($i = 1; $i <= $max_page; $i++) {
            echo '<a href="#"'.($i == $current ? ' class="current"' : '').'>'.$i.'</a> ';
        }
        echo '</div>';
    }
    
    function ajax_mts_save_translation() {
        $id = stripslashes($_POST['id']);
        $val = stripslashes($_POST['val']);
        
        if ( empty( $id ) || ! is_string( $id ) || ! is_string( $val ) ) {
            echo 0;
            exit;
        }
        
        $translations = get_option('mts_translations_'.MTS_THEME_NAME);
        $translations[$id] = $val;
        update_option('mts_translations_'.MTS_THEME_NAME, $translations);
        echo 1;
        
        exit;
    }

    function mts_child_theme() {

		if ( is_child_theme() ) {

			return; // Return if this is child theme
		}

		$child_name = stripslashes($_POST['child_name']);

		if ( empty( $child_name ) ) {

			return;
		}

		$access_type = get_filesystem_method();
		if ( $access_type === 'direct' ) {

			/* you can safely run request_filesystem_credentials() without any issues and don't need to worry about passing in a URL */
			$creds = request_filesystem_credentials( site_url() . '/wp-admin/', '', false, false, array() );

			/* initialize the API */
			if ( ! WP_Filesystem( $creds ) ) {
				/* any problems and we exit */
				return false;
			}	

			global $wp_filesystem;
			/* do our file manipulations below */
			$parent_theme = wp_get_theme();// current theme object
			$parent_stylesheet = $parent_theme->get_stylesheet();
			$parent_name       = $parent_theme->name;
			$parent_dir_name   = $parent_theme->template;

			if ( !empty( $child_name ) ) {

				$child_theme_directory = trailingslashit( get_theme_root() ) . sanitize_file_name( strtolower( $child_name ) );

				if ( !$wp_filesystem->is_dir( $child_theme_directory ) ) {

					$wp_filesystem->mkdir( $child_theme_directory );
					$child_stylesheet = trailingslashit( $child_theme_directory ) . 'style.css';
					$wp_filesystem->touch( $child_stylesheet );
					$child_stylesheet_contents = <<<EOF
/*
Theme Name: $child_name
Version: 1.0
Description: A child theme of $parent_name
Template: $parent_stylesheet
*/

@import url("../$parent_stylesheet/style.css");

EOF;
					$wp_filesystem->put_contents( $child_stylesheet, $child_stylesheet_contents );
				}

			}

		} else {
			/* don't have direct write access. Prompt user with our notice */
			//add_action('admin_notice', array(&$this,'mts_filesystem_notice'));
		}
	}

	function mts_filesystem_notice() {
		echo '<div class="updated"><p>You do not have permission to do this.</p></div>';
	}

	function mts_get_child_themes() {
		$child_themes = array();
		$current_theme = wp_get_theme();// current theme object
		$current_theme_dir_name   = $current_theme->template;

		$themes = wp_get_themes();// get all themes

		foreach ( $themes as $slug => $data ) { // check each theme

			if ( $data->template !== $data->stylesheet && $data->template === $current_theme_dir_name ) {

				$child_themes[ $slug ] = $data->name; // Generate array
			}
		}

		return $child_themes;
	}

	function mts_list_child_themes() {
		$child_themes_arr = $this->mts_get_child_themes();

		echo '<div class="nhp-opts-field-wrapper">';
		if ( !empty( $child_themes_arr ) ) {
			echo '<p class="description">' . __('Existing child themes for current theme:', 'mythemeshop' ) . '</p>';
			echo '<ul class="child-theme-list">';
				foreach ( $child_themes_arr as $child_theme ) {
					echo '<li><mark>'.$child_theme.'</mark></li>';
				}
			echo '</ul>';
		} else {
			_e('No child themes found for current theme', 'mythemeshop' );
		}
		echo '</div>';

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) die();
	}
	
}//class
}//if

?>