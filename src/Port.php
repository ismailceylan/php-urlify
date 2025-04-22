<?php

namespace Iceylan\Urlify;

use JsonSerializable;

/**
 * Represents a port of a URL.
 */
class Port implements JsonSerializable
{
	/**
	 * The port number.
	 *
	 * @var integer
	 */
	private ?int $port;
	
	/**
	 * The scheme of the URL.
	 *
	 * @var Scheme
	 */
	private Scheme $scheme;

	/**
	 * Constructs a new Port object.
	 *
	 * @param int|string $port the port number
	 */
	public function __construct( int|string|null $port, Scheme $scheme )
	{
		$this->port = $port? (int) $port : null;
		$this->scheme = $scheme;
	}

	/**
	 * Converts the port number to a string representation.
	 *
	 * @return string The string representation of the port number.
	 */
	public function __toString()
	{
		return $this->port !== null
			? ':' . (string) $this->port
			: '';
	}

	/**
	 * Returns the port number.
	 *
	 * @return int|null The port number.
	 */
	public function get(): int|null
	{
		return $this->port;
	}

	/**
	 * Sets the port number.
	 *
	 * If the provided port number is null, the port is reset to null.
	 * Otherwise, the given port number is set.
	 *
	 * @param int|null $port The port number to set, or null to unset.
	 * @return self The current instance for method chaining.
	 */
	public function set( ?int $port ): self
	{
		$this->port = $port;
		return $this;
	}

	/**
	 * Determines if the port is defined.
	 *
	 * @return bool True if the port is defined, false otherwise.
	 */
	public function isEmpty(): bool
	{
		return $this->port !== null;
	}

	/**
	 * Returns the effective port number.
	 *
	 * If the port number is explicitly set, it will be returned. Otherwise,
	 * the default port number for the scheme will be returned.
	 *
	 * @return int|null The effective port number.
	 */
	public function getDefault(): ?int
    {
        if( $this->port !== null )
		{
            return $this->port;
        }

        return self::getDefaultPortForScheme( $this->scheme );
    }

	/**
	 * Determines if the port is the default port for its scheme.
	 *
	 * @return bool True if the port is explicitly set and matches the default 
	 * port for the scheme, false otherwise.
	 */
	public function isDefault(): bool
	{
		return $this->port !== null &&
			$this->port === self::getDefaultPortForScheme( $this->scheme );
	}

	/**
	 * Returns the default port number for the given scheme.
	 *
	 * @param string $scheme The scheme to retrieve the default port number for.
	 * @return int|null The default port number if the scheme is supported, null otherwise.
	 */
	public static function getDefaultPortForScheme( string $scheme ): ?int
    {
		return Scheme::getScheme( $scheme )[ 'port' ] ?? null;
    }

	/**
	 * Converts the object to an array for JSON serialization.
	 *
	 * @return array The port number.
	 */
	public function jsonSerialize(): array
	{
		return $this->toArray();
	}

	/**
	 * Converts the object to an array.
	 *
	 * @return array An associative array containing the following keys:
	 *  - address: The port number as a string.
	 *  - default: The default port number for the scheme as an integer.
	 */
	public function toArray(): array
	{
		return [
			'address' => $this->get(),
			'default' => $this->getDefault()
		];
	}
}
