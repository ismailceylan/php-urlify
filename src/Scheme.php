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
		'http' => [ 'suffix' => '://', 'secure' => false, 'port' => 80 ],
		'https' => [ 'suffix' => '://', 'secure' => true, 'port' => 443 ],
		'ws' => [ 'suffix' => '://', 'secure' => false, 'port' => 80 ],
		'wss' => [ 'suffix' => '://', 'secure' => true, 'port' => 443 ],

		// file transfer protocols
		'ftp' => [ 'suffix' => '://', 'secure' => false, 'port' => 21 ],
		'ftps' => [ 'suffix' => '://', 'secure' => true, 'port' => 990 ],
		'sftp' => [ 'suffix' => '://', 'secure' => false, 'port' => 22 ],
		'scp' => [ 'suffix' => '://', 'secure' => false, 'port' => 22 ],
		'tftp' => [ 'suffix' => '://', 'secure' => false, 'port' => 69 ],
		
		// database connection protocols
		'mysql' => [ 'suffix' => '://', 'secure' => false, 'port' => 3306 ],
		'pgsql' => [ 'suffix' => '://', 'secure' => false, 'port' => 5432 ],
		'postgres' => [ 'suffix' => '://', 'secure' => false, 'port' => 5432 ],
		'sqlite' => [ 'suffix' => '://', 'secure' => false, 'port' => null ],
		'mongodb' => [ 'suffix' => '://', 'secure' => false, 'port' => 27017 ],
		'redis' => [ 'suffix' => '://', 'secure' => false, 'port' => 6379 ],
		'mssql' => [ 'suffix' => '://', 'secure' => false, 'port' => 1433 ],

		// application & service protocols
		'ssh' => [ 'suffix' => '://', 'secure' => false, 'port' => 22 ],
		'telnet' => [ 'suffix' => '://', 'secure' => false, 'port' => null ],
		'ldap' => [ 'suffix' => '://', 'secure' => false, 'port' => 389 ],
		'smb' => [ 'suffix' => '://', 'secure' => false, 'port' => 445 ],
		'nfs' => [ 'suffix' => '://', 'secure' => false, 'port' => 2049 ],

		// email and communication protocols
		'mailto' => [ 'suffix' => ':', 'secure' => false, 'port' => null ],
		'tel' => [ 'suffix' => ':', 'secure' => false, 'port' => null ],
		'sms' => [ 'suffix' => ':', 'secure' => false, 'port' => null ],
		'sip' => [ 'suffix' => ':', 'secure' => false, 'port' => 5060 ],

		// file system and special URI protocols
		'file' => [ 'suffix' => ':', 'secure' => false, 'port' => null ],
		'data' => [ 'suffix' => ':', 'secure' => false, 'port' => null ],
		'blob' => [ 'suffix' => ':', 'secure' => false, 'port' => null ],
		'urn' => [ 'suffix' => ':', 'secure' => false, 'port' => null ],

		// other (some platforms or frameworks use these)
		'chrome' => [ 'suffix' => ':', 'secure' => false, 'port' => null ],
		'about' => [ 'suffix' => ':', 'secure' => false, 'port' => null ],
		'geo' => [ 'suffix' => ':', 'secure' => false, 'port' => null ],
		'javascript' => [ 'suffix' => ':', 'secure' => false, 'port' => null ],
		'intent' => [ 'suffix' => ':', 'secure' => false, 'port' => null ],
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
	 * Resets the scheme to null.
	 *
	 * @return self The current instance of the Scheme class.
	 */
	public function clean(): self
	{
		return $this->set( null );
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
			'isKnown' => $this->isKnown(),
			'suffix' => static::getScheme( $this->scheme )[ 'suffix' ] ?? '://'
		];
	}
}
