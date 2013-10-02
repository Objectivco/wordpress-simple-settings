<?php
global $AwesomePlugin; // we'll need this below
?>
<div class="wrap">
    <h2>Awesome Plugin</h2>

    <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
    	<?php $AwesomePlugin->the_nonce(); ?>
    	<table class="form-table">
			<tbody>
				<tr>
					<th scope="row" valign="top">Favorite Color</th>
					<td>
						<label>
							<input type="text" name="<?php echo $AwesomePlugin->get_field_name('favorite_color'); ?>" value="<?php echo $AwesomePlugin->get_setting('favorite_color'); ?>" /><br />
							Put your favorite color here, boss!
						</label>
					</td>
				</tr>	
				<tr>
					<th scope="row" valign="top">Favorite Array</th>
					<td>
						<label>
							<input type="text" name="<?php echo $AwesomePlugin->get_field_name('favorite_array'); ?>" value="<?php echo $AwesomePlugin->get_setting('favorite_array'); ?>" /><br />
							This array is so clutch.
						</label>
						
<pre>
<?php print_r( $AwesomePlugin->get_setting('favorite_array', 'array') ); ?>
</pre>
					</td>
				</tr>
				<tr>
					<th scope="row" valign="top">Favorite Checkbox</th>
					<td>
						<label>
							<input type="hidden" name="<?php echo $AwesomePlugin->get_field_name('favorite_checkbox'); ?>" value="no" />
							<input type="checkbox" name="<?php echo $AwesomePlugin->get_field_name('favorite_checkbox'); ?>" value="yes" <?php if ( $AwesomePlugin->get_setting('favorite_checkbox') == "yes") echo 'checked="checked"'; ?> />	Check this box, son!
						</label>
					</td>
				</tr>	
			</tbody>
    	</table>
    	<input class="button-primary" type="submit" value="Save Settings" />
    </form>
</div>