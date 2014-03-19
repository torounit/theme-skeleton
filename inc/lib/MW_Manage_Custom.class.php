<?php


/**
 *
 * MW Manage Custom
 *
 * カスタム投稿タイプ & カスタムタクソノミーのクラス
 */
Class MW_Manage_Custom {

	private $custom_post_type = array();
	private $custom_post_dashboard = array();
	private $custom_taxonomy = array();

	/**
	 * 実行
	 */
	public function init() {
		if ( !empty( $this->custom_post_type ) ) {
			add_action( 'init', array( $this, 'register_post_type' ) );
		}
		if ( !empty( $this->custom_post_dashboard ) ) {
			add_action( 'right_now_content_table_end', array( $this, 'right_now_content_table_end' ) );
		}
		if ( !empty( $this->custom_taxonomy ) ) {
			add_action( 'init', array( $this, 'register_taxonomy' ) );
		}
	}

	/**
	 * カスタム投稿タイプの登録
	 * http://codex.wordpress.org/Function_Reference/register_post_type
	 * @param   String  表示名
	 *	    String  スラッグ（登録名）
	 *	    Array   サポートタイプ
	 *	    Array   オプション項目
	 */
	public function custom_post_type( $name, $slug, Array $supports = array(), Array $options = array() ) {
		$custom_post_type = array(
			'name' => $name,
			'slug' => $slug,
			'supports' => $supports,
			'options' => $options
		);

		$this->custom_post_type[] = $custom_post_type;
		$this->custom_post_dashboard( $slug );
	}

	/**
	 * 多次元配列をマージ
	 * @param  Array  $args
	 * @param  Array  $override
	 * @return Array
	 */
	protected function array_merge( Array $args, Array $override ) {
		foreach ( $override as $key => $val ) {
			if ( isset( $args[$key] ) && is_array( $val ) ) {
				$args[$key] = $this->array_merge( $args[$key] , $val );
			} else {
				$args[$key] = $val;
			}
		}
		return $args;
	}

	/**
	 * カスタム登録タイプの登録を実行
	 */
	public function register_post_type() {
		foreach ( $this->custom_post_type as $cpt ) {
			if ( empty( $cpt['supports'] ) ) {
				$cpt['supports'] = array( 'title', 'editor' );
			}
			$labels = array(
				'name' => $cpt['name'],
				'singular_name' => $cpt['name'],
				'add_new_item' => $cpt['name'].'を追加',
				'add_new' => '新規追加',
				'new_item' => '新規追加',
				'edit_item' => $cpt['name'].'を編集',
				'view_item' => $cpt['name'].'を表示',
				'not_found' => $cpt['name'].'は見つかりませんでした',
				'not_found_in_trash' => 'ゴミ箱に'.$cpt['name'].'はありません。',
				'search_items' => $cpt['name'].'を検索',
			);
			$default_options = array(
				'public' => true,
				'has_archive' => true,
				'hierarchical' => false,
				'labels' => $labels,
				'menu_position' => 20,
				'supports' => $cpt['supports'],
				'rewrite' => array(
					'slug' => $cpt['slug'],
					'with_front' => false
				)
			);
			$args = $this->array_merge( $default_options, $cpt['options'] );
			// 関連するカスタムタクソノミーがある場合は配列に持たせる
			$_taxonomies = array();
			foreach ( $this->custom_taxonomy as $custom_taxonomy ) {
				$post_type = (is_array($custom_taxonomy['post_type'])) ? $custom_taxonomy['post_type'] : array($custom_taxonomy['post_type']);
				if ( in_array( $cpt['slug'], $post_type ) ) {
					$_taxonomies[] = $custom_taxonomy['slug'];
				}
			}
			if ( !empty( $_taxonomies ) ) {
				$args_taxonomies = array(
					'taxonomies' => $_taxonomies
				);
				$args = array_merge( $args, $args_taxonomies );
			}
			register_post_type( $cpt['slug'], $args );
		}
	}

	/**
	 * ダッシュボードに表示したいカスタム登録タイプを登録
	 */
	private function custom_post_dashboard( $custom_post_type ) {
		$this->custom_post_dashboard[] = $custom_post_type;
	}

	/**
	 * ダッシュボードにカスタム登録タイプの情報を表示
	 */
	public function right_now_content_table_end() {
		foreach ( $this->custom_post_dashboard as $custom_post_type ) {
			global $wp_post_types;
			$num_post_type = wp_count_posts( $custom_post_type );
			$num = number_format_i18n( $num_post_type->publish );
			$text = _n( $wp_post_types[$custom_post_type]->labels->singular_name, $wp_post_types[$custom_post_type]->labels->name, $num_post_type->publish );
			$capability = $wp_post_types[$custom_post_type]->cap->edit_posts;

			if ( current_user_can( $capability ) ) {
				$num = "<a href='edit.php?post_type=" . $custom_post_type . "'>$num</a>";
				$text = "<a href='edit.php?post_type=" . $custom_post_type . "'>$text</a>";
			}

			echo '<tr>';
			echo '<td class="first b b_' . $custom_post_type . '">' . $num . '</td>';
			echo '<td class="t ' . $custom_post_type . '">' . $text . '</td>';
			echo '</tr>';
		}
	}

	/**
	 * カスタムタクソノミーの登録
	 * http://codex.wordpress.org/Function_Reference/register_taxonomy
	 * @param   String  表示名
	 *	    String  スラッグ（登録名）
	 *	    Array   ポストタイプ
	 *	    Array   オプション項目
	 */
	public function custom_taxonomy( $name, $slug, $post_type, $options = array() ) {
		if(is_array($post_type)) {
			$post_type = array($post_type);
		}

		$custom_taxonomy = array(
			'name' => $name,
			'slug' => $slug,
			'post_type' => $post_type,
			'options' => $options
		);
		$this->custom_taxonomy[] = $custom_taxonomy;
	}

	/**
	 * カスタムタクソノミーの登録を実行
	 */
	public function register_taxonomy() {
		foreach ( $this->custom_taxonomy as $ct ) {
			$default_options = array(
				'hierarchical' => false,
				'public' => true,
				'rewrite' => array(
					'with_front' => false
				)
			);
			$ct['options'] = array_merge( $default_options, $ct['options'] );
			$ct['options']['label'] = $ct['name'];
			$ct['options']['singular_name'] = $ct['name'];
			register_taxonomy(
				$ct['slug'],
				$ct['post_type'],
				$ct['options']
			);
		}
	}
}

?>