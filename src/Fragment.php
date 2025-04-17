<?php

namespace Iceylan\Urlify;

use JsonSerializable;
use Iceylan\Urlify\Query\Query;

/**
 * Represents a fragment of a URL.
 */
class Fragment implements JsonSerializable
{
	/**
	 * The fragment of the URL.
	 *
	 * @var ?string
	 */
	private ?string $fragment;

	/**
	 * Constructs a new Fragment object.
	 * 
	 * @param string|null $fragment the fragment of the URL
	 */
	public function __construct( ?string $fragment = null )
	{
		$this->fragment = $fragment;
	}

	/**
	 * Retrieves the fragment of the URL.
	 *
	 * @return ?string The fragment of the URL, or null if not set.
	 */
	public function get(): ?string
	{
		return $this->fragment;
	}

	/**
	 * Sets the fragment of the URL.
	 * 
	 * @param string $fragment the new fragment of the URL
	 * @return self This object, for method chaining.
	 */
	public function set( string $fragment ): self
	{
		$this->fragment = $fragment;
		return $this;
	}

	/**
	 * Clears the fragment of the URL.
	 * 
	 * @return self This object, for method chaining.
	 */
	public function clear(): self
	{
		$this->fragment = null;
		return $this;
	}

	/**
	 * Determines if the fragment of the URL is empty.
	 * 
	 * A fragment is considered empty if it is null.
	 * 
	 * @return bool True if the fragment is empty, otherwise false.
	 */
	public function isEmpty(): bool
	{
		return $this->fragment === null;
	}

	/**
	 * Encodes the fragment of the URL.
	 * 
	 * @return string The encoded fragment of the URL, or an empty string if the fragment is empty.
	 */
	public function encode(): string
	{
		return $this->fragment
			? rawurlencode( $this->fragment )
			: '';
	}

	/**
	 * Returns the encoded fragment of the URL as a string.
	 *
	 * This method is invoked when the object is treated as a string, such as when
	 * used in string concatenation.
	 *
	 * @return string The encoded fragment of the URL, or an empty string if the
	 *                fragment is empty.
	 */
	public function __toString()
	{
		return $this->isEmpty()
			? ''
			: '#' . $this->encode();
	}

	/**
	 * Checks if this fragment is equal to another fragment.
	 * 
	 * @param Fragment $fragment the other fragment to compare with
	 * @return bool true if the two fragments are equal, otherwise false
	 */
	public function equals( Fragment $fragment ): bool
	{
		return $this->fragment === $fragment->get();
	}

	/**
	 * Retrieves the fragment of the URL as a Query object.
	 *
	 * @return Query|null The fragment of the URL as a Query object.
	 */
	public function asQuery(): Query|null
	{
		return strpos( $this->fragment, '&' ) !== false
			? new Query( $this->fragment )
			: null;
	}

	/**
	 * Returns the fragment of the URL as an associative array.
	 *
	 * The associative array contains two keys:
	 *
	 * - `'fragment'`: the fragment of the URL as a string
	 * - `'asQuery'`: the fragment of the URL as a Query object
	 *
	 * @return array The fragment of the URL as an associative array.
	 */
	public function toArray(): array
	{
		return [
			'fragment' => $this->get(),
			'asQuery' => $this->asQuery(),
		];
	}

	/**
	 * Serializes the fragment of the URL to a format suitable for JSON encoding.
	 *
	 * This method is used to specify the data that should be serialized to JSON
	 * when the Fragment object is encoded using json_encode().
	 *
	 * @return array The fragment of the URL as an associative array, suitable
	 *               for JSON encoding.
	 */
	public function jsonSerialize(): array
	{
		return $this->toArray();
	}
}
