
jQuery(document).ready( function() {

	/** translate code language name to Prism style and highlight */
	if ( Prism ) {
		jQuery('pre > code[class]').each( function() {
			if ( jQuery(this).hasClass( 'language-shell' ) )
				jQuery(this).attr( 'class', 'language-bash' );
			if ( jQuery(this).hasClass( 'language-js' ) )
				jQuery(this).attr( 'class', 'language-javascript' );
			if ( jQuery(this).hasClass( 'language-json' ) )
				jQuery(this).attr( 'class', 'language-javascript' );
			if ( jQuery(this).hasClass( 'language-html' ) )
				jQuery(this).attr( 'class', 'language-markup' );
		} );
		/** apply Markup to code blocks where no languages defined */
		jQuery('pre > code:not([class])').each( function() {
			jQuery(this).attr( 'class', 'language-markup' );
		} );
		/** re-highlight all code blocks */
		Prism.highlightAll();
	}

	/** initial navigation menu toggling */
	if ( jQuery(window).width() < 768 ) {
		jQuery('#wiki-nav').removeClass('in');
	} else {
		jQuery('#wiki-nav').addClass('in');
	}

	/** toggle navigation menu automatically */
	jQuery(window).bind( 'resize', function() {
		if ( jQuery(window).width() < 768 && jQuery('#wiki-nav').hasClass('in') &&
				jQuery('#wiki-nav-toggle').hasClass('collapsed') ) {
			jQuery('#wiki-nav').removeClass('in');
		}
		if ( jQuery(window).width() >= 768 && ! jQuery('#wiki-nav').hasClass('in') ) {
			jQuery('#wiki-nav').addClass('in');
			jQuery('#wiki-nav-toggle').addClass('collapsed');
		}
	} );

} );
