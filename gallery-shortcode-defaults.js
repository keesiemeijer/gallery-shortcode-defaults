( function( $ ) {

	// variable set by wp_localize_script()
	if( typeof gallery_shortcode_defaults === 'undefined' ) {
		return;
	}

	var media = wp.media;
	var editing = false;
	var obj = ( _.isObject( gallery_shortcode_defaults ) ) ? gallery_shortcode_defaults : {};

	media.view.MediaFrame.Post = media.view.MediaFrame.Post.extend( {

		initialize: function() {

			media.view.MediaFrame.Select.prototype.initialize.apply( this, arguments );

			if ( _.isUndefined( this.options.editing ) ) {
				editing = false;
			} else {
				editing = true;
			}

		}

	} );


	media.view.Settings.Gallery = media.view.Settings.Gallery.extend( {

		render: function() {

			media.view.Settings.prototype.render.apply( this, arguments );

			// Only set defaults when not editing an existing gallery.
			if ( !editing ) {

				if ( _.has( obj, 'link' ) )
					this.model.set( 'link', _.escape( obj.link ) );

				if ( _.has( obj, 'columns' ) )
					this.model.set( 'columns', parseInt( _.escape( obj.columns ), 10 ) );

				if ( _.has( obj, '_orderbyRandom' ) )
					this.model.set( '_orderbyRandom', _.escape( obj._orderbyRandom ) );
			}

			if ( _.has( obj, 'size' ) ) {

				// Append the custom template
				this.$el.append( media.template( 'custom-size-setting' ) );

				this.update.apply( this, [ 'size' ] );
			}

			editing = false;
		}

	} );

} )( jQuery );