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
        'http', 'https', 'ftp', 'mailto', 'file', 'tel', 'data', 'irc', 'ssh', 'webcal'
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
	 * @return string The scheme, e.g., "http" or "https".
	 */
	public function __toString(): string
	{
		return $this->scheme;
	}

	/**
	 * @inheritDoc
	 * 
	 * Returns the scheme as a string.
	 * 
	 * @return string The scheme, e.g., "http" or "https".
	 */
	public function jsonSerialize(): string
	{
		return $this->scheme;
	}
}
