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
	 * An array of subdomains.
	 *
	 * @var array
	 */
	private array $subdomains = [];

	/**
	 * The primary domain name of the host.
	 *
	 * @var ?string
	 */
	private ?string $primaryDomainName = null;

	/**
	 * The top-level domain of the host.
	 * 
	 * @var ?string
	 */
	private ?string $topLevelDomainName = null;

	/**
	 * An array of top-level domains.
	 *
	 * @var array
	 */
	private array $tlds = [];

	/**
	 * Constructs a new Host object.
	 *
	 * @param ?string $host The original host to be set.
	 */
	public function __construct( ?string $host = null )
	{
		$this->setTopLevelDomains( require( 'resources/tlds.php' ));

		if( $host )
		{
			$this->parse( $host );
		}
	}

	/**
	 * Parses the given host into its parts.
	 *
	 * Retrieves the top-level domain, primary domain name and subdomains from the
	 * original host and sets them to their respective properties.
	 *
	 * @param string $host The original host to be parsed.
	 */
	private function parse( string $host )
	{
		$this->topLevelDomainName = $this->parseTopLevelDomain( $host );
		$this->primaryDomainName = $this->parsePrimaryDomainName( $host );
		$this->subdomains = $this->parseSubdomains( $host );
	}

	/**
	 * Retrieves the top-level domain from the original host.
	 *
	 * If the top-level domain has been previously determined, it returns the cached value.
	 * Otherwise, it splits the original host into parts and checks each possible top-level
	 * domain until a match is found. If no valid top-level domain is found, an exception
	 * is thrown.
	 *
	 * @param string $host The original host.
	 * @return ?string The top-level domain.
	 * @throws Exception If no valid top-level domain can be determined.
	 */
	public function parseTopLevelDomain( string $host ): ?string
	{
		$parts = explode( '.', $host );
		$len = count( $parts );

		for( $i = 0; $i < $len; $i++ )
		{
			$dotted = implode( '.', array_slice( $parts, $i ));

			if( $this->isTopLevelDomain( $dotted ))
			{
				return $dotted;
			}
		}

		throw new Exception( "Unknown top-level domain: {$host}" );
	}

	/**
	 * Sets the array of top-level domains for the host.
	 *
	 * This method allows for the specification of valid top-level domains
	 * that the host can recognize. The provided array of top-level domains
	 * replaces any previously set list.
	 *
	 * @param array $tlds An array of top-level domains to set.
	 * @return self The object itself.
	 */
	public function setTopLevelDomains( array $tlds = []): self
	{
		$this->tlds = $tlds;
		return $this;
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
		return in_array( $domain, $this->tlds );
	}

	/**
	 * Retrieves the primary domain name from the original host.
	 *
	 * The primary domain name is the first subdomain in front of the
	 * top-level domain.
	 *
	 * @return ?string The primary domain name.
	 * @throws Exception If no valid top-level domain can be determined.
	 */
	public function parsePrimaryDomainName( string $host ): ?string
	{
		$hostWithoutTld = trim( str_replace( $this->topLevelDomainName, '', $host ), '.' );

		if( $hostWithoutTld === '' )
		{
			return null;
		}

		$parts = explode(
			separator: '.',
			string: $hostWithoutTld
		);

		return $parts[ count( $parts ) - 1 ];
	}

	/**
	 * Retrieves the subdomains of the host as an array.
	 *
	 * If there is no subdomain, an empty array is returned.
	 *
	 * @param string $host The original host.
	 * @return array The subdomains of the host.
	 */
	public function parseSubdomains( string $host ): array
	{
		// If there is no primary domain name, that means there is no subdomain
		if( $this->primaryDomainName === null )
		{
			return [];
		}

		// If there is no top-level domain name, that means there is no subdomain
		if( $this->topLevelDomainName === null )
		{
			return [];
		}

		$fullDomainCleaned = trim(
			string: str_replace(
				search: $this->primaryDomainName . '.' . $this->topLevelDomainName,
				replace: '',
				subject: $host
			),
			characters: '.'
		);

		// If the full domain is empty, that means there is no subdomain
		if( $fullDomainCleaned === '' )
		{
			return [];
		}

		$parts = explode(
			separator: '.',
			string: $fullDomainCleaned
		);

		return $parts;
	}

	/**
	 * Sets the host from a string.
	 *
	 * If the given host is null, the object is reset to its default state.
	 * Otherwise, the host is parsed into its parts and set to the object.
	 *
	 * @param string $host The host to set.
	 * @return self The object itself.
	 */
	public function set( ?string $host = null ): self
	{
		if( $host === null )
		{
			$this->topLevelDomainName = null;
			$this->primaryDomainName = null;
			$this->subdomains = [];
		}
		else
		{
			$this->parse( $host );
		}

		return $this;
	}

	/**
	 * Sets the subdomain name for the host.
	 *
	 * If the given subdomain is null, the subdomains are reset to an empty array.
	 * Otherwise, the given subdomain is split into parts and set as the subdomains.
	 *
	 * @param ?string $subdomain The subdomain name to set.
	 * @return self The object itself.
	 */
	public function setSubdomain( ?string $subdomain ): self
	{
		if( $subdomain === null )
		{
			$this->subdomains = [];
		}
		else
		{
			$this->subdomains = explode( '.', $subdomain );
		}

		return $this;
	}

	/**
	 * Adds a subdomain to the end of the existing subdomains array.
	 *
	 * @param string $subdomain The subdomain to add.
	 * @return self The object itself.
	 */
	public function pushSubdomain( string $subdomain ): self
	{
		$this->subdomains[] = $subdomain;
		return $this;
	}

	/**
	 * Prepends a subdomain to the beginning of the existing subdomains array.
	 *
	 * @param string $subdomain The subdomain to prepend.
	 * @return self The object itself.
	 */
	public function prependSubdomain( string $subdomain ): self
	{
		array_unshift( $this->subdomains, $subdomain );
		return $this;
	}

	/**
	 * Sets the primary domain name for the host.
	 *
	 * If the given primary domain name is null, the primary domain name is reset to null.
	 * Otherwise, the given primary domain name is set as the primary domain name.
	 *
	 * @param ?string $name The primary domain name to set.
	 * @return self The object itself.
	 */
	public function setPrimaryDomainName( ?string $name ): self
	{
		$this->primaryDomainName = $name;
		return $this;
	}

	/**
	 * Sets the top-level domain name for the host.
	 *
	 * If the given top-level domain name is null, the top-level domain name is reset to null.
	 * Otherwise, the given top-level domain name is set as the top-level domain name.
	 *
	 * @param ?string $name The top-level domain name to set.
	 * @return self The object itself.
	 */
	public function setTopLevelDomainName( ?string $name ): self
	{
		$this->topLevelDomainName = $name;
		return $this;
	}

	/**
	 * Converts the object to a string representation.
	 *
	 * The string representation of the object is in the format of
	 * "subdomain.primaryDomain.topLevelDomain".
	 *
	 * @return string The string representation of the object.
	 */
	public function __toString()
	{
		$data = [];

		if( $sub = $this->getSubdomainName())
		{
			$data[] = $sub;
		}

		if( $primary = $this->getPrimaryDomainName())
		{
			$data[] = $primary;
		}

		if( $tld = $this->getTopLevelDomainName())
		{
			$data[] = $tld;
		}

		return implode( '.', $data );
	}

	/**
	 * Retrieves the subdomain name from the host.
	 *
	 * The subdomain name is the string representation of the subdomains array,
	 * joined by dots. Returns null if no subdomain exists.
	 *
	 * @return ?string The subdomain name.
	 */
	public function getSubdomainName(): ?string
	{
		return implode( '.', $this->subdomains ) ?: null;
	}

	/**
	 * Retrieves the subdomains as an array.
	 *
	 * @return array The subdomains of the host.
	 */
	public function getSubdomains(): array
	{
		return $this->subdomains;
	}

	/**
	 * Retrieves the primary domain name from the host.
	 *
	 * @return ?string The primary domain name.
	 */
	public function getPrimaryDomainName(): ?string
	{
		return $this->primaryDomainName;
	}

	/**
	 * Retrieves the top-level domain name from the host.
	 *
	 * @return ?string The top-level domain name, or null if not set.
	 */
	public function getTopLevelDomainName(): ?string
	{
		return $this->topLevelDomainName;
	}

	/**
	 * Retrieves the root domain of the host.
	 *
	 * The root domain is the string representation of the primary domain name and
	 * top-level domain name, joined by dots.
	 *
	 * @return string|null The root domain, or null if not set.
	 */
	public function getRootDomain(): ?string
	{
		$data = [];

		if( $primary = $this->getPrimaryDomainName())
		{
			$data[] = $primary;
		}

		if( $tld = $this->getTopLevelDomainName())
		{
			$data[] = $tld;
		}

		return implode( '.', $data ) ?: null;
	}

	/**
	 * Converts the object to an array for JSON serialization.
	 *
	 * The resulting array will contain the following keys:
	 *
	 * - original: The original host name.
	 * - topLevelDomain: The top-level domain name.
	 * - primaryDomainName: The primary domain name.
	 * - rootDomain: The primary domain name with the top-level domain.
	 * - subdomainName: The subdomain name.
	 * - subdomains: The subdomains as an array.
	 *
	 * @return array The array representation of the object.
	 */
	public function toArray(): array
	{
		return [
			'subdomains' => $this->getSubdomains(),
			'subdomainName' => $this->getSubdomainName(),
			'primaryDomainName' => $this->getPrimaryDomainName(),
			'topLevelDomain' => $this->getTopLevelDomainName(),
			'rootDomain' => $this->getRootDomain(),
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
