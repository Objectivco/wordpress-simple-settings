<?php
abstract class WordPress_SimpleSettings {
	var $settings = array();
	var $prefix;

	public function __construct() {
		// Set a default prefix
		if( function_exists('get_called_class') && empty($this->prefix) ) $this->prefix = get_called_class();

		$this->settings = $this->get_settings_obj( $this->prefix );
		add_action('admin_init', array($this, 'save_settings') );
	}

	public function add_setting ( $option = false, $newvalue ) {
		if($option === false ) return false;

		if ( ! isset($this->settings[$option]) ) {
			return $this->update_setting($option, $newvalue);
		} else return false;
	}

	public function update_setting ( $option = false, $newvalue ) {
		if( $option === false ) return false;

		$this->settings = $this->get_settings_obj($this->prefix);
		$this->settings[$option] = $newvalue;
		return $this->set_settings_obj($this->settings);
	}

	public function get_setting ( $option = false, $type = 'string' ) {
		if($option === false || ! isset($this->settings[$option]) ) return false;
		
		$value = $this->settings[$option];
		
		if( strtolower($type) == 'array' ) {
			$value = (array)explode(";", $value);
		}

		return apply_filters($thix->prefix . '_get_setting', $value, $option);
	}

	public function get_field_name($setting, $type = 'string') {
		return "{$this->prefix}_setting[$setting][$type]";
	}

	public function the_nonce() {
		wp_nonce_field( "save_{$this->prefix}_settings", "{$this->prefix}_save" );
	}

	public function save_settings()
	{
		if( isset($_REQUEST["{$this->prefix}_setting"]) && check_admin_referer("save_{$this->prefix}_settings","{$this->prefix}_save") ) {
			$new_settings = $_REQUEST["{$this->prefix}_setting"];

			foreach( $new_settings as $setting_name => $setting_value  ) {
				foreach( $setting_value as $type => $value ) {
					if( $type == "array" ) {
						$this->update_setting($setting_name, (array)explode(";", $value) );
					} else {
						$this->update_setting($setting_name, $value);
					}
				}
			}

			add_action('admin_notices', array($this, 'saved_admin_notice') );
		}
	}

	public function get_settings_obj () {
		return get_option("{$this->prefix}_settings", false);
	}

	public function set_settings_obj ( $newobj ) {
		return update_option("{$this->prefix}_settings", $newobj);
	}
}