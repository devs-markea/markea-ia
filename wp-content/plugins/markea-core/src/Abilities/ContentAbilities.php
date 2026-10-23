<?php
declare( strict_types=1 );

namespace Markea\Core\Abilities;

use Markea\Core\PostTypes;
use WP_Error;
use WP_Post;

defined( 'ABSPATH' ) || exit;

/**
 * Pages, posts, projects and strategists: list, read, create, edit, publish, trash and restore (never delete for good),
 * their ACF fields, taxonomy terms and Yoast SEO meta. Leads are personal data and stay out.
 */
final class ContentAbilities {

	private const STATUSES = array( 'draft', 'pending', 'publish', 'future', 'private' );

	/** @return array<int,string> */
	private function types(): array {
		$types = array( 'page', 'post', PostTypes::PROJECT, PostTypes::STRATEGIST );

		return array_values( array_filter( (array) apply_filters( 'markea_mcp_post_types', $types ), 'post_type_exists' ) );
	}

	public function register(): void {
		$type   = array( 'type' => 'string', 'enum' => $this->types(), 'description' => 'Tipo de contenido.' );
		$id     = array( 'type' => 'integer', 'minimum' => 1 );
		$fields = array( 'type' => 'object', 'description' => 'Campos ACF del contenido: nombre completo (ej. markea_project_tag) => valor. Solo valores simples e imágenes por ID.' );
		$terms  = array( 'type' => 'object', 'description' => 'Mapa taxonomía => lista de IDs o nombres/slugs de términos existentes. Reemplaza los términos de esa taxonomía.' );
		$body   = array(
			'title'          => array( 'type' => 'string' ),
			'content'        => array( 'type' => 'string', 'description' => 'HTML del editor (se filtra con wp_kses_post).' ),
			'excerpt'        => array( 'type' => 'string' ),
			'slug'           => array( 'type' => 'string' ),
			'status'         => array( 'type' => 'string', 'enum' => self::STATUSES, 'description' => 'publish y future requieren permiso de publicación; future necesita "date".' ),
			'date'           => array( 'type' => 'string', 'description' => 'Fecha ISO 8601 (para programar).' ),
			'parent'         => array( 'type' => 'integer', 'minimum' => 0 ),
			'menu_order'     => array( 'type' => 'integer' ),
			'featured_media' => array( 'type' => 'integer', 'minimum' => 0, 'description' => 'ID de imagen de la biblioteca; 0 la quita.' ),
			'terms'          => $terms,
			'fields'         => $fields,
		);

		Abilities::define(
			'markea/content-list',
			'read',
			array(
				'label'               => 'Listar contenido',
				'description'         => 'Lista páginas, entradas, proyectos o estrategas con filtros de estado y búsqueda.',
				'category'            => 'markea-content',
				'input_schema'        => Abilities::object_schema(
					array(
						'post_type' => $type,
						'status'    => array( 'type' => 'string', 'enum' => array_merge( array( 'any', 'trash' ), self::STATUSES ), 'default' => 'any' ),
						'search'    => array( 'type' => 'string' ),
						'per_page'  => array( 'type' => 'integer', 'minimum' => 1, 'maximum' => 50, 'default' => 20 ),
						'page'      => array( 'type' => 'integer', 'minimum' => 1, 'default' => 1 ),
					)
				),
				'output_schema'       => array( 'type' => 'object' ),
				'permission_callback' => function ( $input = array() ): bool {
					$obj = get_post_type_object( (string) ( ( (array) $input )['post_type'] ?? 'page' ) );

					return $obj && current_user_can( $obj->cap->edit_posts );
				},
				'execute_callback'    => function ( $input = array() ) {
					$input = (array) $input;
					$query = new \WP_Query(
						array(
							'post_type'      => (string) ( $input['post_type'] ?? 'page' ),
							'post_status'    => (string) ( $input['status'] ?? 'any' ),
							's'              => (string) ( $input['search'] ?? '' ),
							'posts_per_page' => (int) ( $input['per_page'] ?? 20 ),
							'paged'          => (int) ( $input['page'] ?? 1 ),
							'orderby'        => 'modified',
							'order'          => 'DESC',
						)
					);

					return array(
						'total' => (int) $query->found_posts,
						'items' => array_values( array_map( array( $this, 'summary' ), array_filter( $query->posts, static fn( $p ) => current_user_can( 'edit_post', $p->ID ) ) ) ),
					);
				},
			)
		);

		Abilities::define(
			'markea/content-get',
			'read',
			array(
				'label'               => 'Leer un contenido',
				'description'         => 'Devuelve título, contenido, extracto, estado, jerarquía, imagen destacada, términos y campos ACF de un contenido. Las secciones de la landing se leen con landing-get-section.',
				'category'            => 'markea-content',
				'input_schema'        => Abilities::object_schema( array( 'id' => $id ), array( 'id' ) ),
				'output_schema'       => array( 'type' => 'object' ),
				'permission_callback' => fn( $input = array() ): bool => $this->can_edit_post( (int) ( ( (array) $input )['id'] ?? 0 ) ),
				'execute_callback'    => function ( $input = array() ) {
					$post = get_post( (int) ( (array) $input )['id'] );

					return $post ? $this->detail( $post ) : new WP_Error( 'markea_not_found', 'No existe ese contenido.', array( 'status' => 404 ) );
				},
			)
		);

		Abilities::define(
			'markea/content-create',
			'create',
			array(
				'label'               => 'Crear contenido',
				'description'         => 'Crea una página, entrada, proyecto o estratega. Por defecto queda en borrador; publicar o programar requiere permiso de publicación.',
				'category'            => 'markea-content',
				'input_schema'        => Abilities::object_schema( array( 'post_type' => $type ) + $body, array( 'post_type', 'title' ) ),
				'output_schema'       => array( 'type' => 'object' ),
				'permission_callback' => function ( $input = array() ): bool {
					$obj = get_post_type_object( (string) ( ( (array) $input )['post_type'] ?? '' ) );

					return $obj && current_user_can( $obj->cap->create_posts );
				},
				'execute_callback'    => fn( $input = array() ) => $this->save( (array) $input, 0 ),
			)
		);

		Abilities::define(
			'markea/content-update',
			'write',
			array(
				'label'               => 'Editar contenido',
				'description'         => 'Cambia solo lo indicado de un contenido existente (texto, estado, fecha, jerarquía, imagen destacada, términos y campos ACF). Registra el valor anterior.',
				'category'            => 'markea-content',
				'input_schema'        => Abilities::object_schema( array( 'id' => $id ) + $body, array( 'id' ) ),
				'output_schema'       => array( 'type' => 'object' ),
				'permission_callback' => fn( $input = array() ): bool => $this->can_edit_post( (int) ( ( (array) $input )['id'] ?? 0 ) ),
				'execute_callback'    => fn( $input = array() ) => $this->save( (array) $input, (int) ( (array) $input )['id'] ),
			)
		);

		Abilities::define(
			'markea/content-trash',
			'write',
			array(
				'label'               => 'Mandar a la papelera',
				'description'         => 'Mueve un contenido a la papelera (recuperable con content-restore). No lo elimina. La página de inicio y la de entradas no se pueden mandar a la papelera.',
				'category'            => 'markea-content',
				'input_schema'        => Abilities::object_schema( array( 'id' => $id ), array( 'id' ) ),
				'output_schema'       => array( 'type' => 'object' ),
				'permission_callback' => fn( $input = array() ): bool => $this->can_trash( (int) ( ( (array) $input )['id'] ?? 0 ) ),
				'execute_callback'    => function ( $input = array() ) {
					$post_id = (int) ( (array) $input )['id'];
					if ( in_array( $post_id, array( (int) get_option( 'page_on_front' ), (int) get_option( 'page_for_posts' ) ), true ) ) {
						return new WP_Error( 'markea_protected', 'La página de inicio y la de entradas no se pueden mandar a la papelera.', array( 'status' => 400 ) );
					}

					$before = get_post_status( $post_id );
					$post   = wp_trash_post( $post_id );
					if ( ! $post ) {
						return new WP_Error( 'markea_trash_failed', 'No se pudo mandar a la papelera.', array( 'status' => 500 ) );
					}
					Audit::record( 'markea/content-trash', $this->label( $post_id ), array( array( 'field' => 'status', 'old' => $before, 'new' => 'trash' ) ) );

					return array( 'id' => $post_id, 'status' => 'trash' );
				},
			)
		);

		Abilities::define(
			'markea/content-restore',
			'write',
			array(
				'label'               => 'Restaurar de la papelera',
				'description'         => 'Saca un contenido de la papelera (vuelve como borrador).',
				'category'            => 'markea-content',
				'input_schema'        => Abilities::object_schema( array( 'id' => $id ), array( 'id' ) ),
				'output_schema'       => array( 'type' => 'object' ),
				'permission_callback' => fn( $input = array() ): bool => $this->can_trash( (int) ( ( (array) $input )['id'] ?? 0 ) ),
				'execute_callback'    => function ( $input = array() ) {
					$post_id = (int) ( (array) $input )['id'];
					if ( 'trash' !== get_post_status( $post_id ) ) {
						return new WP_Error( 'markea_not_trashed', 'Ese contenido no está en la papelera.', array( 'status' => 400 ) );
					}

					if ( ! wp_untrash_post( $post_id ) ) {
						return new WP_Error( 'markea_restore_failed', 'No se pudo restaurar.', array( 'status' => 500 ) );
					}
					$status = (string) get_post_status( $post_id );
					Audit::record( 'markea/content-restore', $this->label( $post_id ), array( array( 'field' => 'status', 'old' => 'trash', 'new' => $status ) ) );

					return array( 'id' => $post_id, 'status' => $status );
				},
			)
		);

		Abilities::define(
			'markea/terms-list',
			'read',
			array(
				'label'               => 'Listar términos',
				'description'         => 'Lista los términos (categorías, etiquetas, categoría de proyecto) para usar sus IDs al asignar contenido.',
				'category'            => 'markea-content',
				'input_schema'        => Abilities::object_schema( array( 'taxonomy' => array( 'type' => 'string', 'enum' => $this->taxonomies() ) ), array( 'taxonomy' ) ),
				'output_schema'       => array( 'type' => 'object' ),
				'permission_callback' => Abilities::can( 'edit_posts' ),
				'execute_callback'    => function ( $input = array() ) {
					$terms = get_terms( array( 'taxonomy' => (string) ( (array) $input )['taxonomy'], 'hide_empty' => false ) );

					return is_wp_error( $terms ) ? $terms : array( 'terms' => array_map( static fn( $t ) => array( 'id' => $t->term_id, 'name' => $t->name, 'slug' => $t->slug, 'count' => (int) $t->count ), $terms ) );
				},
			)
		);

		Abilities::define(
			'markea/seo-update',
			'write',
			array(
				'label'               => 'Editar SEO (Yoast)',
				'description'         => 'Cambia el título SEO, la meta descripción y la frase clave de un contenido. Requiere Yoast SEO activo.',
				'category'            => 'markea-content',
				'input_schema'        => Abilities::object_schema(
					array(
						'id'          => $id,
						'title'       => array( 'type' => 'string', 'description' => 'Título SEO.' ),
						'description' => array( 'type' => 'string', 'description' => 'Meta descripción.' ),
						'focus_kw'    => array( 'type' => 'string', 'description' => 'Frase clave.' ),
					),
					array( 'id' )
				),
				'output_schema'       => array( 'type' => 'object' ),
				'permission_callback' => fn( $input = array() ): bool => $this->can_edit_post( (int) ( ( (array) $input )['id'] ?? 0 ) ),
				'execute_callback'    => function ( $input = array() ) {
					$input = (array) $input;
					if ( ! defined( 'WPSEO_VERSION' ) ) {
						return new WP_Error( 'markea_no_yoast', 'Yoast SEO no está activo en este sitio.', array( 'status' => 400 ) );
					}

					$map     = array(
						'title'       => '_yoast_wpseo_title',
						'description' => '_yoast_wpseo_metadesc',
						'focus_kw'    => '_yoast_wpseo_focuskw',
					);
					$changes = array();
					foreach ( $map as $key => $meta ) {
						if ( ! array_key_exists( $key, $input ) ) {
							continue;
						}
						$old = (string) get_post_meta( (int) $input['id'], $meta, true );
						$new = sanitize_text_field( (string) $input[ $key ] );
						update_post_meta( (int) $input['id'], $meta, $new );
						$changes[] = array( 'field' => 'seo.' . $key, 'old' => $old, 'new' => $new );
					}
					if ( ! $changes ) {
						return new WP_Error( 'markea_nothing_to_do', 'No se indicó ningún campo SEO.', array( 'status' => 400 ) );
					}
					Audit::record( 'markea/seo-update', $this->label( (int) $input['id'] ), $changes );

					return array( 'id' => (int) $input['id'], 'updated' => array_column( $changes, 'field' ) );
				},
			)
		);
	}

	/** @return array<int,string> */
	private function taxonomies(): array {
		$out = array();
		foreach ( $this->types() as $type ) {
			$out = array_merge( $out, get_object_taxonomies( $type ) );
		}

		return array_values( array_unique( $out ) );
	}

	private function can_edit_post( int $id ): bool {
		$post = $id > 0 ? get_post( $id ) : null;

		return $post && in_array( $post->post_type, $this->types(), true ) && current_user_can( 'edit_post', $id );
	}

	private function can_trash( int $id ): bool {
		$post = $id > 0 ? get_post( $id ) : null;

		return $post && in_array( $post->post_type, $this->types(), true ) && current_user_can( 'delete_post', $id );
	}

	private function label( int $id ): string {
		return sprintf( '%s #%d "%s"', get_post_type( $id ), $id, get_the_title( $id ) );
	}

	/** @return array<string,mixed> */
	private function summary( WP_Post $post ): array {
		return array(
			'id'       => $post->ID,
			'type'     => $post->post_type,
			'title'    => get_the_title( $post ),
			'slug'     => $post->post_name,
			'status'   => $post->post_status,
			'modified' => gmdate( 'c', str_starts_with( $post->post_modified_gmt, '0000' ) ? (int) get_gmt_from_date( $post->post_modified, 'U' ) : (int) strtotime( $post->post_modified_gmt . ' UTC' ) ),
			'link'     => (string) get_permalink( $post ),
		);
	}

	/** @return array<string,mixed> */
	private function detail( WP_Post $post ): array {
		$terms = array();
		foreach ( get_object_taxonomies( $post->post_type ) as $taxonomy ) {
			$list = wp_get_object_terms( $post->ID, $taxonomy );
			if ( ! is_wp_error( $list ) && $list ) {
				$terms[ $taxonomy ] = array_map( static fn( $t ) => array( 'id' => $t->term_id, 'name' => $t->name ), $list );
			}
		}

		$fields = function_exists( 'get_fields' ) ? get_fields( $post->ID, false ) : array();
		$fields = is_array( $fields ) ? $fields : array();
		unset( $fields['markea_sections'] );

		return $this->summary( $post ) + array(
			'content'        => $post->post_content,
			'excerpt'        => $post->post_excerpt,
			'parent'         => (int) $post->post_parent,
			'menu_order'     => (int) $post->menu_order,
			'featured_media' => (int) get_post_thumbnail_id( $post ),
			'template'       => 'page' === $post->post_type ? (string) get_post_meta( $post->ID, '_wp_page_template', true ) : '',
			'terms'          => $terms,
			'fields'         => $fields,
		);
	}

	/** @return array<int,array<string,mixed>> ACF fields that apply to a post type or an existing post. */
	private function field_defs( string $type, int $post_id ): array {
		if ( ! function_exists( 'acf_get_field_groups' ) ) {
			return array();
		}

		$defs   = array();
		$groups = acf_get_field_groups( $post_id > 0 ? array( 'post_id' => $post_id ) : array( 'post_type' => $type ) );
		foreach ( $groups as $group ) {
			foreach ( (array) acf_get_fields( $group ) as $field ) {
				$defs[] = $field;
			}
		}

		return $defs;
	}

	/**
	 * Create (id 0) or edit. Everything is validated first; nothing is written when any value is rejected.
	 *
	 * @param array<string,mixed> $input
	 * @return array<string,mixed>|WP_Error
	 */
	private function save( array $input, int $id ) {
		$post = $id > 0 ? get_post( $id ) : null;
		$type = $post ? $post->post_type : (string) $input['post_type'];
		$obj  = get_post_type_object( $type );

		$args = array();
		if ( isset( $input['title'] ) ) {
			$args['post_title'] = sanitize_text_field( (string) $input['title'] );
		}
		if ( isset( $input['content'] ) ) {
			$args['post_content'] = wp_kses_post( (string) $input['content'] );
		}
		if ( isset( $input['excerpt'] ) ) {
			$args['post_excerpt'] = sanitize_textarea_field( (string) $input['excerpt'] );
		}
		if ( isset( $input['slug'] ) ) {
			$args['post_name'] = sanitize_title( (string) $input['slug'] );
		}
		if ( isset( $input['parent'] ) ) {
			$args['post_parent'] = (int) $input['parent'];
		}
		if ( isset( $input['menu_order'] ) ) {
			$args['menu_order'] = (int) $input['menu_order'];
		}

		$status = (string) ( $input['status'] ?? ( $post ? '' : 'draft' ) );
		if ( '' !== $status ) {
			if ( in_array( $status, array( 'publish', 'future', 'private' ), true ) && ! current_user_can( $obj->cap->publish_posts ) ) {
				return new WP_Error( 'markea_cannot_publish', 'Tu rol no puede publicar este tipo de contenido.', array( 'status' => 403 ) );
			}
			$args['post_status'] = $status;
		}

		if ( isset( $input['date'] ) ) {
			$stamp = strtotime( (string) $input['date'] );
			if ( false === $stamp ) {
				return new WP_Error( 'markea_bad_date', 'Fecha no válida (usa formato ISO 8601).', array( 'status' => 400 ) );
			}
			$args['post_date']     = wp_date( 'Y-m-d H:i:s', $stamp );
			$args['post_date_gmt'] = gmdate( 'Y-m-d H:i:s', $stamp );
			$args['edit_date']     = true; // Without it WordPress ignores the date on updates (and would publish a scheduled post at once).

		}
		if ( 'future' === $status && empty( $args['post_date'] ) ) {
			return new WP_Error( 'markea_bad_date', 'Para programar indica "date".', array( 'status' => 400 ) );
		}

		if ( ! $post && '' === ( $args['post_title'] ?? '' ) ) {
			return new WP_Error( 'markea_title_required', 'El título es obligatorio.', array( 'status' => 400 ) );
		}

		$media = null;
		if ( isset( $input['featured_media'] ) ) {
			$media = (int) $input['featured_media'];
			if ( $media > 0 && ! wp_attachment_is_image( $media ) ) {
				return new WP_Error( 'markea_bad_media', sprintf( 'El ID %d no es una imagen de la biblioteca.', $media ), array( 'status' => 400 ) );
			}
		}

		$term_sets = array();
		foreach ( (array) ( $input['terms'] ?? array() ) as $taxonomy => $list ) {
			$resolved = $this->resolve_terms( $type, (string) $taxonomy, (array) $list );
			if ( is_wp_error( $resolved ) ) {
				return $resolved;
			}
			$term_sets[ (string) $taxonomy ] = $resolved;
		}

		$field_values = array();
		if ( ! empty( $input['fields'] ) ) {
			$defs = $this->field_defs( $type, $id );
			foreach ( (array) $input['fields'] as $name => $value ) {
				$field = FieldAccess::find( $defs, (string) $name );
				if ( ! $field || 'markea_sections' === $name ) {
					return new WP_Error( 'markea_unknown_field', sprintf( 'El campo "%s" no existe para este contenido.', $name ), array( 'status' => 400 ) );
				}
				$clean = FieldAccess::sanitize( $field, $value );
				if ( is_wp_error( $clean ) ) {
					return $clean;
				}
				$field_values[ (string) $name ] = $clean;
			}
		}

		// Everything checked: write.
		$changes = array();
		if ( $post ) {
			foreach ( $args as $key => $new ) {
				if ( 'edit_date' !== $key && ! str_starts_with( $key, 'post_date' ) && (string) $post->$key !== (string) $new ) {
					$changes[] = array( 'field' => $key, 'old' => $post->$key, 'new' => $new );
				}
			}
			$result = wp_update_post( wp_slash( array_merge( $args, array( 'ID' => $id ) ) ), true );
		} else {
			$result    = wp_insert_post( wp_slash( array_merge( $args, array( 'post_type' => $type ) ) ), true );
			$changes[] = array( 'field' => 'creado', 'old' => '', 'new' => $args['post_title'] ?? '' );
		}
		if ( is_wp_error( $result ) ) {
			return $result;
		}
		$id = (int) $result;

		if ( null !== $media ) {
			$old = (int) get_post_thumbnail_id( $id );
			$media > 0 ? set_post_thumbnail( $id, $media ) : delete_post_thumbnail( $id );
			$changes[] = array( 'field' => 'featured_media', 'old' => $old, 'new' => $media );
		}
		foreach ( $term_sets as $taxonomy => $ids ) {
			$old = wp_get_object_terms( $id, $taxonomy, array( 'fields' => 'ids' ) );
			wp_set_object_terms( $id, $ids, $taxonomy );
			$changes[] = array( 'field' => 'terms.' . $taxonomy, 'old' => $old, 'new' => $ids );
		}
		foreach ( $field_values as $name => $value ) {
			$changes[] = array( 'field' => $name, 'old' => get_field( $name, $id, false ), 'new' => $value );
			update_field( $name, $value, $id );
		}

		Audit::record( $post ? 'markea/content-update' : 'markea/content-create', $this->label( $id ), $changes );

		return $this->summary( get_post( $id ) );
	}

	/**
	 * @param array<int,mixed> $list
	 * @return array<int,int>|WP_Error
	 */
	private function resolve_terms( string $type, string $taxonomy, array $list ) {
		if ( ! taxonomy_exists( $taxonomy ) || ! is_object_in_taxonomy( $type, $taxonomy ) ) {
			return new WP_Error( 'markea_bad_taxonomy', sprintf( 'La taxonomía "%s" no aplica a "%s".', $taxonomy, $type ), array( 'status' => 400 ) );
		}
		if ( ! current_user_can( get_taxonomy( $taxonomy )->cap->assign_terms ) ) {
			return new WP_Error( 'markea_forbidden', 'Tu rol no puede asignar esos términos.', array( 'status' => 403 ) );
		}

		$ids = array();
		foreach ( $list as $item ) {
			$term = is_numeric( $item ) ? get_term( (int) $item, $taxonomy ) : ( get_term_by( 'name', (string) $item, $taxonomy ) ?: get_term_by( 'slug', (string) $item, $taxonomy ) );
			if ( ! $term || is_wp_error( $term ) ) {
				return new WP_Error( 'markea_unknown_term', sprintf( 'El término "%s" no existe en "%s" (usa terms-list).', $item, $taxonomy ), array( 'status' => 400 ) );
			}
			$ids[] = (int) $term->term_id;
		}

		return $ids;
	}
}
