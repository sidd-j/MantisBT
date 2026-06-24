<?php
/**
 * PostgreSQL-backed PHP session handler.
 * Replaces the default filesystem sessions so sessions survive container restarts on Render.
 *
 * Table is auto-created on first use.
 */

class PgSessionHandler implements SessionHandlerInterface {
	private PDO $pdo;
	private int $lifetime;

	public function __construct( PDO $p_pdo, int $p_lifetime = 86400 ) {
		$this->pdo = $p_pdo;
		$this->lifetime = $p_lifetime;
		$this->ensureTable();
	}

	private function ensureTable(): void {
		$this->pdo->exec( "
			CREATE TABLE IF NOT EXISTS php_sessions (
				sess_id   VARCHAR(128) PRIMARY KEY,
				sess_data TEXT         NOT NULL DEFAULT '',
				updated   BIGINT       NOT NULL DEFAULT 0
			)
		" );
	}

	public function open( string $path, string $name ): bool {
		return true;
	}

	public function close(): bool {
		return true;
	}

	public function read( string $id ): string|false {
		$stmt = $this->pdo->prepare(
			'SELECT sess_data FROM php_sessions WHERE sess_id = :id AND updated > :exp'
		);
		$stmt->execute( [ ':id' => $id, ':exp' => time() - $this->lifetime ] );
		$row = $stmt->fetch( PDO::FETCH_ASSOC );
		return $row ? $row['sess_data'] : '';
	}

	public function write( string $id, string $data ): bool {
		$stmt = $this->pdo->prepare( "
			INSERT INTO php_sessions (sess_id, sess_data, updated)
			VALUES (:id, :data, :now)
			ON CONFLICT (sess_id) DO UPDATE
			SET sess_data = EXCLUDED.sess_data,
			    updated   = EXCLUDED.updated
		" );
		return $stmt->execute( [ ':id' => $id, ':data' => $data, ':now' => time() ] );
	}

	public function destroy( string $id ): bool {
		$stmt = $this->pdo->prepare( 'DELETE FROM php_sessions WHERE sess_id = :id' );
		return $stmt->execute( [ ':id' => $id ] );
	}

	public function gc( int $max_lifetime ): int|false {
		$stmt = $this->pdo->prepare( 'DELETE FROM php_sessions WHERE updated < :exp' );
		$stmt->execute( [ ':exp' => time() - $max_lifetime ] );
		return $stmt->rowCount();
	}
}

/**
 * Bootstrap the DB session handler using the same DSN as MantisBT's config.
 * Called once before session_start().
 */
function db_session_handler_init(): void {
	$host     = $GLOBALS['g_hostname']      ?? 'localhost';
	$db       = $GLOBALS['g_database_name'] ?? 'mantisbt';
	$user     = $GLOBALS['g_db_username']   ?? '';
	$pass     = $GLOBALS['g_db_password']   ?? '';

	# host may include port as "host:port"
	$parts = explode( ':', $host, 2 );
	$dsn_host = $parts[0];
	$dsn_port = isset( $parts[1] ) ? ';port=' . $parts[1] : '';

	try {
		$pdo = new PDO(
			"pgsql:host={$dsn_host}{$dsn_port};dbname={$db}",
			$user,
			$pass,
			[ PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ]
		);

		$handler = new PgSessionHandler( $pdo );
		session_set_save_handler( $handler, true );
	} catch ( Exception $e ) {
		# Fall back to default filesystem sessions rather than crashing.
		error_log( 'db_session_handler_init failed: ' . $e->getMessage() );
	}
}
