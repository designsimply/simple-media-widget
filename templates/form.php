<div class="<?php echo esc_attr( $widget_id ); ?> media-widget-preview">
	<p>
		<a data-id="<?php echo esc_attr( $widget_id ); ?>" class="button select-media widefat">
			<?php esc_html_e( 'Select Media' ); ?>
		</a>
	</p>

	<p>
		<label for="<?php echo $this->get_field_id('title'); ?>"><?php esc_html_e('Title:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
			 name="<?php echo $this->get_field_name('title'); ?>"
			 type="text" value="<?php echo esc_html($instance['title'] ); ?>" />
	</p>

	<p class="attachment-description <?php echo ( empty( $instance['description'] ) ) ? 'hidden' : '' ?>">
		<?php echo esc_html( $instance['description'] ); ?>
	</p>

	<p>
		<?php if ( ! empty( $instance['id'] ) ) {
			// If an image id is saved for this widget, display the image using `wp_get_attachment_image()`.
			echo wp_get_attachment_image( esc_attr( $instance['id'] ), esc_attr( $instance['size'] ), false, array(
				'data-id' => esc_attr( $widget_id ),
				'class' => 'image align' . esc_attr( $instance['align'] ),
				'title' => esc_attr( $instance['title'] ),
			) );
		} else {
				echo '<img class="image hidden" />';
		} ?>
	</p>

	<p class="extras<?php if ( empty( $instance['id'] ) ) { echo ' hidden'; } ?>">
		<input type="checkbox" name="<?php echo $this->get_field_name( 'target' ); ?>"
			id="<?php echo $this->get_field_id( 'target' ); ?>"
			<?php if ( ! empty( $instance['target'] ) ) { checked( 'on', $instance['target'] ); } ?> />
		<label for="<?php echo $this->get_field_id( 'target' ); ?>"><?php esc_html_e( 'Open link in a new tab or window', 'simple-media-widget' ); ?></label>
	</p>

	<?php // Use hidden form fields to capture the attachment details from the media manager.
	unset( $instance['title'] );
	unset( $instance['target'] );
	foreach( array_keys( $instance ) as $i ) { ?>
		<input type="hidden" id="<?php echo $this->get_field_id( $i ); ?>" name="<?php echo $this->get_field_name( $i ); ?>" value="<?php echo esc_attr( $instance[$i] ); ?>" />
	<?php } ?>
</div>
