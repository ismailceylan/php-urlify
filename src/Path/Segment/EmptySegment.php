<?php

namespace Iceylan\Urlify\Path\Segment;

/**
 * Represents an empty segment of a path.
 */
final class EmptySegment implements SegmentInterface
{
	/**
	 * Tests if the given segment is an empty string.
	 *
	 * @param string $segment The segment to test.
	 * @return bool True if the segment is empty, false otherwise.
	 */
	public static function test( string $segment ): bool
	{
		return $segment === '';
	}

	/**
	 * Returns the value of the segment.
	 *
	 * The value of an empty segment is always an empty string.
	 *
	 * @return string The value of the segment.
	 */
	public function getValue(): string
	{
		return '';
	}

	/**
	 * Checks if the segment is navigational.
	 *
	 * An empty segment is not navigational, so this method always returns false.
	 *
	 * @return bool Always returns false as empty segments are not navigational.
	 */
	public function isNavigational(): bool
	{
		return false;
	}
}
