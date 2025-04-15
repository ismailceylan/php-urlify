<?php

namespace Iceylan\Urlify;

use InvalidArgumentException;
use Iceylan\Urlify\Path\Path;

class Url
{
	/**
	 * The original URL.
	 * 
	 * @var string
	 */
	private string $original;

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
	public function __construct( string $url, int $options = 0 )
	{
		$this->options = $options;
		$this->original = $this->normalize( $url );
		$this->parts = parse_url( $this->original );

		$this->scheme = new Scheme( $this->part( 'scheme', '' ));
		$this->host = new Host( $this->part( 'host' ));

		$this->auth = new Auth(
			$this->part( 'user', null ),
			$this->part( 'pass', null ) 
		);

		$this->port = new Port(
			$this->part( 'port', null ),
			$this->scheme
		);

		$this->path = new Path(
			$this->part( 'path', '' )
		);

		$this->validate();
	}

	/**
	 * Returns the value of the given key from the parsed URL.
	 * 
	 * @param string $key the key to be retrieved
	 * @param mixed $default the default value if the key does not exist
	 * @return string|null the value of the given key if it exists, null otherwise
	 */
	private function part( string $key, mixed $default = null ): ?string
	{
		return $this->parts[ $key ] ?? $default;
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
	 * Validates the original URL.
	 *
	 * @throws InvalidArgumentException if the URL is invalid.
	 */
	protected function validate(): void
	{
		if( ! filter_var( $this->original, FILTER_VALIDATE_URL ) )
		{
			throw new InvalidArgumentException( "Invalid URL: {$this->original}" );
		}
	}

}
