<?php
declare( strict_types=1 );

namespace Markea\Core\Abilities;

use WP_Error;

defined( 'ABSPATH' ) || exit;

/**
 * Media library: list, add from a public https URL and edit alt text, title and caption. No deletion.
 * Images, mp4/webm video and PDF only (no SVG: it can carry scripts).
 */
final class MediaAbilities {

	private const MAX_BYTES = 25 * MB_IN_BYTES;
	private const MIMES     = array(
		'jpg|jpeg|jpe' => 'image/jpeg',
		'png'          => 'image/png',
		'gif'          => 'image/gif',
		'webp'         => 'image/webp',
		'avif'         => 'image/avif',
		'mp4'          => 'video/mp4',
		'webm'         => 'video/webm',
		'pdf'          => 'application/pdf',
	);

	public function register(): void {
		$id = array( 'type' => 'integer', 'minimum' => 1 );

		Abilities::define(
			'markea/media-list',
			'read',
			array(
				'label'               => 'Listar medios',
				'description'         => 'Lista archivos de la biblioteca de medios con su ID (el ID se usa en campos de imagen e imagen destacada).',
				'category'            => 'markea-media',
				'input_schema'        => Abilities::object_schema(
					array(
						'search'   => array( 'type' => 'string' ),
						'type'     => array( 'type' => 'string', 'enum' => array( 'image', 'video', 'application' ), 'description' => 'Familia de MIME.' ),
						'per_page' => array( 'type' => 'integer', 'minimum' => 1, 'maximum' => 50, 'default' => 20 ),
						'page'     => array( 'type' => 'integer', 'minimum' => 1, 'default' => 1 ),
					)
				),
				'output_schema'       => array( 'type' => 'object' ),
				'permission_callback' => Abilities::can( 'upload_files' ),
				'execute_callback'    => function ( $input = array() ) {
					$input = (array) $input;
					$query = new \WP_Query(
						array(
							'post_type'      => 'attachment',
							'post_status'    => 'inherit',
							's'              => (string) ( $input['search'] ?? '' ),
							'post_mime_type' => (string) ( $input['type'] ?? '' ),
							'posts_per_page' => (int) ( $input['per_page'] ?? 20 ),
							'paged'          => (int) ( $input['page'] ?? 1 ),
							'orderby'        => 'date',
							'order'          => 'DESC',
						)
					);

					return array(
						'total' => (int) $query->found_posts,
						'items' => array_map( array( $this, 'describe' ), $query->posts ),
					);
				},
			)
		);

		Abilities::define(
			'markea/media-upload-from-url',
			'create',
			array(
				'label'               => 'Subir un medio desde una URL',
				'description'         => 'Descarga un archivo de una URL pública https (imagen jpg/png/gif/webp/avif, video mp4/webm o PDF, máximo 25 MB) y lo guarda en la biblioteca. Devuelve el ID.',
				'category'            => 'markea-media',
				'input_schema'        => Abilities::object_schema(
					array(
						'url'     => array( 'type' => 'string', 'description' => 'URL https pública del archivo.' ),
						'title'   => array( 'type' => 'string' ),
						'alt'     => array( 'type' => 'string', 'description' => 'Texto alternativo (imágenes).' ),
						'caption' => array( 'type' => 'string' ),
					),
					array( 'url' )
				),
				'output_schema'       => array( 'type' => 'object' ),
				'permission_callback' => Abilities::can( 'upload_files' ),
				'execute_callback'    => fn( $input = array() ) => $this->upload( (array) $input ),
			)
		);

		Abilities::define(
			'markea/media-update',
			'write',
			array(
				'label'               => 'Editar datos de un medio',
				'description'         => 'Cambia el texto alternativo, el título o el pie de un archivo de la biblioteca.',
				'category'            => 'markea-media',
				'input_schema'        => Abilities::object_schema(
					array(
						'id'      => $id,
						'alt'     => array( 'type' => 'string' ),
						'title'   => array( 'type' => 'string' ),
						'caption' => array( 'type' => 'string' ),
					),
					array( 'id' )
				),
				'output_schema'       => array( 'type' => 'object' ),
				'permission_callback' => function ( $input = array() ): bool {
					$media = (int) ( ( (array) $input )['id'] ?? 0 );

					return $media > 0 && 'attachment' === get_post_type( $media ) && current_user_can( 'edit_post', $media );
				},
				'execute_callback'    => function ( $input = array() ) {
					$input   = (array) $input;
					$media   = (int) $input['id'];
					$post    = get_post( $media );
					$changes = array();

					if ( array_key_exists( 'alt', $input ) ) {
						$old = (string) get_post_meta( $media, '_wp_attachment_image_alt', true );
						$new = sanitize_text_field( (string) $input['alt'] );
						update_post_meta( $media, '_wp_attachment_image_alt', $new );
						$changes[] = array( 'field' => 'alt', 'old' => $old, 'new' => $new );
					}

					$update = array( 'ID' => $media );
					if ( array_key_exists( 'title', $input ) ) {
						$update['post_title'] = sanitize_text_field( (string) $input['title'] );
						$changes[]            = array( 'field' => 'title', 'old' => $post->post_title, 'new' => $update['post_title'] );
					}
					if ( array_key_exists( 'caption', $input ) ) {
						$update['post_excerpt'] = sanitize_textarea_field( (string) $input['caption'] );
						$changes[]              = array( 'field' => 'caption', 'old' => $post->post_excerpt, 'new' => $update['post_excerpt'] );
					}
					if ( count( $update ) > 1 ) {
						wp_update_post( wp_slash( $update ) );
					}

					if ( ! $changes ) {
						return new WP_Error( 'markea_nothing_to_do', 'No se indicó ningún dato que cambiar.', array( 'status' => 400 ) );
					}
					Audit::record( 'markea/media-update', sprintf( 'medio #%d', $media ), $changes );

					return $this->describe( get_post( $media ) );
				},
			)
		);
	}

	/** @return array<string,mixed> */
	private function describe( \WP_Post $post ): array {
		$meta = wp_get_attachment_metadata( $post->ID );

		return array(
			'id'      => $post->ID,
			'title'   => $post->post_title,
			'alt'     => (string) get_post_meta( $post->ID, '_wp_attachment_image_alt', true ),
			'caption' => $post->post_excerpt,
			'mime'    => $post->post_mime_type,
			'url'     => (string) wp_get_attachment_url( $post->ID ),
			'width'   => (int) ( $meta['width'] ?? 0 ),
			'height'  => (int) ( $meta['height'] ?? 0 ),
		);
	}

	/** @param array<string,mixed> $input @return array<string,mixed>|WP_Error */
	private function upload( array $input ) {
		$url = esc_url_raw( (string) $input['url'], array( 'https' ) );
		if ( '' === $url ) {
			return new WP_Error( 'markea_bad_url', 'La URL debe ser https y válida.', array( 'status' => 400 ) );
		}

		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		// download_url() uses the safe HTTP API: private and loopback addresses are refused.
		$tmp = download_url( $url, 60 );
		if ( is_wp_error( $tmp ) ) {
			return new WP_Error( 'markea_download_failed', 'No se pudo descargar el archivo: ' . $tmp->get_error_message(), array( 'status' => 400 ) );
		}

		$size = (int) filesize( $tmp );
		$name = sanitize_file_name( wp_basename( (string) wp_parse_url( $url, PHP_URL_PATH ) ) );
		$type = wp_check_filetype_and_ext( $tmp, $name, self::MIMES );
		if ( $size <= 0 || $size > self::MAX_BYTES || empty( $type['type'] ) ) {
			wp_delete_file( $tmp );

			return new WP_Error( 'markea_bad_file', 'Archivo no permitido: solo imágenes, mp4/webm o PDF de hasta 25 MB, con extensión correcta.', array( 'status' => 400 ) );
		}
		if ( ! empty( $type['proper_filename'] ) ) {
			$name = $type['proper_filename'];
		}

		$id = media_handle_sideload( array( 'name' => $name, 'tmp_name' => $tmp ), 0, isset( $input['title'] ) ? sanitize_text_field( (string) $input['title'] ) : null );
		if ( is_wp_error( $id ) ) {
			wp_delete_file( $tmp );

			return $id;
		}

		if ( isset( $input['alt'] ) ) {
			update_post_meta( $id, '_wp_attachment_image_alt', sanitize_text_field( (string) $input['alt'] ) );
		}
		if ( isset( $input['caption'] ) ) {
			wp_update_post( wp_slash( array( 'ID' => $id, 'post_excerpt' => sanitize_textarea_field( (string) $input['caption'] ) ) ) );
		}

		Audit::record( 'markea/media-upload-from-url', sprintf( 'medio #%d', $id ), array( array( 'field' => 'origen', 'old' => '', 'new' => $url ) ) );

		return $this->describe( get_post( (int) $id ) );
	}
}
