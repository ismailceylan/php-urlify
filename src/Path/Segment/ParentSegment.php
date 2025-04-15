<?php

namespace Iceylan\Urlify\Path\Segment;

/**
 * Represents a parent segment sign in a path.
 */
final class ParentSegment implements SegmentInterface
{
	/**
	 * Tests if the given segment is a parent segment.
	 *
	 * @param string $segment The segment to test.
	 * @return bool True if the segment is a parent segment, false otherwise.
	 */
	public static function test( string $segment ): bool
	{
		return $segment === '..';
	}
	
	/**
	 * Returns the value of the segment.
	 *
	 * @return string The value of the segment, which is '..'.
	 */
	public function getValue(): string
	{
		return '..';
	}

	/**
	 * Checks if the segment is navigational.
	 *
	 * @return bool Always returns true as parent segments are navigational.
	 */
	public function isNavigational(): bool
	{
		return true;
	}
}
