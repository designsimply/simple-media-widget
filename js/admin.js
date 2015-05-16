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
	var frame;

	smw.media = frame = {
		buttonId: '.simple-media-widget-select.button, .simple-media-widget-preview img',

		init: function() {
			$( frame.buttonId ).on( 'click', this.openMediaManager );
		},

		openMediaManager: function( e ) {
			e.preventDefault();

			wp.media.editor.send.attachment = frame.renderAttachmentDetails;
			wp.media.editor.remove = frame.closeMediaManager;

			var widget_id = $( e.target ).attr( 'id');
			wp.media.editor.open( widget_id );
		},

		/**
		 * Renders the attachment details from the media modal into the widget.
		 *
		 * @global wp.media.editor.activeEditor
		 *
		 * @param {Object} props Attachment Display Settings (align, link, size, etc).
		 * @param {Object} attachment Attachment Details (title, description, caption, url, sizes, etc).
		 */
		renderAttachmentDetails: function( props, attachment ) {
			var widget_id = wp.media.editor.activeEditor;

			// Display a preview of the image in the widgets page or customizer panel.
			$( '.simple-media-widget-preview.' + widget_id + ' p' ).html( attachment.description );
			$( '.simple-media-widget-preview.' + widget_id + ' img' ).attr({
				'src':    attachment.sizes[props.size].url,
				'class':  'align' + props.align,
				'title':  attachment.title,
				'alt':    attachment.alt,
				'width':  attachment.sizes[props.size].width,
				'height': attachment.sizes[props.size].height
			});

			// Populate form fields with selection data from the media frame.
			$( '#widget-' + widget_id + '-title' ).val( attachment.title );
			$( '#widget-' + widget_id + '-id' ).val( attachment.id );
			$( '#widget-' + widget_id + '-url' ).val( attachment.url );
			$( '#widget-' + widget_id + '-link' ).val( attachment.link);
			$( '#widget-' + widget_id + '-caption' ).val( attachment.caption );
			$( '#widget-' + widget_id + '-alt' ).val( attachment.alt );
			$( '#widget-' + widget_id + '-description' ).val( attachment.description );
			$( '#widget-' + widget_id + '-align' ).val( props.align );
			$( '#widget-' + widget_id + '-size' ).val( props.size );
			$( '#widget-' + widget_id + '-linkTo' ).val( props.link );
			$( '#widget-' + widget_id + '-linkUrl' ).val( props.linkUrl );
			$( '#widget-' + widget_id + '-width' ).val( attachment.sizes[props.size].width );
		},

		closeMediaManager: function( id ) {
			wp.media.editor.remove( id );
		}
	};

	$( document ).ready( function( $ ) {
		frame.init();
	});

	$( document ).on( "widget-added", function(event, widget) {
			frame.init();
	});

	$( document ).on( "widget-updated", function() {
			frame.init();
	});

})( jQuery );

