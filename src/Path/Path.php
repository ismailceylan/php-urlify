<?php

namespace Iceylan\Urlify\Path;

use JsonSerializable;
use Iceylan\Urlify\Query\Query;
use Iceylan\Urlify\Path\Segment\CurrentSegment;
use Iceylan\Urlify\Path\Segment\EmptySegment;
use Iceylan\Urlify\Path\Segment\NormalSegment;
use Iceylan\Urlify\Path\Segment\ParentSegment;
use Iceylan\Urlify\Path\Segment\SegmentCollection;

/**
 * Represents a path of a URL.
 */
class Path implements JsonSerializable
{
	/**
	 * The segments of the path.
	 *
	 * @var SegmentCollection
	 */
	private SegmentCollection $segments;

	/**
	 * Constructs a new Path object.
	 *
	 * @param ?string $path the path of the URL
	 */
	public function __construct( ?string $path = null )
	{
		$this->set( $path );
	}

	/**
	 * Converts the Path object to a string.
	 *
	 * This method returns a string representation of the path of the URL, with
	 * empty segments removed and normalized.
	 *
	 * @return string The string representation of the path.
	 */
	public function __toString(): string
	{
		return $this->isEmpty()
			? ''
			: '/' . implode( '/', $this->getNormalizedSegments());
	}

	/**
	 * Sets the path of the URL.
	 *
	 * This method parses the given path into segments and updates the
	 * current path segments with the parsed segments.
	 *
	 * @param ?string $path The path to set.
	 * @return self The current instance for method chaining.
	 */
	public function set( ?string $path ): self
	{
		$this->segments = $this->parseSegments( $path );
		return $this;
	}

	/**
	 * Parses the given path and returns a SegmentCollection.
	 *
	 * @param string $path The path to parse.
	 * @return SegmentCollection The parsed segments.
	 */
	private function parseSegments( ?string $path = null ): SegmentCollection
	{
		$collection = new SegmentCollection;

		if( $path === null )
		{
			return $collection;
		}

		$drivers = [ CurrentSegment::class, EmptySegment::class, NormalSegment::class, ParentSegment::class ];
		$parts = explode( '/', $path );

		foreach( $parts as $part )
		{
			foreach( $drivers as $driver )
			{
				if( $driver::test( $part ))
				{
					$collection->push( new $driver( $part ));
					break;
				}
			}
		}

		return $collection;
	}

	/**
	 * Returns the path of the URL.
	 *
	 * @return string The path of the URL.
	 */
	public function get(): string
	{
		return $this->segments->getSanitizedSegments();
	}

	/**
	 * Returns the original path of the URL, without any trimming.
	 *
	 * @return string The original path of the URL.
	 */
	public function getRaw(): string
	{
		return $this->segments;
	}

	/**
	 * Returns the fixed path of the URL, with empty segments removed.
	 *
	 * This method returns a string representing the path of the URL, with
	 * empty segments removed. This is useful for normalizing relative paths.
	 *
	 * @return string The fixed path of the URL.
	 */
	public function getFixed(): string
	{
		return implode( '/', $this->getSegments());
	}

	/**
	 * Returns the segments of the path, without empty segments.
	 *
	 * This method returns an array of strings, where each string is a segment
	 * of the path. Empty segments are excluded from the result.
	 *
	 * @return array An array of strings, where each string is a segment of the path.
	 */
	public function getSegments(): array
	{
		return $this->segments
			->getNotEmptySegments()
			->map( fn( $segment ) => $segment->getValue())
			->all();
	}

	/**
	 * Returns the normalized segments of the path.
	 *
	 * Normalization removes empty segments and collapses double slashes and
	 * relative path segments. For example, "./foo/bar////../baz" becomes "/foo/baz".
	 *
	 * @return array The normalized segments of the path.
	 */
	public function getNormalizedSegments(): array
	{
		$normalized = [];

		foreach( $this->segments->getSanitizedSegments()->all() as $segment )
		{
			if( $segment instanceof ParentSegment )
			{
				if( ! empty( $normalized ))
				{
					$last = end( $normalized );

					if( $last instanceof NormalSegment )
					{
						array_pop( $normalized );
						continue;
					}
				}
	
				continue;
			}
	
			$normalized[] = $segment;
		}

		return ( new SegmentCollection( $normalized ))
			->map( fn( $segment ) => $segment->getValue())
			->all();
	}

	/**
	 * Returns the resolved path.
	 *
	 * The resolved path is a normalized path that has been collapsed to remove
	 * any relative segments. For example, "/foo/bar/../baz" becomes "/foo/baz".
	 *
	 * @return string The resolved path.
	 */
	public function resolve(): string
	{
		return implode( '/', $this->getNormalizedSegments());
	}

	/**
	 * Returns a portion of the path as a string based on the specified start index
	 * and optional length.
	 *
	 * This method slices the array of segments and joins them into a path string.
	 * If the length is not specified, it extracts all segments from the start index
	 * to the end. The method uses zero-based indexing for the start parameter.
	 *
	 * @param int $start The starting index of the slice.
	 * @param int|null $length The number of segments to include in the slice. If null,
	 *                         includes all segments from the start index onwards.
	 * @return string A string representing the sliced portion of the path.
	 */
	public function slice( int $start, ?int $length = null ): string
	{
		return implode( '/', array_slice( $this->getSegments(), $start, $length ));
	}

	/**
	 * Checks if the current path is a prefix of the given path.
	 *
	 * The method checks if the current path is a prefix of the given path.
	 * This is useful for determining if a URL is a prefix of another URL.
	 *
	 * @param Path $path The path to check against.
	 * @return bool True if the current path is a prefix of the given path, false otherwise.
	 */
	public function isPrefixOf( Path $path ): bool
	{
		return str_starts_with( $this->get(), $path->get());
	}

	/**
	 * Checks if the current path is absolute.
	 *
	 * The method checks if the current path is absolute by checking if the first
	 * segment is an empty segment. An absolute path is one that starts with a
	 * leading slash, which is represented by an empty segment.
	 *
	 * @return bool True if the current path is absolute, false otherwise.
	 */
	public function isAbsolute(): bool
	{
		return $this->segments->get( 0 ) instanceof EmptySegment;
	}

	/**
	 * Returns the segment at the specified index.
	 *
	 * @param int $index the index of the segment
	 * @return string|null the segment at the specified index, or null if no segment exists at that index
	 */
	public function getSegment( int $index ): ?string
	{
		return $this->segments
			->getNotEmptySegments()
			->get( $index )
			?->getValue();
	}

	/**
	 * Converts the segment at the specified index into a Query object.
	 *
	 * The segment is retrieved from the path and returned as a Query object. 
	 * Optionally, a separator and equals sign can be specified for formatting 
	 * purposes, though they are not currently utilized in the function.
	 *
	 * @param int $index The index of the segment to convert.
	 * @param string|null $seperator The separator to use (optional).
	 * @param string|null $equals The equals sign to use (optional).
	 * @return Query A Query object representing the segment.
	 */
	public function getSegmentAsQuery(
		int $index,
		?string $seperator = null,
		?string $equals = null
	): Query
	{
		return new Query( $this->getSegment( $index, ), $seperator, $equals );
	}

	/**
	 * Returns the normalized segment at the specified index.
	 *
	 * Normalization removes empty segments and collapses double slashes and
	 * relative path segments. For example, "/foo/bar/../baz" becomes "/foo/baz".
	 *
	 * @param int $index the index of the segment
	 * @return string|null the normalized segment at the specified index, or null if no segment exists at that index
	 */
	public function getNormalizedSegment( int $index ): ?string
	{
		return $this->getNormalizedSegments()[ $index ] ?? null;
	}

	/**
	 * Returns the number of segments in the path.
	 *
	 * @return int the number of segments in the path
	 */
	public function getSegmentsCount(): int
	{
		return count( $this->getSegments());
	}

	/**
	 * Returns the number of normalized segments in the path.
	 *
	 * Normalization removes empty segments and collapses double slashes and
	 * relative path segments. For example, "/foo/bar/../baz" becomes "/foo/baz".
	 *
	 * @return int the number of normalized segments in the path
	 */
	public function getNormalizedSegmentsCount(): int
	{
		return count( $this->getNormalizedSegments());
	}

	/**
	 * Checks if the path is empty.
	 *
	 * A path is considered empty if it has no segments.
	 *
	 * @return bool true if the path is empty, false otherwise
	 */
	public function isEmpty(): bool
	{
		return $this->getSegmentsCount() === 0;
	}

	/**
	 * Checks if the normalized path is empty.
	 *
	 * A normalized path is considered empty if it has no segments after
	 * normalization. Normalization removes empty segments and collapses double
	 * slashes and relative path segments. For example, "/foo/bar/../baz" becomes
	 * "/foo/baz".
	 *
	 * @return bool true if the normalized path is empty, false otherwise
	 */
	public function isNormalizedEmpty(): bool
	{
		return $this->getNormalizedSegmentsCount() === 0;
	}

	/**
	 * Prepends a segment to the beginning of the path.
	 *
	 * @param string $segment the segment to prepend
	 * @return static the current instance for method chaining
	 */
	public function prepend( string $segment ): self
	{
		$this->segments->prepend( $segment );
		return $this;
	}

	/**
	 * Appends a segment to the end of the path.
	 *
	 * @param string $segment The segment to append.
	 * @return self The current instance for method chaining.
	 */
	public function append( string $segment ): self
	{
		$this->segments->append( $segment );
		return $this;
	}

	/**
	 * Inserts a segment at the specified index in the path.
	 *
	 * @param int $index The index at which to insert the segment.
	 * @param string $segment The segment to insert.
	 * @return self The current instance for method chaining.
	 */
	public function insertAt( int $index, string $segment ): self
	{
		$this->segments->insertAt( $index, $segment );
		return $this;
	}

	/**
	 * Replaces the segment at the specified index with the given segment.
	 *
	 * @param int $index The index at which to replace the segment.
	 * @param string $segment The segment to replace the existing segment with.
	 * @return self The current instance for method chaining.
	 */
	public function replaceAt( int $index, string $segment ): self
	{
		$this->segments->replaceAt( $index, $segment );
		return $this;
	}
	
	/**
	 * Removes the segment at the specified index from the path.
	 *
	 * If the index is out of range, nothing happens.
	 *
	 * @param int $index The index of the segment to remove.
	 * @return self The current instance for method chaining.
	 */
	public function removeAt( int $index ): self
	{
		$this->segments->removeAt( $index );
		return $this;
	}

	/**
	 * Converts the Path object to an array.
	 *
	 * @return array An array of segments in the path.
	 */
	public function toArray(): array
	{
		return $this->segments
			->map( fn( $segment ) =>
				$segment->getValue()
			)
			->all();
	}

	/**
	 * Converts the Path object to an array for JSON serialization.
	 *
	 * The resulting array contains:
	 * - 'rawSegments': an array of the original segments in the path.
	 * - 'resolvedSegments': an array of the normalized segments in the path.
	 *
	 * @return array The serialized representation of the Path object.
	 */
	public function jsonSerialize(): array
	{
		return
		[
			'rawSegments' => $this->toArray(),
			'resolvedSegments' => $this->getNormalizedSegments(),

		];
	}
}
