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
	 * @var string
	 */
	private string $scheme;

	/**
	 * An array of known schemes.
	 * 
	 * @var array
	 */
	protected const KNOWN_SCHEMES =
	[
		// web protocols
		'http',
		'https',
		'ws',
		'wss',

		// file transfer protocols
		'ftp',
		'ftps',
		'sftp',
		'scp',
		'tftp',

		// database connection protocols
		'mysql',
		'pgsql',
		'postgres',
		'sqlite',
		'mongodb',
		'redis',
		'mssql',

		// application & service protocols
		'ssh',
		'telnet',
		'ldap',
		'smb',
		'nfs',

		// email and communication protocols
		'mailto',
		'tel',
		'sms',
		'sip',

		// file system and special URI protocols
		'file',
		'data',
		'blob',
		'urn',

		// other (some platforms or frameworks use these)
		'chrome',
		'about',
		'geo',
		'javascript',
		'intent',
    ];

	/**
	 * An array of schemes that have a colon prefix.
	 * 
	 * @var array
	 */
	const COLON_SCHEMES = [
		'mailto',
		'tel',
		'sms',
		'sip',
		'urn',
		'data',
		'blob',
		'geo',
		'about',
		'javascript',
		'intent',
		'ssh',
		'scp',
		'sqlite',
	];

	/**
	 * Constructs a new Scheme object.
	 *
	 * @param string $scheme The scheme to be set, e.g., "http" or "https".
	 */
	public function __construct( string $scheme )
	{
		$this->scheme = $scheme;
	}

	/**
	 * Returns the scheme as a string.
	 *
	 * @return string The scheme, e.g., "http" or "https".
	 */
	public function get(): string
	{
		return $this->scheme;
	}

	/**
	 * Determines if the scheme is secure.
	 *
	 * @return bool True if the scheme is "https", false otherwise.
	 */
	public function isSecure(): bool
	{
		return $this->scheme === 'https';
	}

	/**
	 * Determines if the scheme is known.
	 *
	 * @return bool True if the scheme is in the list of known schemes, false otherwise.
	 */
	public function isKnown(): bool
	{
		return in_array( $this->scheme, self::KNOWN_SCHEMES );
	}

	/**
	 * Returns the scheme as a string.
	 *
	 * @return string The scheme, e.g., "http://" or "https://".
	 */
	public function __toString(): string
	{
		return $this->scheme . ( in_array( $this->scheme, self::COLON_SCHEMES )? ':' : '://' );
	}

	/**
	 * @inheritDoc
	 * 
	 * Returns the scheme as a string.
	 * 
	 * @return array The scheme, e.g., "http" or "https".
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
