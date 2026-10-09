<?php
declare( strict_types=1 );

namespace Markea\Core\Cli;

use Markea\Core\Seed\Seeder;

defined( 'ABSPATH' ) || exit;

/**
 * Loads the initial content. Safe to run many times.
 */
final class SeedCommand {

	/**
	 * Seed terms, strategists, projects, base settings and the landing page.
	 *
	 * ## OPTIONS
	 *
	 * [--force]
	 * : Overwrite fields that already have a value.
	 *
	 * ## EXAMPLES
	 *
	 *     wp markea seed
	 *     wp markea seed --force
	 *
	 * @param array<int,string>    $args       Positional args.
	 * @param array<string,string> $assoc_args Flags.
	 */
	public function __invoke( array $args, array $assoc_args ): void {
		try {
			$log = ( new Seeder( isset( $assoc_args['force'] ) ) )->run();
		} catch ( \Throwable $e ) {
			\WP_CLI::error( $e->getMessage() );
		}

		foreach ( $log as $line ) {
			\WP_CLI::log( $line );
		}
		\WP_CLI::success( 'Seed completo.' );
	}
}
