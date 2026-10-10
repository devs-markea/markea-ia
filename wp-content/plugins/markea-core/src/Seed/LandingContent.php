<?php
declare( strict_types=1 );

namespace Markea\Core\Seed;

defined( 'ABSPATH' ) || exit;

/**
 * Landing copy taken from the Figma file "Propuestas de Diseños - Markea IA"
 * (frame "Markea IA Homepage - Type A Future v2", visible layers only).
 *
 * Deliberate differences from the Figma text:
 * - Units: "3 unidades de negocio activas" (the team confirmed 3, the design says 5).
 * - Missing dashes in a few sentences are restored.
 */
final class LandingContent {

	/**
	 * Flexible Content rows, in page order.
	 *
	 * @return array<int,array<string,mixed>>
	 */
	public static function sections(): array {
		return array(
			array(
				'acf_fc_layout' => 'hero',
				'title'         => 'Tu empresa no necesita hacer más.',
				'highlight'     => 'Necesita operar mejor.',
				'subtitle'      => 'Acompañamos la adopción de IA en tu empresa, identificando oportunidades y desarrollando las herramientas, automatizaciones y sistemas que tu operación realmente necesita.',
				'cta_label'     => 'Hablemos',
				'cta_url'       => '#contacto',
			),
			array(
				'acf_fc_layout' => 'problem',
				'eyebrow'       => 'El problema',
				'title'         => 'La mayoría de las agencias venden estrategia que nunca han operado',
				'items'         => array(
					array( 'icon' => 'clock', 'title' => 'Ciclos de venta largos', 'text' => 'Hospitalidad, turismo y real estate deciden en meses, no en clics — cada error de estrategia se paga caro.' ),
					array( 'icon' => 'user-check', 'title' => 'Cliente de alto valor', 'text' => 'El comprador o huésped premium exige una narrativa consistente, no campañas genéricas de volumen.' ),
					array( 'icon' => 'activity', 'title' => 'Consultoría sin operación', 'text' => 'Muchas agencias recomiendan desde afuera, sin haber sostenido nunca una marca propia en el mercado.' ),
				),
			),
			array(
				'acf_fc_layout' => 'about',
				'eyebrow'       => 'QUIÉNES SOMOS',
				'years'         => '8 años.',
				'title'         => 'Una agencia incubadora de negocios',
				'text'          => '<p>No es solo un proveedor externo, es el motor de un grupo que construye y opera marcas propias. Eso cambia la forma en que pensamos cada estrategia: la pensamos como dueños, no como consultores.</p>',
				'panel_text'    => 'Operamos nuestras propias marcas y usamos ese aprendizaje para impulsar las de nuestros clientes.',
				'items'         => array(
					array( 'title' => 'Marcas propias', 'text' => '' ),
					array( 'title' => 'Clientes externos', 'text' => '' ),
				),
			),
			array(
				'acf_fc_layout' => 'units',
				'eyebrow'       => 'UNIDADES DE NEGOCIO',
				'title'         => '3 unidades de negocio activas',
				'text'          => 'Hospitality, real estate, tecnología y más — cada vertical operada con su propio sistema de marketing.',
				'items'         => array(
					array( 'name' => 'Hospitality' ),
					array( 'name' => 'Real estate' ),
					array( 'name' => 'Tecnología y más' ),
				),
			),
			array(
				'acf_fc_layout' => 'case',
				'eyebrow'       => 'CASO DE OPERACIÓN PROPIA',
				'kicker'        => 'LA PRUEBA DE FUEGO',
				'subtitle'      => 'Real estate de ultra-lujo',
				'title'         => 'No somos teoría.',
				'text'          => 'Operamos una firma de real estate ultra-lujo y una marca de F&B en formato dark kitchen. Aplicamos exactamente lo que ofrecemos — posicionamiento, CRM, contenido, ventas con presupuesto y consecuencias reales.',
				'items'         => array(
					array( 'text' => 'No es un caso de estudio ajeno: es una operación en vivo, hoy.' ),
					array( 'text' => 'Cada decisión de marketing la sostenemos con nuestro propio riesgo.' ),
					array( 'text' => 'El mismo sistema que usamos ahí es el que ponemos a tu servicio.' ),
				),
				'tone'          => 'dark',
			),
			array(
				'acf_fc_layout' => 'mos',
				'title'         => 'Un Marketing Operating System real',
				'emphasis'      => 'Marketing Operating System',
				'text'          => 'Esto es lo que construimos para operar nuestras propias marcas — la prueba de que sabemos diseñar sistemas, no solo usarlos.',
				'items'         => array(
					array( 'title' => 'Dashboard interactivo', 'text' => 'Dashboard interactivos para el control total y métricas del sistema operando.' ),
					array( 'title' => 'Playbooks por unidad', 'text' => '' ),
					array( 'title' => 'Checklists operativos', 'text' => '' ),
					array( 'title' => 'Plan de desarrollo', 'text' => '' ),
				),
			),
			array(
				'acf_fc_layout' => 'offer',
				'title'         => 'Oferta diferenciada no solo usamos tecnología, la construimos',
				'emphasis'      => 'Oferta diferenciada',
				'text'          => 'Si tu equipo necesita tecnología de marketing que no existe en el mercado, la desarrollamos para ti.',
				'items'         => array(
					array( 'icon' => 'layout-dashboard', 'title' => 'Dashboard e integraciones a medida', 'text' => 'Herramientas de gestión diseñadas para tu operación — no plantillas genéricas. Conectamos CRM, ERP y sistemas operativos entre sí.', 'tags' => 'Reactivación, CRM' ),
					array( 'icon' => 'brain', 'title' => 'Flujos de IA aplicados a ventas y marketing', 'text' => 'Generación de contenido e imagen, y automatizaciones que aceleran la producción sin sacrificar calidad.', 'tags' => 'CRM, Ads' ),
					array( 'icon' => 'settings-2', 'title' => 'Automatización de procesos comerciales', 'text' => 'Seguimiento de leads, reportes y checklists operativos que corren solos, sin depender de memoria o buena voluntad.', 'tags' => 'CRM, Datos, Cuentas Clave' ),
				),
			),
			array(
				'acf_fc_layout' => 'projects',
				'eyebrow'       => 'Portafolio',
				'title'         => 'Nuestros Proyectos',
				'emphasis'      => 'Proyectos',
				'text'          => 'Nuestros casos de éxito con proyectos a la medida y con un enfoque a crecimiento de ventas e innovación.',
			),
			array(
				'acf_fc_layout' => 'tools',
				'title'         => 'Trabajamos con +20 herramientas de marketing y tecnología',
				'emphasis'      => '+20 herramientas de marketing y tecnología',
				'items'         => array(
					array( 'name' => 'Claude' ),
					array( 'name' => 'OpenClaw' ),
					array( 'name' => 'Wati' ),
					array( 'name' => 'n8n' ),
					array( 'name' => 'Zapier' ),
					array( 'name' => 'Metricool' ),
					array( 'name' => 'HubSpot' ),
					array( 'name' => 'fal' ),
				),
			),
			array(
				'acf_fc_layout' => 'relevance',
				'eyebrow'       => 'RELEVANCIA SECTORIAL',
				'title'         => 'Mismos retos. Misma disciplina. Ahora para tu marca.',
				'text'          => 'Si operas en hospitalidad, turismo o real estate, enfrentas los mismos retos que ya resolvimos: cliente de alto valor, ciclo de decisión largo, y la necesidad de una narrativa premium consistente en cada punto de contacto.',
				'items'         => array(
					array( 'icon' => 'calendar', 'title' => 'Ciclos de decisión de semanas o meses', 'text' => 'Hospitalidad, turismo y real estate deciden en meses, no en clics.' ),
					array( 'icon' => 'search', 'title' => 'Clientes que investigan antes de contactar', 'text' => '' ),
					array( 'icon' => 'shield-check', 'title' => 'Marca y confianza como factor de cierre', 'text' => '' ),
				),
			),
			array(
				'acf_fc_layout' => 'how',
				'eyebrow'       => 'CÓMO TRABAJAMOS',
				'title'         => 'Tres líneas de servicio, una sola disciplina',
				'text'          => 'Pocas marcas. Foco total. No buscamos volumen.',
				'items'         => array(
					array( 'icon' => 'presentation-chart-line', 'title' => 'Estrategia y operación de marketing', 'text' => 'Posicionamiento, sistema y ejecución continua.' ),
					array( 'icon' => 'target', 'title' => 'Desarrollo de contenido y marca', 'text' => 'Narrativa premium bilingüe, consistente en cada canal.' ),
					array( 'icon' => 'code', 'title' => 'Desarrollo tecnológico e IA', 'text' => 'Dashboards, integraciones y automatización a medida.' ),
				),
			),
			array(
				'acf_fc_layout' => 'next_steps',
				'eyebrow'       => 'PRÓXIMOS PASOS',
				'title'         => 'Empezamos con un diagnóstico estratégico sin compromiso.',
				'emphasis'      => 'sin compromiso.',
				'video_label'   => 'Reproducir',
				'video_duration' => '4 min 30 sec',
				'text'          => 'Auditoría inicial de marketing y tecnología para identificar brechas y oportunidades concretas, antes de proponer cualquier alcance de trabajo.',
				'items'         => array(
					array( 'title' => 'Diagnóstico', 'text' => 'Revisión de marca, marketing y stack tecnológico actual.' ),
					array( 'title' => 'Hallazgos', 'text' => 'Brechas y oportunidades priorizadas por impacto.' ),
					array( 'title' => 'Propuesta', 'text' => 'Alcance de trabajo a medida, con foco en resultados.' ),
				),
			),
			array(
				'acf_fc_layout' => 'strategists',
				'title'         => 'Conoce a nuestros estrategas',
				'text'          => 'Nuestros expertos combinan conocimiento técnico profundo, pensamiento creativo y un enfoque centrado en resultados para implementar sistemas de IA que transforman operaciones comerciales.',
			),
			array(
				'acf_fc_layout' => 'form',
				'eyebrow'       => 'Empecemos',
				'title'         => 'Construyamos tu próximo sistema de crecimiento',
				'text'          => 'Hablemos de tu marca.',
				'interests'     => "Sistemas IA\nGrowth Ops\nAutomatización\nConsultoría\nRevenue Ops",
			),
		);
	}

	/**
	 * Site Settings values from the footer and contact blocks of the design.
	 *
	 * @return array<string,string>
	 */
	public static function settings(): array {
		return array(
			'contact_email'     => 'hola@markea.agency',
			'contact_address'   => 'Av. Nader, Bancos #27l, 77500, Cancún, Quintana Roo',
			'menu_eyebrow'      => 'Conéctate con nosotros',
			'menu_title'        => 'Transforma tu Visión en una Experiencia que Perdura',
			'menu_copyright'    => '© 2026 Markea IA. Todos los derechos reservados.',
			'social_linkedin'   => 'https://www.linkedin.com/company/33240077',
			'social_instagram'  => 'https://www.instagram.com/markeagroup/',
			'social_facebook'   => 'https://www.facebook.com/markeagency',
			'footer_cta_title'  => '¿Tienes un proyecto fascinante en mente?',
			'footer_cta_emphasis' => 'en mente',
			'footer_badges'     => "Google Partner\nHubSpot\nMeta Partner",
			'footer_tagline'    => 'Impulsados por resultados',
			'footer_text'       => 'Construimos sistemas de IA que transforman operaciones comerciales',
			'footer_services'   => "Sistemas IA\nGrowth Ops\nAutomatización\nRevenue Ops\nConsultoría",
			'footer_copyright'  => '© 2026 Markea IA Native. Todos los derechos reservados.',
			'newsletter_title'  => 'Newsletter',
			'newsletter_button' => 'Registrarme',
		);
	}

	/**
	 * Values written by earlier seeder versions; they are replaced (not treated as user edits).
	 *
	 * @return array<string,array<int,string>>
	 */
	public static function legacy_settings(): array {
		return array(
			'contact_address' => array( 'Av. Nader, Cancún, Quintana Roo' ),
		);
	}

	/**
	 * Main menu links (anchors on the landing). Relative URLs so they survive domain changes.
	 *
	 * @return array<int,array{0:string,1:string}> Label and URL.
	 */
	public static function menu(): array {
		return array(
			array( 'Inicio', '/#inicio' ),
			array( 'Casos', '/#proyectos' ),
			array( 'Servicios', '/#como-trabajamos' ),
			array( 'Nosotros', '/#quienes-somos' ),
		);
	}
}
