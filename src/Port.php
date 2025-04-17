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
	 * Determines if the port is defined.
	 *
	 * @return bool True if the port is defined, false otherwise.
	 */
	public function isDefined(): bool
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
	public function getEffective(): ?int
    {
        if( $this->port !== null )
		{
            return $this->port;
        }

        return self::defaultPortForScheme( $this->scheme );
    }

	/**
	 * Returns the default port number for the given scheme.
	 *
	 * @param string $scheme The scheme to retrieve the default port number for.
	 * @return int|null The default port number if the scheme is supported, null otherwise.
	 */
	public static function defaultPortForScheme( string $scheme ): ?int
    {
        return match( $scheme )
		{
            'http', 'ws' => 80,
            'https', 'wss' => 443,
            'ftp' => 21,
            'ssh' => 22,
            'mysql' => 3306,
            'pgsql', 'postgres' => 5432,
            'redis' => 6379,
            default => null,
        };
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
	 * The array contains two elements, 'port' and 'effective'. The 'port'
	 * element contains the port number that was explicitly set, or null if
	 * no port number was set. The 'effective' element contains the effective
	 * port number to use, which is either the explicitly set port number or
	 * the default port number for the scheme.
	 *
	 * @return array The port number and the effective port number.
	 */
	public function toArray(): array
	{
		return [
			'address' => $this->get(),
			'effective' => $this->getEffective()
		];
	}
}
