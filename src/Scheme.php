<?php

namespace Iceylan\Urlify;

use JsonSerializable;

/**
 * Represents a URL scheme.
 */
class Scheme implements JsonSerializable
{
	/**
	 * The scheme of the URL.
	 *
	 * @var ?string
	 */
	private ?string $scheme;

	/**
	 * An array of custom schemes.
	 *
	 * @var array
	 */
	private static array $schemes =
	[
		// web protocols
		'http' => [ 'suffix' => '://', 'secure' => false ],
		'https' => [ 'suffix' => '://', 'secure' => true ],
		'ws' => [ 'suffix' => '://', 'secure' => false ],
		'wss' => [ 'suffix' => '://', 'secure' => true ],

		// file transfer protocols
		'ftp' => [ 'suffix' => '://', 'secure' => false ],
		'ftps' => [ 'suffix' => '://', 'secure' => true ],
		'sftp' => [ 'suffix' => '://', 'secure' => false ],
		'scp' => [ 'suffix' => '://', 'secure' => false ],
		'tftp' => [ 'suffix' => '://', 'secure' => false ],
		
		// database connection protocols
		'mysql' => [ 'suffix' => '://', 'secure' => false ],
		'pgsql' => [ 'suffix' => '://', 'secure' => false ],
		'postgres' => [ 'suffix' => '://', 'secure' => false ],
		'sqlite' => [ 'suffix' => '://', 'secure' => false ],
		'mongodb' => [ 'suffix' => '://', 'secure' => false ],
		'redis' => [ 'suffix' => '://', 'secure' => false ],
		'mssql' => [ 'suffix' => '://', 'secure' => false ],

		// application & service protocols
		'ssh' => [ 'suffix' => '://', 'secure' => false ],
		'telnet' => [ 'suffix' => '://', 'secure' => false ],
		'ldap' => [ 'suffix' => '://', 'secure' => false ],
		'smb' => [ 'suffix' => '://', 'secure' => false ],
		'nfs' => [ 'suffix' => '://', 'secure' => false ],

		// email and communication protocols
		'mailto' => [ 'suffix' => ':', 'secure' => false ],
		'tel' => [ 'suffix' => ':', 'secure' => false ],
		'sms' => [ 'suffix' => ':', 'secure' => false ],
		'sip' => [ 'suffix' => ':', 'secure' => false ],

		// file system and special URI protocols
		'file' => [ 'suffix' => ':', 'secure' => false ],
		'data' => [ 'suffix' => ':', 'secure' => false ],
		'blob' => [ 'suffix' => ':', 'secure' => false ],
		'urn' => [ 'suffix' => ':', 'secure' => false ],

		// other (some platforms or frameworks use these)
		'chrome' => [ 'suffix' => ':', 'secure' => false ],
		'about' => [ 'suffix' => ':', 'secure' => false ],
		'geo' => [ 'suffix' => ':', 'secure' => false ],
		'javascript' => [ 'suffix' => ':', 'secure' => false ],
		'intent' => [ 'suffix' => ':', 'secure' => false ]
	];

	/**
	 * Constructs a new Scheme object.
	 *
	 * @param ?string $scheme The scheme to be set, e.g., "http" or "https".
	 */
	public function __construct( ?string $scheme = null )
	{
		$this->set( $scheme );
	}
	
	/**
	 * Registers a custom scheme.
	 *
	 * @param string $scheme The scheme to be registered, e.g., "ftp" or "sftp".
	 * @param string $suffix The suffix of the scheme, e.g., "://" or ":".
	 * @param bool $secure Whether the scheme is secure or not.
	 */
	public static function registerScheme(
		string $scheme,
		string $suffix = '://',
		bool $secure = false
	): void
	{
		self::$schemes[ strtolower( $scheme )] =
		[
			'suffix' => $suffix,
			'secure' => $secure
		];
	}

	/**
	 * Gets a scheme from the list of registered schemes.
	 *
	 * @param string $scheme The scheme to be retrieved, e.g., "http" or "https".
	 * @return ?array The scheme definition, or null if the scheme is unknown.
	 */
	public static function getScheme( string $scheme ): ?array
	{
		return static::$schemes[ $scheme ] ?? null;
	}

	/**
	 * Returns the scheme as a string.
	 *
	 * @return ?string The scheme, e.g., "http" or "https".
	 */
	public function get(): ?string
	{
		return $this->scheme;
	}

	/**
	 * Sets the scheme for the current object.
	 *
	 * @param ?string $scheme The scheme to be set, e.g., "http" or "https".
	 * @return self The current instance of the Scheme class.
	 */
	public function set( ?string $scheme ): self
	{
		$this->scheme = $scheme !== null
			? strtolower( $scheme )
			: null;
		
		return $this;
	}

	/**
	 * Determines if the scheme is secure.
	 *
	 * @return bool True if the scheme is "https", false otherwise.
	 */
	public function isSecure(): bool
	{
		return $this->isKnown()
			? static::getScheme( $this->scheme )[ 'secure' ]
			: false;
	}

	/**
	 * Determines if the scheme is known.
	 *
	 * @return bool True if the scheme is in the list of known schemes, false otherwise.
	 */
	public function isKnown(): bool
	{
		return in_array( $this->scheme, array_keys( static::$schemes ));
	}

	/**
	 * Returns the scheme as a string.
	 *
	 * @return string The scheme, e.g., "http://" or "https://".
	 */
	public function __toString(): string
	{
		return $this->scheme === null
			? ''
			: $this->scheme . (
				$this->isKnown()
					? static::getScheme( $this->scheme )[ 'suffix' ]
					: '://'
			);
	}

	/**
	 * Returns an array that can be serialized to JSON.
	 *
	 * This method is used when the object is passed to json_encode() to
	 * generate a JSON representation of the Scheme object.
	 *
	 * @return array The JSON serializable representation of the Scheme object.
	 */
	public function jsonSerialize(): array
	{
		return
		[
			'name' => $this->scheme,
			'isSecure' => $this->isSecure(),
			'isKnown' => $this->isKnown()
		];
	}
}
