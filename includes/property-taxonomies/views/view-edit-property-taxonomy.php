<?php // $this refers to Easy_Real_Estate_Property_Taxonomies class ?>

<?php
$property_taxonomies = self::get_taxonomies();

if ( array_key_exists( $_REQUEST['id'], ( array ) $property_taxonomies ) )
	$ere_taxonomy = stripslashes_deep( $property_taxonomies[$_REQUEST['id']] );
else
	wp_die( 'Sorry but that taxonomy doesn\'t exist or can\'t be edited. Click back and try again.' );
?>

<?php screen_icon( 'themes' ); ?>
<h2>Edit Taxonomy</h2>

<form method="post" action="<?php echo admin_url( 'admin.php?page=' . self::$ere_property_menu_page . '&amp;action=edit' ); ?>">
	<?php wp_nonce_field( 'ere_edit_property_taxonomy', 'ere_edit_property_taxonomy_nonce' ); // Nonce for verification ?>

	<table class="form-table">
		<tr class="form-field">
			<th scope="row" valign="top"><label for="ere_taxonomy[name]">Plural Name</label></th>
			<td><input name="ere_taxonomy[name]" id="ere_taxonomy[name]" type="text" value="<?php echo esc_html( $ere_taxonomy['labels']['name'] ); ?>" size="40" />
			<p class="description">Example: "Prices" or "Locations"</p></td>
		</tr>
		<tr class="form-field">
			<th scope="row" valign="top"><label for="ere_taxonomy[singular_name]">Singular Name</label></th>
			<td><input name="ere_taxonomy[singular_name]" id="ere_taxonomy[singular_name]" type="text" value="<?php echo esc_html( $ere_taxonomy['labels']['singular_name'] ); ?>" size="40" />
			<p class="description">Example: "Price" or "Location"</p></td>
		</tr>
		<tr class="form-field">
			<th scope="row" valign="top"><label for="ere_taxonomy[id]">Slug</label></th>
			<td>
			<input type="text" value="<?php echo esc_html( $_REQUEST['id'] ); ?>" size="40" disabled="disabled" />
			<input name="ere_taxonomy[id]" id="ere_taxonomy[id]" type="hidden" value="<?php echo esc_html( $_REQUEST['id'] ); ?>" size="40" />
			<p class="description">The slug is the URL-friendly version of the name used to identify the taxonomy (cannot be changed).</p></td>
		</tr>
	</table>

	<?php submit_button( 'Update Taxonomy' ); ?>
</form>