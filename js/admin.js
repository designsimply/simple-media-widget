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

			// Render the attachment details.
			media_widget_frame.on( 'close', function() {
				var props = media_widget_frame.content.get('.attachments-browser').sidebar.get('display').model.toJSON();
				var attachment = media_widget_frame.state().get('selection').first().toJSON();

				frame.renderAttachmentDetails( widget_id, props, attachment );
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


			// Display a preview of the image in the widgets page and customizer controls.
			rendered_view.find( '.extras' ).removeClass( 'hidden' );
			rendered_view.find( '.description' ).html( attachment.description );
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

	$( document ).ready( function() {
		frame.init();
	});

	$( document ).on( 'widget-added widget-updated', function() {
		frame.init();
	});

})( jQuery );
