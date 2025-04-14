<?php

namespace Iceylan\Urlify;

use InvalidArgumentException;

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

		$this->host = new Host( $this->parts[ 'host' ]);

		$this->auth = new Auth(
			$this->parts[ 'user' ] ?? null,
			$this->parts[ 'pass' ] ?? null
		);

		$this->validate();
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
