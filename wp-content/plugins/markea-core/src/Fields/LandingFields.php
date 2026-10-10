<?php
declare( strict_types=1 );

namespace Markea\Core\Fields;

defined( 'ABSPATH' ) || exit;

/**
 * Landing content as ONE Flexible Content field (markea_sections). Each section is a layout the team can
 * add, remove and reorder on the page that uses templates/landing.php.
 *
 * Layout names map to theme partials: next_steps => template-parts/home/section-next-steps.php.
 */
final class LandingFields {

	public const TEMPLATE = 'templates/landing.php';

	/** Layout names the theme is allowed to render. */
	public const LAYOUTS = array( 'hero', 'problem', 'about', 'units', 'case', 'mos', 'offer', 'projects', 'tools', 'relevance', 'how', 'next_steps', 'strategists', 'form' );

	public function register(): void {
		acf_add_local_field_group(
			array(
				'key'            => 'group_markea_landing',
				'title'          => 'Contenido de la landing',
				'fields'         => array(
					F::field(
						'flexible_content',
						'sections',
						'Secciones',
						array(
							'instructions' => 'Añade, quita y reordena las secciones. Las secciones sin contenido no se muestran.',
							'button_label' => 'Añadir sección',
							'layouts'      => $this->layouts(),
						)
					),
				),
				'location'       => array( array( array( 'param' => 'page_template', 'operator' => '==', 'value' => self::TEMPLATE ) ) ),
				'hide_on_screen' => array( 'the_content', 'excerpt', 'discussion', 'comments', 'slug', 'author', 'format', 'categories', 'tags', 'send-trackbacks' ),
			)
		);
	}

	/** @return array<int,array<string,mixed>> */
	private function layouts(): array {
		$title = F::sub( 'text', 'title', 'Título' );
		$cards = static fn( string $label ): array => F::items(
			'items',
			$label,
			array(
				F::sub( 'text', 'title', 'Título' ),
				F::sub( 'textarea', 'text', 'Texto', array( 'rows' => 3, 'new_lines' => '' ) ),
			),
			array( 'button_label' => 'Añadir fila' )
		);

		return array(
			F::layout(
				'hero',
				'Hero',
				array(
					F::sub( 'text', 'title', 'Título' ),
					F::sub( 'text', 'highlight', 'Parte en negrita', array( 'instructions' => 'Texto que va en negrita después del título (o fragmento exacto del título).' ) ),
					F::sub( 'textarea', 'subtitle', 'Subtítulo', array( 'rows' => 3, 'new_lines' => '' ) ),
					F::sub( 'text', 'cta_label', 'Texto del botón (CTA)', array( 'instructions' => 'Se usa en todos los botones "Hablemos" del sitio.' ) ),
					F::sub( 'text', 'cta_url', 'Enlace del botón', array( 'default_value' => '#contacto' ) ),
					F::sub( 'url', 'embed_url', 'Visual interactivo (URL)', array( 'instructions' => 'Escena de Spline (URL que termina en .splinecode) o la URL de un iframe. Se muestra en el círculo de la derecha y se carga después de que la página termina de cargar.' ) ),
					F::sub( 'true_false', 'embed_hide_badge', 'Ocultar la insignia de Spline', array( 'ui' => 1, 'default_value' => 1, 'instructions' => 'Solo aplica a escenas de Spline.' ) ),
					F::sub( 'text', 'embed_title', 'Descripción del visual', array( 'instructions' => 'Texto para lectores de pantalla. Ej. "Cubo 3D interactivo".' ) ),
				)
			),
			F::layout(
				'problem',
				'El problema',
				array(
					$title,
					F::items(
						'items',
						'Cards',
						array(
							F::sub( 'select', 'icon', 'Ícono', array( 'choices' => array( '' => 'Sin ícono', 'clock' => 'Reloj', 'user-check' => 'Usuario', 'activity' => 'Actividad' ), 'default_value' => '', 'allow_null' => 0 ) ),
							F::sub( 'text', 'title', 'Título' ),
							F::sub( 'textarea', 'text', 'Texto', array( 'rows' => 3, 'new_lines' => '' ) ),
						),
						array( 'max' => 6, 'button_label' => 'Añadir card' )
					),
				)
			),
			F::layout(
				'about',
				'Quiénes somos',
				array(
					F::sub( 'text', 'years', 'Dato destacado', array( 'instructions' => 'Ej. "8 años".' ) ),
					$title,
					F::sub( 'wysiwyg', 'text', 'Texto', array( 'media_upload' => 0, 'toolbar' => 'basic' ) ),
					F::sub( 'textarea', 'panel_text', 'Texto del panel', array( 'rows' => 3, 'new_lines' => '', 'instructions' => 'Frase sobre los bloques (panel oscuro).' ) ),
					F::sub( 'image', 'panel_image', 'Imagen del panel', array( 'return_format' => 'id', 'preview_size' => 'medium', 'instructions' => 'Foto inclinada en la esquina del panel oscuro. Si queda vacía se usa la imagen del diseño. Recomendado: horizontal, mínimo 1000 px de ancho.' ) ),
					$cards( 'Bloques' ),
				)
			),
			F::layout(
				'units',
				'Unidades de negocio',
				array(
					$title,
					F::sub( 'textarea', 'text', 'Texto', array( 'rows' => 3, 'new_lines' => '' ) ),
					F::items(
						'items',
						'Unidades',
						array(
							F::sub( 'text', 'name', 'Nombre' ),
							F::sub( 'image', 'logo', 'Imagen de la tarjeta', array( 'return_format' => 'id', 'preview_size' => 'medium', 'instructions' => 'Tarjeta que se muestra al elegir la unidad (aprox. 420 x 410 px). Si queda vacía se muestra el nombre.' ) ),
							F::sub( 'color_picker', 'color', 'Color de la tarjeta de fondo', array( 'default_value' => '#D5AA53', 'enable_opacity' => 0, 'return_format' => 'string' ) ),
							F::sub( 'url', 'url', 'Enlace (opcional)' ),
						),
						array( 'button_label' => 'Añadir unidad' )
					),
				)
			),
			F::layout(
				'case',
				'Caso de operación propia',
				array(
					F::sub( 'text', 'kicker', 'Etiqueta destacada', array( 'instructions' => 'Ej. "LA PRUEBA DE FUEGO".' ) ),
					F::sub( 'text', 'subtitle', 'Subtítulo' ),
					$title,
					F::sub( 'wysiwyg', 'text', 'Texto', array( 'media_upload' => 0, 'toolbar' => 'basic' ) ),
				)
			),
			F::layout( 'mos', 'Marketing Operating System', array( $title, F::sub( 'textarea', 'text', 'Texto', array( 'rows' => 3, 'new_lines' => '' ) ), $cards( 'Pilares' ) ) ),
			F::layout(
				'offer',
				'Oferta diferenciada',
				array(
					$title,
					F::sub( 'textarea', 'text', 'Texto', array( 'rows' => 3, 'new_lines' => '' ) ),
					F::items(
						'items',
						'Cards',
						array(
							F::sub( 'text', 'title', 'Título' ),
							F::sub( 'textarea', 'text', 'Texto', array( 'rows' => 3, 'new_lines' => '' ) ),
							F::sub( 'text', 'tags', 'Tags', array( 'instructions' => 'Separados por coma.' ) ),
						),
						array( 'button_label' => 'Añadir card' )
					),
				)
			),
			F::layout(
				'projects',
				'Proyectos',
				array(
					$title,
					F::sub( 'textarea', 'text', 'Texto', array( 'rows' => 3, 'new_lines' => '', 'instructions' => 'Los proyectos y sus filtros se administran en el menú Proyectos.' ) ),
				)
			),
			F::layout(
				'tools',
				'Herramientas',
				array(
					$title,
					F::items(
						'items',
						'Herramientas',
						array(
							F::sub( 'text', 'name', 'Nombre' ),
							F::sub( 'image', 'logo', 'Logo', array( 'return_format' => 'id', 'preview_size' => 'thumbnail' ) ),
						),
						array( 'button_label' => 'Añadir herramienta' )
					),
				)
			),
			F::layout(
				'relevance',
				'Relevancia sectorial',
				array(
					$title,
					F::sub( 'textarea', 'text', 'Texto', array( 'rows' => 3, 'new_lines' => '' ) ),
					$cards( 'Puntos' ),
				)
			),
			F::layout( 'how', 'Cómo trabajamos', array( $title, F::sub( 'textarea', 'text', 'Texto', array( 'rows' => 3, 'new_lines' => '' ) ), $cards( 'Líneas de servicio' ) ) ),
			F::layout(
				'next_steps',
				'Próximos pasos',
				array(
					$title,
					F::sub( 'textarea', 'text', 'Texto', array( 'rows' => 3, 'new_lines' => '' ) ),
					$cards( 'Pasos' ),
					F::sub( 'text', 'tagline', 'Frase de cierre', array( 'instructions' => 'Ej. "Start. Grow. Succeed".' ) ),
					F::sub( 'url', 'video_url', 'URL del video', array( 'instructions' => 'YouTube o Vimeo. Se carga al hacer clic en reproducir.' ) ),
				)
			),
			F::layout(
				'strategists',
				'Estrategas',
				array(
					$title,
					F::sub( 'textarea', 'text', 'Texto', array( 'rows' => 3, 'new_lines' => '', 'instructions' => 'Las personas se administran en el menú Estrategas.' ) ),
				)
			),
			F::layout(
				'form',
				'Formulario',
				array(
					$title,
					F::sub( 'textarea', 'text', 'Texto', array( 'rows' => 3, 'new_lines' => '' ) ),
					F::sub( 'textarea', 'interests', 'Opciones de "Me interesa"', array( 'rows' => 6, 'new_lines' => '', 'instructions' => 'Una opción por línea.' ) ),
				)
			),
		);
	}
}
