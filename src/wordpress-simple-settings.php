<?php
/**
 * WordPress Simple Settings
 *
 * A simple framework for managing WordPress plugin settings.
 *
 * @author Clifton H. Griffin II
 * @version 0.7.3
 * @copyright Objectiv 2013-2017
 * @license GNU GPL version 3 (or later) {@see license.txt}
 **/
abstract class WordPress_SimpleSettings {
	var $settings = array();
	var $prefix;
	var $delimiter;
	var $network_only = false;

	/**
	 * Constructor
	 *
	 * @author Clifton H. Griffin II
	 * @since 0.1.0
	 *
	 * @return void
	 **/
	public function __construct() {
		// Set a default prefix
		if ( function_exists( 'get_called_class' ) && empty( $this->prefix ) ) {
			$this->prefix = get_called_class();
		}

		// Set a default delimiter for separated values
		if ( empty( $this->delimiter ) ) {
			$this->delimiter = ';';
		}

		$this->settings = $this->get_settings_obj();

		add_action( 'admin_init', array( $this, 'save_settings' ) );
	}

	/**
	 * Add a new setting
	 *
	 * @author Clifton H. Griffin II
	 * @since 0.1.0
	 *
	 * @param string|bool $setting The name of the new option
	 * @param string $value The value of the new option
	 * @return boolean True if successful, false otherwise
	 **/
	public function add_setting( $setting, $value ) {
		if ( ! isset( $this->settings[ $setting ] ) ) {
			return $this->update_setting( $setting, $value );
		} else {
			return false;
		}
	}

	/**
	 * Updates or adds a setting
	 *
	 * @param string|bool $setting The name of the option
	 * @param string|array $value The new value of the option
	 * @param bool $save_to_db
	 *
	 * @return boolean True if successful, false if not
	 * @author Clifton H. Griffin II
	 * @since 0.1.0
	 *
	 */
	public function update_setting( $setting, $value, $save_to_db = true ) {
		if ( $setting === false ) {
			return false;
		}

		$old_value                  = isset( $this->settings[ $setting ] ) ? $this->settings[ $setting ] : NULL;
		$this->settings[ $setting ] = $value;

		do_action( "{$this->prefix}_update_setting", $setting, $old_value, $value );
		do_action( "{$this->prefix}_update_setting_{$setting}", $old_value, $value );

		if ( $save_to_db ) {
			return $this->set_settings_obj( $this->settings );
		}

		return true;
	}

	/**
	 * Deletes a setting
	 *
	 * @author Clifton H. Griffin II
	 * @since 0.1
	 *
	 * @param string $setting The name of the option
	 * @return boolean True if successful, false if not
	 **/
	public function delete_setting( $setting ) {
		if ( ! isset( $this->settings[ $setting ] ) ) {
			return false;
		}

		unset( $this->settings[ $setting ] );

		return $this->set_settings_obj( $this->settings );
	}

	/**
	 * Retrieves a setting value
	 *
	 * @author Clifton H. Griffin II
	 * @since 0.1.0
	 *
	 * @param string $setting The name of the option
	 * @param string $type The return format preferred, string or array. Default: string
	 * @return mixed The value of the setting
	 **/
	public function get_setting( $setting, $type = 'string' ) {
		if ( ! isset( $this->settings[ $setting ] ) ) {
			return false;
		}

		$value = $this->settings[ $setting ];

		if ( strtolower( $type ) == 'array' && ! empty( $value ) ) {
			$value = (array) explode( $this->delimiter, $value );
		}

		return apply_filters( $this->prefix . '_get_setting', $value, $setting );
	}

	/**
	 * Generates HTML field name for a particular setting
	 *
	 * @author Clifton H. Griffin II
	 * @since 0.1.0
	 *
	 * @param string $setting The name of the setting
	 * @param string $type The return format of the field, string or array. Default: string
	 * @return string The name of the field
	 **/
	public function get_field_name( $setting, $type = 'string' ) {
		return "{$this->prefix}_setting[$setting][$type]";
	}

	/**
	 * Prints nonce for admin form
	 *
	 * @author Clifton H. Griffin II
	 * @since 0.1.0
	 *
	 * @return void
	 **/
	public function the_nonce() {
		wp_nonce_field( "save_{$this->prefix}_settings", "{$this->prefix}_save" );
	}

	/**
	 * Saves settings
	 *
	 * @author Clifton H. Griffin II
	 * @since 0.1.0
	 *
	 * @return void
	 **/
	public function save_settings() {
		if ( isset( $_REQUEST[ "{$this->prefix}_setting" ] ) && check_admin_referer( "save_{$this->prefix}_settings", "{$this->prefix}_save" ) ) {
			$new_settings = $_REQUEST[ "{$this->prefix}_setting" ];

			// Strip Magic Slashes
			$new_settings = stripslashes_deep( $new_settings );

			foreach ( $new_settings as $setting_name => $setting_value ) {
				foreach ( $setting_value as $type => $value ) {
					if ( $type == 'array' ) {
						if ( ! is_array( $value ) && ! empty( $value ) ) {
							$value = (array) explode( $this->delimiter, $value );
						}

						$this->update_setting( $setting_name, $value, false );
					} else {
						$this->update_setting( $setting_name, $value, false );
					}
				}
			}

			// Actually write the changes to the db
			$this->set_settings_obj( $this->settings );

			do_action( "{$this->prefix}_settings_saved" );
		}
	}

	/**
	 * Gets settings object
	 *
	 * @author Clifton H. Griffin II
	 * @since 0.1.0
	 *
	 * @return array The settings array
	 **/
	public function get_settings_obj() {
		if ( $this->network_only ) {
			return get_site_option( "{$this->prefix}_settings", false );
		} else {
			return get_option( "{$this->prefix}_settings", false );
		}
	}

	/**
	 * Sets settings object
	 *
	 * @author Clifton H. Griffin II
	 * @since 0.1.0
	 *
	 * @param array $newobj The new settings object
	 * @return boolean True if successful, false otherwise
	 **/
	public function set_settings_obj( $newobj ) {
		if ( $this->network_only ) {
			return update_site_option( "{$this->prefix}_settings", $newobj );
		} else {
			return update_option( "{$this->prefix}_settings", $newobj );
		}
	}
}
