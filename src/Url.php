<?php

namespace Iceylan\Urlify;

use InvalidArgumentException;
use Iceylan\Urlify\Path\Path;
use Iceylan\Urlify\Query\Query;

class Url
{
	/**
	 * The options.
	 *
	 * @var integer
	 */
	private int $options;

	/**
	 * The parts of the URL.
	 *
	 * @var array
	 */
	private array $parts;

	/**
	 * The scheme of the URL.
	 *
	 * @var Scheme
	 */
	public Scheme $scheme;

	/**
	 * The host of the URL.
	 *
	 * @var Auth
	 */
	public Auth $auth;

	/**
	 * The host of the URL.
	 *
	 * @var Host
	 */
	public Host $host;

	/**
	 * The port of the URL.
	 *
	 * @var Port
	 */
	public Port $port;

	/**
	 * The path of the URL.
	 *
	 * @var Path
	 */
	public Path $path;

	/**
	 * The query of the URL.
	 *
	 * @var Query
	 */
	public Query $query;

	/**
	 * The fragment of the URL.
	 *
	 * @var Fragment
	 */
	public Fragment $fragment;

	/**
	 * Auto-detects the scheme of the URL.
	 * 
	 * @var int
	 */
	public const AutoDetectScheme = 1;

	/**
	 * Constructs a new Url object.
	 *
	 * @param string $url the URL to be parsed
	 * @throws InvalidArgumentException if the given URL is invalid
	 */
	public function __construct( ?string $url = null, int $options = 0 )
	{
		$this->options = $options;

		$this->scheme = new Scheme;
		$this->auth = new Auth;
		$this->host = new Host;
		$this->port = new Port( null, $this->scheme );
		$this->path = new Path;
		$this->query = new Query;
		$this->fragment = new Fragment;
		
		if( $url !== null )
		{
			$this->parse( $url );	
		}
	}

	/**
	 * Parses the given URL into it's parts.
	 * 
	 * @param string $url the URL to be parsed
	 * @throws InvalidArgumentException if the given URL is invalid
	 */
	public function parse( string $url )
	{
		static::isValid( $url );

		$parts = parse_url( $this->normalize( $url )) ?: [];

		$this->setScheme( $parts[ 'scheme' ] ?? null );
		$this->setHost( $parts[ 'host' ] ?? null );
		$this->setUsername( $parts[ 'user' ] ?? null );
		$this->setPassword( $parts[ 'pass' ] ?? null );
		$this->setPort( $parts[ 'port' ] ?? null );
		$this->setPath( $parts[ 'path' ] ?? null );
		$this->setQuery( $parts[ 'query' ] ?? null );
		$this->setFragment( $parts[ 'fragment' ] ?? null );
	}

	/**
	 * Validates the original URL.
	 *
	 * @param string $url the URL to be validated.
	 * @throws InvalidArgumentException if the URL is invalid.
	 */
	static public function isValid( string $url ): void
	{
		if( ! filter_var( $url, FILTER_VALIDATE_URL ) )
		{
			throw new InvalidArgumentException( "Invalid URL: {$url}" );
		}
	}

	/**
	 * Normalizes the given URL.
	 *
	 * If the given URL does not contain a scheme, and the Url::AutoDetectScheme
	 * option is enabled, this method adds a scheme of "http://" to the URL.
	 *
	 * @param string $url the URL to be normalized
	 * @return string the normalized URL
	 */
	private function normalize( string $url ): string
	{
		if( $this->options & self::AutoDetectScheme )
		{
			if( ! parse_url( $url, PHP_URL_SCHEME ))
			{
				$url = 'http://' . $url;
			}
		}

		return $url;
	}

	/**
	 * Sets the scheme of the URL.
	 *
	 * @param string $scheme the scheme to be set
	 * @return self the current instance of the Url class
	 */
	public function setScheme( string $scheme ): self
	{
		$this->scheme->set( $scheme );
		return $this;
	}

	/**
	 * Sets the username of the user.
	 * 
	 * @param string $user the username to be set
	 * @return self the current instance of the Url class
	 */
	public function setUsername( ?string $user = null ): self
	{
		$this->auth->setUser( $user );
		return $this;
	}

	/**
	 * Sets the password of the user.
	 * 
	 * @param string|null $pass The password to be set, or null to unset.
	 * @return self The current instance of the Url class for method chaining.
	 */
	public function setPassword( ?string $pass = null ): self
	{
		$this->auth->setPass( $pass );
		return $this;
	}

	/**
	 * Sets the host of the URL.
	 *
	 * If the given host is null, the host is reset to null.
	 * Otherwise, the given host is set as the host.
	 *
	 * @param ?string $host The host to be set, or null to unset.
	 * @return self The current instance of the Url class.
	 */
	public function setHost( ?string $host = null ): self
	{
		$this->host->set( $host );
		return $this;
	}

	/**
	 * Sets the port number of the URL.
	 *
	 * If the given port number is null, the port number is reset to null.
	 * Otherwise, the given port number is set as the port number.
	 *
	 * @param ?int $port The port number to set, or null to unset.
	 * @return self The current instance of the Url class.
	 */
	public function setPort( ?int $port = null ): self
	{
		$this->port->set( $port );
		return $this;
	}

	/**
	 * Sets the path of the URL.
	 *
	 * If the given path is null, the path is reset to its default state.
	 * Otherwise, the given path is set as the path.
	 *
	 * @param ?string $path The path to set, or null to unset.
	 * @return self The current instance of the Url class for method chaining.
	 */
	public function setPath( ?string $path ): self
	{
		$this->path->set( $path );
		return $this;
	}
	
	/**
	 * Sets the query string of the URL.
	 *
	 * If the given query string is null, the query string is reset to an empty string.
	 * Otherwise, the given query string is parsed and set as the query string.
	 *
	 * @param string|null $query The query string to set, or null to unset.
	 * @return self The current instance of the Url class for method chaining.
	 */
	public function setQuery( ?string $query = null ): self
	{
		$this->query->setRaw( $query );
		return $this;
	}

	/**
	 * Sets the fragment of the URL.
	 *
	 * If the given fragment is null, the fragment is reset to null.
	 * Otherwise, the given fragment is set as the fragment.
	 *
	 * @param string|null $fragment The fragment to be set, or null to unset.
	 * @return self The current instance of the Url class for method chaining.
	 */
	public function setFragment( ?string $fragment = null ): self
	{
		$this->fragment->set( $fragment );
		return $this;
	}

	/**
	 * Converts the Url object to a string.
	 *
	 * @return string the string representation of the Url object
	 */
	public function __toString()
	{
		return $this->scheme . $this->auth . $this->host . $this->port .
			   $this->path . $this->query . $this->fragment;
	}

	/**
	 * Converts the Url object to an associative array.
	 *
	 * @return array An associative array with the following keys:
	 *               - scheme: The scheme of the URL.
	 *               - user: The username of the URL.
	 *               - pass: The password of the URL.
	 *               - host: The host of the URL.
	 *               - port: The port number of the URL.
	 *               - path: The path of the URL.
	 *               - query: The query string of the URL.
	 *               - fragment: The fragment of the URL.
	 */
	public function toArray(): array
	{
		return [
			'scheme'   => (string) $this->scheme,
			'user'     => $this->auth->getUser(),
			'pass'     => $this->auth->getPass(),
			'host'     => (string) $this->host,
			'port'     => $this->port->get(),
			'path'     => (string) $this->path,
			'query'    => (string) $this->query,
			'fragment' => (string) $this->fragment,
		];
	}

	/**
	 * Creates a Url object from an associative array of URL components.
	 *
	 * This method takes an associative array containing URL components
	 * such as scheme, user, pass, host, port, path, query, and fragment,
	 * and sets these components to a new Url instance. If a component
	 * is present in the array, it will be set on the Url object.
	 *
	 * @param array $parts An associative array with the following optional keys:
	 *                     - scheme: The scheme of the URL.
	 *                     - user: The username for authentication.
	 *                     - pass: The password for authentication.
	 *                     - host: The host of the URL.
	 *                     - port: The port number of the URL.
	 *                     - path: The path of the URL.
	 *                     - query: The query string of the URL.
	 *                     - fragment: The fragment of the URL.
	 * @return self A Url object with components set from the provided array.
	 */
	public static function fromArray(array $parts): self
	{
		$url = new self;

		isset( $parts['scheme']) && $url->setScheme( $parts['scheme']);
		isset( $parts['user']) && $url->setUsername( $parts['user']);
		isset( $parts['pass']) && $url->setPassword( $parts['pass']);
		isset( $parts['host']) && $url->setHost( $parts['host']);
		isset( $parts['port']) && $url->setPort((int) $parts['port']);
		isset( $parts['path']) && $url->setPath( $parts['path']);
		isset( $parts['query']) && $url->setQuery( $parts['query']);
		isset( $parts['fragment']) && $url->setFragment( $parts['fragment']);

		return $url;
	}
}
