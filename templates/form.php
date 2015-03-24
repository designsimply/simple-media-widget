<p>
	<a id="<?php echo $widget_id; ?>" class="button simple-media-widget-select widefat"><?php esc_html_e( 'Select Media' ); ?></a>
</p>

<div class="<?php echo $widget_id; ?> simple-media-widget-preview">
	<h2<?php echo ( empty( $instance['title'] ) ) ? ' class="hidden">' : '>' . $instance['title']; ?></h2>
	<p<?php echo ( empty( $instance['description'] ) ) ? ' class="hidden">' : '>' . $instance['description']; ?></p>
	<?php if ( ! empty( $instance['id'] ) ) {
		// If an image id is saved for this widget, display the image using `wp_get_attachment_image()`.
		echo wp_get_attachment_image( $instance['id'], $instance['size'], false, array(
			'id'    => $widget_id,
			'class' => 'align' . $instance['align'],
			'title' => $instance['title'],
		) );
	} else {
			echo '<img id="' . $widget_id . '" class="hidden" />';
	} ?>
</div>

<p>
	<input type="checkbox" name="<?php echo $this->get_field_name( 'target' ); ?>"
		id="<?php echo $this->get_field_id( 'target' ); ?>"
		<?php if ( ! empty( $instance['target'] ) ) { checked( 'on', $instance['target'] ); } ?> />
	<label for="<?php echo $this->get_field_id( 'target' ); ?>"><?php esc_html_e( 'Open link in a new tab or window', 'simple-media-widget' ); ?></label>
</p>

<?php // Use hidden form fields to capture the attachment details from the media manager.
unset( $instance['target'] );
foreach( array_keys( $instance ) as $i ) { ?>
	<input type="hidden" id="<?php echo $this->get_field_id( $i ); ?>" name="<?php echo $this->get_field_name( $i ); ?>" value="<?php echo esc_attr( $instance[$i] ); ?>" />
<?php } ?>

