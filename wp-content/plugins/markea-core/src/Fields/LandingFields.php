<?php
declare( strict_types=1 );

namespace Markea\Core\Fields;

defined( 'ABSPATH' ) || exit;

/**
 * Field group for the landing page (template templates/landing.php), one tab per section.
 */
final class LandingFields {

	public const TEMPLATE = 'templates/landing.php';

	public function register(): void {
		acf_add_local_field_group(
			array(
				'key'                   => 'group_markea_landing',
				'title'                 => 'Contenido de la landing',
				'fields'                => $this->fields(),
				'location'              => array( array( array( 'param' => 'page_template', 'operator' => '==', 'value' => self::TEMPLATE ) ) ),
				'hide_on_screen'        => array( 'the_content', 'excerpt', 'discussion', 'comments', 'slug', 'author', 'format', 'categories', 'tags', 'send-trackbacks' ),
				'label_placement'       => 'top',
				'instruction_placement' => 'field',
			)
		);
	}

	/** @return array<int,array<string,mixed>> */
	private function fields(): array {
		$title_text = static fn( string $label ): array => array(
			F::sub( 'text', 'title', 'Título' ),
			F::sub( 'textarea', 'text', 'Texto', array( 'rows' => 3 ) ),
		);

		return array(
			F::tab( 'hero', '1. Hero' ),
			F::text( 'hero_title', 'Título', array( 'instructions' => 'Titular principal.' ) ),
			F::text( 'hero_highlight', 'Parte resaltada', array( 'instructions' => 'Fragmento exacto del título que se muestra resaltado.' ) ),
			F::textarea( 'hero_subtitle', 'Subtítulo', array( 'rows' => 3 ) ),
			F::text( 'hero_cta_label', 'Texto del botón (CTA)', array( 'instructions' => 'Se usa en todos los botones "Hablemos" del sitio.' ) ),
			F::text( 'hero_cta_url', 'Enlace del botón', array( 'default_value' => '#contacto' ) ),

			F::tab( 'problem', '2. El problema' ),
			F::text( 'problem_title', 'Título de la sección' ),
			F::repeater( 'problem_items', 'Cards', $title_text( '' ), array( 'max' => 6, 'button_label' => 'Añadir card' ) ),

			F::tab( 'about', '3. Quiénes somos' ),
			F::text( 'about_years', 'Dato destacado', array( 'instructions' => 'Ej. "8 años".' ) ),
			F::text( 'about_title', 'Título' ),
			F::wysiwyg( 'about_text', 'Texto' ),
			F::repeater( 'about_blocks', 'Bloques', $title_text( '' ), array( 'max' => 4, 'button_label' => 'Añadir bloque' ) ),

			F::tab( 'units', '4. Unidades de negocio' ),
			F::text( 'units_title', 'Título' ),
			F::repeater(
				'units_items',
				'Unidades',
				array(
					F::sub( 'text', 'name', 'Nombre' ),
					F::sub( 'image', 'logo', 'Logo', array( 'return_format' => 'id', 'preview_size' => 'thumbnail' ) ),
					F::sub( 'url', 'url', 'Enlace (opcional)' ),
				),
				array( 'button_label' => 'Añadir unidad' )
			),

			F::tab( 'case', '5. Caso de operación propia' ),
			F::text( 'case_eyebrow', 'Etiqueta superior' ),
			F::text( 'case_title', 'Título' ),
			F::wysiwyg( 'case_text', 'Texto' ),

			F::tab( 'mos', '6. Marketing Operating System' ),
			F::text( 'mos_title', 'Título' ),
			F::repeater( 'mos_steps', 'Pasos', $title_text( '' ), array( 'max' => 8, 'button_label' => 'Añadir paso' ) ),

			F::tab( 'offer', '7. Oferta diferenciada' ),
			F::text( 'offer_title', 'Título' ),
			F::repeater(
				'offer_items',
				'Cards',
				array(
					F::sub( 'text', 'title', 'Título' ),
					F::sub( 'textarea', 'text', 'Texto', array( 'rows' => 3 ) ),
					F::sub( 'text', 'tags', 'Tags', array( 'instructions' => 'Separados por coma.' ) ),
				),
				array( 'button_label' => 'Añadir card' )
			),

			F::tab( 'projects', '8. Proyectos' ),
			F::text( 'projects_title', 'Título' ),
			F::textarea( 'projects_text', 'Texto', array( 'instructions' => 'Los proyectos y sus filtros se administran en el menú Proyectos.' ) ),

			F::tab( 'tools', '9. Herramientas' ),
			F::text( 'tools_title', 'Título' ),
			F::gallery( 'tools_gallery', 'Logos de herramientas' ),

			F::tab( 'relevance', '10. Relevancia sectorial' ),
			F::text( 'relevance_title', 'Título' ),
			F::textarea( 'relevance_text', 'Texto' ),
			F::repeater( 'relevance_points', 'Puntos', array( F::sub( 'text', 'text', 'Punto' ) ), array( 'layout' => 'table', 'button_label' => 'Añadir punto' ) ),

			F::tab( 'how', '11. Cómo trabajamos' ),
			F::text( 'how_title', 'Título' ),
			F::repeater( 'how_items', 'Líneas de servicio', $title_text( '' ), array( 'max' => 6, 'button_label' => 'Añadir línea' ) ),

			F::tab( 'next', '12. Próximos pasos' ),
			F::text( 'next_title', 'Título' ),
			F::repeater( 'next_steps', 'Pasos', $title_text( '' ), array( 'max' => 6, 'button_label' => 'Añadir paso' ) ),
			F::url( 'next_video_url', 'URL del video', array( 'instructions' => 'YouTube o Vimeo. Se carga al hacer clic en reproducir.' ) ),

			F::tab( 'strategists', '13. Estrategas' ),
			F::text( 'strategists_title', 'Título' ),
			F::textarea( 'strategists_text', 'Texto', array( 'instructions' => 'Las personas se administran en el menú Estrategas.' ) ),

			F::tab( 'form', '14. Formulario' ),
			F::text( 'form_title', 'Título' ),
			F::textarea( 'form_text', 'Texto' ),
			F::textarea( 'form_interests', 'Opciones de "Me interesa"', array( 'rows' => 6, 'instructions' => 'Una opción por línea.' ) ),
		);
	}
}
