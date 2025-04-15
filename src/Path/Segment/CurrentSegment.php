<?php

namespace Iceylan\Urlify\Path\Segment;

/**
 * Represents a current segment sign in a path.
 */
final class CurrentSegment implements SegmentInterface
{
	/**
	 * Checks if the given segment is a current segment.
	 *
	 * A current segment is represented by a single dot ('.').
	 *
	 * @param string $segment The segment to test.
	 * @return bool Returns true if the segment is a current segment, otherwise false.
	 */
	public static function test( string $segment ): bool
	{
		return $segment === '.';
	}

	/**
	 * Returns the value of the segment.
	 *
	 * @return string The value of the segment, which is '.'.
	 */
	public function getValue(): string
	{
		return '.';
	}

	/**
	 * Checks if the segment is navigational.
	 *
	 * @return bool Always returns true as current segments are navigational.
	 */
	public function isNavigational(): bool
	{
		return true;
	}
}
