<?php

namespace Iceylan\Urlify\Path\Segment;

/**
 * Represents a collection of segments of a path.
 */
class SegmentCollection
{
	/**
	 * The segments of the path.
	 *
	 * @var array
	 */
	private array $segments = [];

	/**
	 * Constructs a new SegmentCollection object.
	 *
	 * @param array $segments The segments to add to the collection.
	 */
	public function __construct( array $segments = [])
	{
		$this->segments = $segments;
	}

	/**
	 * Retrieves all segments in the collection.
	 *
	 * @return array An array of segments contained in the collection.
	 */
	public function all(): array
	{
		return $this->segments;
	}

	/**
	 * Pushes a segment into the collection.
	 *
	 * @param SegmentInterface $segment The segment to push into the collection.
	 * @return static The current instance for method chaining.
	 */
	public function push( SegmentInterface $segment ): self
	{
		$this->segments[] = $segment;
		return $this;
	}

	/**
	 * Creates a new SegmentCollection containing only the segments that pass the
	 * given test implemented by the provided function.
	 *
	 * @param callable $callback The callback function to use when testing each
	 *     segment. It takes a SegmentInterface as its only argument and must
	 *     return a boolean value.
	 * @return SegmentCollection A new SegmentCollection containing only the
	 *     segments that pass the test implemented by the provided function.
	 */
	public function filter( callable $callback ): SegmentCollection
	{
		return new static([ ...array_filter( $this->segments, $callback )]);
	}

	/**
	 * Applies the given callback function to each segment in the collection and
	 * returns a new SegmentCollection containing the results.
	 *
	 * @param callable $callback The callback function to apply to each segment in
	 *     the collection. It takes a SegmentInterface as its only argument and
	 *     must return a SegmentInterface.
	 * @return SegmentCollection A new SegmentCollection containing the results
	 *     of applying the given callback function to each segment in the
	 *     collection.
	 */
	public function map( callable $callback ): SegmentCollection
	{
		return new static([ ...array_map( $callback, $this->segments )]);
	}

	/**
	 * Retrieves a segment from the collection by index.
	 *
	 * @param int $index The index of the segment to retrieve.
	 * @return SegmentInterface|null The segment at the given index, or null if
	 *     the index is out of range.
	 */
	public function get( int $index ): SegmentInterface|null
	{
		return $this->segments[ $index ] ?? null;
	}

	/**
	 * Retrieves a new SegmentCollection containing only non-empty segments.
	 *
	 * This method filters out any segments that are instances of EmptySegment
	 * and returns a collection with the remaining segments.
	 *
	 * @return SegmentCollection A collection of non-empty segments.
	 */
	public function getNotEmptySegments(): SegmentCollection
	{
		return $this->filter( fn( $segment ) =>
			! $segment instanceof EmptySegment
		);
	}

	/**
	 * Retrieves a new SegmentCollection containing only the sanitized segments
	 * in the original collection.
	 *
	 * A sanitized segment is a segment that is not an empty string, nor a
	 * current segment ('.'). This method is useful for collapsing relative
	 * paths.
	 *
	 * @return SegmentCollection A new SegmentCollection containing only the
	 *     sanitized segments in the original collection.
	 */
	public function getSanitizedSegments(): SegmentCollection
	{
		return $this
			->getNotEmptySegments()
			->filter( fn( $segment ) =>
				! ( $segment instanceof CurrentSegment )
			);
	}

	/**
	 * Prepends the given segment to the collection.
	 *
	 * This method prepends the given segment to the collection, and returns
	 * the current instance for method chaining.
	 *
	 * @param string $segment The segment to prepend.
	 * @return self The current instance for method chaining.
	 */
	public function prepend( string $segment ): self
	{
		array_unshift( $this->segments, $this->stringToSegment( $segment ));
		return $this;
	}

	/**
	 * Appends the given segment to the collection.
	 *
	 * This method appends the given segment to the collection, and returns
	 * the current instance for method chaining.
	 *
	 * @param string $segment The segment to append.
	 * @return self The current instance for method chaining.
	 */
	public function append( string $segment ): self
	{
		$this->segments[] = $this->stringToSegment( $segment );
		return $this;
	}

	/**
	 * Inserts a segment at the specified index in the collection.
	 *
	 * This method inserts the given segment at the specified index in the
	 * collection. If the index is greater than the length of the collection,
	 * the segment is appended to the end of the collection.
	 *
	 * @param int $index The index at which to insert the segment.
	 * @param string $segment The segment to insert.
	 * @return self The current instance for method chaining.
	 */
	public function insertAt( int $index, string $segment ): self
	{
		array_splice(
			$this->segments,
			$this->normalizeNegativeIndex( $index ),
			0,
			[ $this->stringToSegment( $segment )]
		);

		return $this;
	}

	/**
	 * Replaces the segment at the specified index in the collection with the
	 * given segment.
	 *
	 * If the index is negative, it is treated as an offset from the end of the
	 * collection. If the index is out of range, nothing happens.
	 *
	 * @param int $index The index of the segment to replace.
	 * @param string $segment The segment to replace the existing segment with.
	 * @return self The current instance for method chaining.
	 */
	public function replaceAt( int $index, string $segment ): self
	{

		$this->segments[ $this->normalizeNegativeIndex( $index )] =
			$this->stringToSegment( $segment );
		
		return $this;
	}

	/**
	 * Removes the segment at the specified index from the collection.
	 *
	 * This method removes the segment at the specified index from the collection.
	 * If the index is out of range, nothing happens.
	 *
	 * @param int $index The index of the segment to remove.
	 * @return self The current instance for method chaining.
	 */
	public function removeAt( int $index ): self
	{
		array_splice( $this->segments, $this->normalizeNegativeIndex( $index ), 1 );
		return $this;
	}

	/**
	 * Normalizes a negative index to a positive index relative to the collection.
	 *
	 * If the provided index is negative, it is converted to a positive index by adding
	 * the total number of segments in the collection. This allows for accessing elements
	 * from the end of the collection using negative indices.
	 *
	 * @param int $index The index to normalize, which may be negative.
	 * @return int The normalized positive index.
	 */
	private function normalizeNegativeIndex( int $index ): int
	{
		return $index < 0
			? $index += count( $this->segments )
			: $index;
	}

	/**
	 * Converts a string segment into a SegmentInterface.
	 *
	 * This method takes a string segment and converts it into a SegmentInterface
	 * object. If the segment is an empty string, a current segment, or a parent
	 * segment, the appropriate specialized SegmentInterface object is returned.
	 * Otherwise, a NormalSegment object is returned.
	 *
	 * @param string $segment The segment to convert.
	 * @return SegmentInterface The converted SegmentInterface object.
	 */
	private function stringToSegment( string $segment ): SegmentInterface
	{
		return match( $segment )
		{
			'' => new EmptySegment,
			'.' => new CurrentSegment,
			'..' => new ParentSegment,
			default => new NormalSegment( $segment )
		};
	}

	/**
	 * Converts the collection to a string.
	 *
	 * This method returns a string created by concatenating all the segment values
	 * in the collection, separated by forward slashes.
	 *
	 * @return string The string representation of the collection.
	 */
	public function __toString()
	{
		return implode(
			'/',
			array_map(
				fn( $segment ) => $segment->getValue(),
				$this->segments
			)
		);
	}
}
