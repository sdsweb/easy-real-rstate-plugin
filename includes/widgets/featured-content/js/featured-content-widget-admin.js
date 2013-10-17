/*
 * This script changes the total number of posts to be displayed based on the content type selection from the user.
 */
jQuery( function( $ ) {
	$( document ).on( 'change', '.ere-featured-content-type', function() {
		var selected = $( ':selected', this ),
		widget_parent = selected.parents( '.widget'),
		content_type = selected.parent().attr( 'data-content-type' ),
		value = selected.val(),
		num_posts_select = widget_parent.find( '.ere-featured-num-posts' );
		num_posts_select_current_count = parseInt( num_posts_select.val() );
		num_posts = parseInt( num_posts_select.attr( 'data-count-' + value ) );

		// Reset number of posts select
		num_posts_select.find( 'option' ).remove().end().append( '<option value="">Select Number of Posts</option>' );

		// Append correct number of posts to select
		for( var i = 1; i <= num_posts; i++ )
			num_posts_select.append( ( i === num_posts_select_current_count ) ? '<option value="' + i + '" selected="selected">' + i + '</option>' : '<option value="' + i + '">' + i + '</option>' );

		// If the current selection is greater than the new number of posts, select the largest value
		if ( num_posts_select_current_count > num_posts )
			num_posts_select.val( num_posts );
	} );
} );