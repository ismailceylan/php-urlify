<?php

namespace Iceylan\Urlify;

use Exception;
use JsonSerializable;

/**
 * Represents a host of a URL.
 */
class Host implements JsonSerializable
{
	/**
	 * The original host.
	 *
	 * @var string
	 */
	private string $original;

	/**
	 * The top-level domain of the host.
	 * 
	 * @var string
	 */
	private string $topLevelDomain;

	/**
	 * An array of top-level domains.
	 *
	 * @var array
	 */
	private array $topLevelDomains = [];

	/**
	 * Constructs a new Host object.
	 *
	 * @param string $host The original host to be set.
	 */
	public function __construct( string $host )
	{
		$this->original = $host;
	}

	/**
	 * Retrieves the top-level domain from the original host.
	 *
	 * If the top-level domain has been previously determined, it returns the cached value.
	 * Otherwise, it splits the original host into parts and checks each possible top-level
	 * domain until a match is found. If no valid top-level domain is found, an exception
	 * is thrown.
	 *
	 * @return string The top-level domain.
	 * @throws Exception If no valid top-level domain can be determined.
	 */
	public function getTopLevelDomain(): string
	{
		if( isset( $this->topLevelDomain ))
		{
			return $this->topLevelDomain;
		}

		$parts = explode( '.', $this->original );
		$len = count( $parts );

		for( $i = 0; $i < $len; $i++ )
		{
			$dotted = implode( '.', array_slice( $parts, $i ));

			if( $this->isTopLevelDomain( $dotted ))
			{
				return $this->topLevelDomain = $dotted;
			}
		}

		throw new Exception( "Unknown top-level domain: {$this->original}" );
	}

	/**
	 * Retrieves the primary domain name from the original host.
	 *
	 * The primary domain name is the first subdomain in front of the
	 * top-level domain.
	 *
	 * @return string The primary domain name.
	 * @throws Exception If no valid top-level domain can be determined.
	 */
	public function getPrimaryDomainName(): string
	{
		$tldCleanedNS = trim( str_replace( $this->getTopLevelDomain(), '', $this->original ), '.' );
		$parts = explode( '.', $tldCleanedNS );

		return $parts[ count( $parts ) - 1 ];
	}

	/**
	 * Retrieves the primary domain name from the original host.
	 *
	 * The primary domain is the full domain name of the primary domain
	 * name in front of the top-level domain.
	 *
	 * @return string The primary domain name.
	 * @throws Exception If no valid top-level domain can be determined.
	 */
	public function getPrimaryDomain(): string
	{
		return $this->getPrimaryDomainName() . '.' . $this->getTopLevelDomain();
	}

	/**
	 * Retrieves the subdomain from the original host.
	 *
	 * The subdomain is the portion of the domain that precedes the primary domain
	 * and top-level domain. If there is no subdomain, an empty string is returned.
	 *
	 * @return string The subdomain of the host.
	 */
	public function getSubdomainName(): string
	{
		return trim( str_replace( $this->getPrimaryDomain(), '', $this->original ), '.' );
	}

	/**
	 * Retrieves the subdomains of the host as an array.
	 *
	 * If there is no subdomain, an empty array is returned.
	 *
	 * @return array The subdomains of the host.
	 */
	public function getSubdomains(): array
	{
		return explode( '.', $this->getSubdomainName());
	}

	/**
	 * Determines if the given domain is a valid top-level domain.
	 *
	 * This method checks if the given domain is listed in the Public Suffix List.
	 *
	 * @param string $domain The domain to check.
	 * @return bool True if the domain is a valid top-level domain, false otherwise.
	 */
	public function isTopLevelDomain( string $domain ): bool
	{
		if( empty( $this->topLevelDomains ))
		{
			$this->topLevelDomains = require_once( 'resources/tlds.php' );
		}

		return in_array( $domain, $this->topLevelDomains );
	}

	/**
	 * Converts the object to an array for JSON serialization.
	 *
	 * The resulting array will contain the following keys:
	 *
	 * - original: The original host name.
	 * - topLevelDomain: The top-level domain name.
	 * - primaryDomainName: The primary domain name.
	 * - primaryDomain: The primary domain name with the top-level domain.
	 * - subdomainName: The subdomain name.
	 * - subdomains: The subdomains as an array.
	 *
	 * @return array The array representation of the object.
	 */
	public function toArray(): array
	{
		return [
			'original' => $this->original,
			'topLevelDomain' => $this->getTopLevelDomain(),
			'primaryDomainName' => $this->getPrimaryDomainName(),
			'primaryDomain' => $this->getPrimaryDomain(),
			'subdomainName' => $this->getSubdomainName(),
			'subdomains' => $this->getSubdomains(),
		];
	}

	/**
	 * Converts the object to an array for JSON serialization.
	 *
	 * @return array The array representation of the object.
	 */
	public function jsonSerialize(): array
	{
		return $this->toArray();
	}
}
