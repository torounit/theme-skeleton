<?php


/*
============================================

Default_Term

============================================
*/

Class Default_Term {

	public function __construct() {
		add_action( 'wp_insert_post', array( $this, 'add_post_type_default_term'), 10, 2 );
		add_filter( 'whitelist_options',  array( $this, 'allow_default_term_setting') );
		add_action( 'load-options-writing.php', array( $this, 'add_default_term_setting_item') );
	}

	public function add_default_term_setting_item() {
		$post_types = get_post_types( array( 'public' => true, 'show_ui' => true ), false );
		if ( $post_types ) {
			foreach ( $post_types as $post_type_slug => $post_type ) {
				$post_type_taxonomies = get_object_taxonomies( $post_type_slug, false );
				if ( $post_type_taxonomies ) {
					foreach ( $post_type_taxonomies as $tax_slug => $taxonomy ) {
						if ( ! ( $post_type_slug == 'post' && $tax_slug == 'category' ) && $taxonomy->show_ui ) {
							add_settings_field( $post_type_slug . '_default_' . $tax_slug, $post_type->label . '用' . $taxonomy->label . 'の初期設定' , array( $this,'default_term_setting_field'), 'writing', 'default', array( 'post_type' => $post_type_slug, 'taxonomy' => $taxonomy ) );
						}
					}
				}
			}
		}
	}


	public function default_term_setting_field( $args ) {
		$option_name = $args['post_type'] . '_default_' . $args['taxonomy']->name;
		$default_term = get_option( $option_name );
		$terms = get_terms( $args['taxonomy']->name, 'hide_empty=0' );
		if ( $terms ) :
	?>
		<select name="<?php echo $option_name; ?>">
			<option value="0">設定しない</option>
	<?php foreach ( $terms as $term ) : ?>
			<option value="<?php echo esc_attr( $term->term_id ); ?>"<?php echo $term->term_id == $default_term ? ' selected="selected"' : ''; ?>><?php echo esc_html( $term->name ); ?></option>
	<?php endforeach; ?>
		</select>
	<?php
		else:
	?>
		<p><?php echo esc_html( $args['taxonomy']->label ); ?>が登録されていません。</p>
	<?php
		endif;
	}


	public function allow_default_term_setting( $whitelist_options ) {
		$post_types = get_post_types( array( 'public' => true, 'show_ui' => true ), false );
		if ( $post_types ) {
			foreach ( $post_types as $post_type_slug => $post_type ) {
				$post_type_taxonomies = get_object_taxonomies( $post_type_slug, false );
				if ( $post_type_taxonomies ) {
					foreach ( $post_type_taxonomies as $tax_slug => $taxonomy ) {
						if ( ! ( $post_type_slug == 'post' && $tax_slug == 'category' ) && $taxonomy->show_ui ) {
							$whitelist_options['writing'][] = $post_type_slug . '_default_' . $tax_slug;
						}
					}
				}
			}
		}
		return $whitelist_options;
	}


	public function add_post_type_default_term( $post_id, $post ) {
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || $post->post_status == 'auto-draft' ) { return; }
		$taxonomies = get_object_taxonomies( $post, false );
		if ( $taxonomies ) {
			foreach ( $taxonomies as $tax_slug => $taxonomy ) {
				$default = get_option( $post->post_type . '_default_' . $tax_slug );
				if ( ! ( $post->post_type == 'post' && $tax_slug == 'category' ) && $taxonomy->show_ui && $default && ! ( $terms = get_the_terms( $post_id, $tax_slug ) ) ) {
					if ( $taxonomy->hierarchical ) {
						$term = get_term( $default, $tax_slug );
						if ( $term ) {
							wp_set_post_terms( $post_id, array_filter( array( $default ) ), $tax_slug );
						}
					} else {
						$term = get_term( $default, $tax_slug );
						if ( $term ) {
							wp_set_post_terms( $post_id, $term->name, $tax_slug );
						}
					}
				}
			}
		}
	}
}

