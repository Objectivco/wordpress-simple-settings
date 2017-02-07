WordPress Simple Settings
=========================

A minimalist framework for managing WordPress plugin or theme settings.

What Is This
------------

This is a super simple framework for managing options in your WordPress plugin or theme. No cumbersome field section registration or other overhead is required.  Just add some settings, update them, get them. Whatever.

It handles nonces, field names, and etc magically!

How Does It Work
----------------

WordPress Simple Settings is an abstract class which you extend in your own plugin class, theme class, or wherever you need it.

It takes a prefix, set by you and builds a settings object which is stored in a single option in the database.

This last bit should be a clue to what type of data you should store using this API: small amounts!

Just like the built-in Options API (which this framework uses!), you only want to use this for small bits of data.  Don't try to store your customer database here!

Installation
------------
The best way is to use composer: `composer require objectivco/wordpress-simple-settings`

You can also clone the repository into your lib or inc directory and require `wordpress-simple-settings.php`. But composer is better.

You can get a quick idea of how to implement in our example plugin: `examples/awesome-plugin`.

The gist is, you'll instantiate your plugin class as extension of `WP_SimpleSettings`. You'll then set a prefix as a class variable called `$prefix`.

If you don't set `$prefix`, the framework will try to use your child class name.  But this will only work in PHP 5.3+.  So, really, just set a prefix ok?

Change Log
--------
### 0.3.0
- Added $delimeter variable to allow switching array delimeter from semi-colon (default) to something else.

### 0.2.2
- Added delete_setting.

### 0.2.1
- Fix location of {$this->prefix}_settings_saved action to prevent inappropriate firing.

### 0.2
- Improves storage and handling of array values.
- Adds do_action("{$this->prefix}_settings_saved") hook for adding your own actions after plugin settings are saved.

### 0.1
- Initial version.

Usage
-----

Here are the basic functions available to you within your plugin.

### add_setting($option_name, $value)

This is essentially a wrapper for `update_setting` that respectfully does not make any changes if the option in question is already set.

You'll most frequently use this in your activation hook.

### delete_setting($option_name)

Removes setting.

### get_setting($option_name, $type)

Retrieves a specific option.  Returns string by default.  If you specify `array` as `$type`, it will treat the value as a delimited string (default semi-colon) and return an array.  

### update_setting($option_name, $new_value)

Updates a specific option.

### get_field_name($option_name, $type)

Gets an HTML field name.  If you set `$type` to array, the field value will be treated as a delimited string (default semi-colon) and stored as an array.  

### the_nonce()

Call `$YourPluginInstance->the_nonce()` somewhere in your admin form to generate the nonce used to validate / save settings.

### save_settings()

There is no need to call this unless you want to override the default functionality.  This function will be called on `admin_init` and automagically saves settings, if the right nonce and `$_REQUEST` is set.

### Settings Object

One of my goals in building this framework was to have an easily accessible settings object that can be used directly when the getters / setters are inconvenient.  This is done by simply using `$this->settings[$option_name]`.

Obviously this should be used in read-only applications.  Setting values here will not update them in the database.


Feedback
--------

I'm in no way claiming to have the perfect, final answer to managing options in WordPress themes or plugins.  

The goal of this minimalist approach is that you can simply build your plugin rather than contorting it to work with more demanding APIs.

If you have any ideas for how I can improve on what I have here, please let me know!
