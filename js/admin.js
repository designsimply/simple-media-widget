/**
 * Main jQuery media file for the plugin.
 *
 * @since 1.0.0
 *
 * @package Simple Media Widget
 * @author  Sheri Bigelow
 */
var smw = smw || {};

(function( $ ) {
	var frame, media_widget_frame;

	smw.media = frame = {
		buttonId: '.media-widget-preview .button, .media-widget-preview .image',

		init: function() {
			$( frame.buttonId ).on( 'click', frame.openMediaManager );
		},

		openMediaManager: function( event ) {
			event.preventDefault();
			var widget_id = $( event.target ).data( 'id');

			// Create the media frame.
			media_widget_frame = wp.media({
					library:    { type: 'image' },
					frame:      'post',
					state:      'insert'
			});

			// Populate previously selected media when the media frame is opened.
			media_widget_frame.on( 'open', function() {
				var selection = media_widget_frame.state().get('selection');
				var ids = $( '#widget-' + widget_id + '-id' ).val().split(',');

				if ( ids[0] > 0 ) {
					ids.forEach( function( id ) {
						var attachment = wp.media.attachment( id );
						attachment.fetch();
						selection.add( attachment ? [ attachment ] : [] );
					});
				}
			});

			// Render the attachment details.
			media_widget_frame.on( 'close', function() {
				// Only try to render the attachment details if a selection was made.
				if ( media_widget_frame.state().get('selection').length > 0 ) {
					var props = media_widget_frame.content.get('.attachments-browser').sidebar.get('display').model.toJSON();
					var attachment = media_widget_frame.state().get('selection').first().toJSON();

					frame.renderAttachmentDetails( widget_id, props, attachment );
				}
			});

			media_widget_frame.open( widget_id );
		},

		/**
		 * Renders the attachment details from the media modal into the widget.
		 *
		 * @param {Object} props Attachment Display Settings (align, link, size, etc).
		 * @param {Object} attachment Attachment Details (title, description, caption, url, sizes, etc).
		 */
		renderAttachmentDetails: function( widget_id, props, attachment ) {
			// Start with container elements for the widgets page, customizer controls, and customizer preview.
			var rendered_view = $( '.' + widget_id + ', #customize-control-widget_' + widget_id + ', #' + widget_id );

			// The widget title bar doesn't update automatically on the Appearance > Widgets page. This fixes that problem.
			rendered_view.closest( '.widget' ).find( '.in-widget-title' ).html( ': ' + attachment.title );

			// Display a preview of the image in the widgets page and customizer controls.
			rendered_view.find( '.extras' ).removeClass( 'hidden' );
			if ( attachment.description ) {
				rendered_view.find( '.attachment-description' ).removeClass( 'hidden' );
			} else {
				rendered_view.find( '.attachment-description' ).addClass( 'hidden' );
			}
			rendered_view.find( '.attachment-description' ).html( attachment.description );
			rendered_view.find( '.image' ).attr({
				'data-id': widget_id,
				'src':     attachment.sizes[props.size].url,
				'class':   'image align' + props.align,
				'title':   attachment.title,
				'alt':     attachment.alt,
				'width':   attachment.sizes[props.size].width,
				'height':  attachment.sizes[props.size].height
			});

			// Populate form fields with selection data from the media frame.
			rendered_view.find( '#widget-' + widget_id + '-title' ).val( attachment.title );
			rendered_view.find( '#widget-' + widget_id + '-id' ).val( attachment.id );
			rendered_view.find( '#widget-' + widget_id + '-url' ).val( attachment.url );
			rendered_view.find( '#widget-' + widget_id + '-link' ).val( attachment.link);
			rendered_view.find( '#widget-' + widget_id + '-caption' ).val( attachment.caption );
			rendered_view.find( '#widget-' + widget_id + '-alt' ).val( attachment.alt );
			rendered_view.find( '#widget-' + widget_id + '-description' ).val( attachment.description );
			rendered_view.find( '#widget-' + widget_id + '-align' ).val( props.align );
			rendered_view.find( '#widget-' + widget_id + '-size' ).val( props.size );
			rendered_view.find( '#widget-' + widget_id + '-linkTo' ).val( props.link );
			rendered_view.find( '#widget-' + widget_id + '-linkUrl' ).val( props.linkUrl );
			rendered_view.find( '#widget-' + widget_id + '-width' ).val( attachment.sizes[props.size].width );

			// Trigger a sync to update the widget in the customizer preview.
			rendered_view.find( '#widget-' + widget_id + '-url' ).trigger( 'change' );
		}
	};

	$( document ).ready( frame.init );
	$( document ).on( 'widget-added widget-updated', frame.init );

})( jQuery );
