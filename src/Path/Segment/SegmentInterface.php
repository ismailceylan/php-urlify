<?php

namespace Iceylan\Urlify\Path\Segment;

/**
 * Represents a segment of a path.
 */
interface SegmentInterface
{
	/**
	 * Returns the value of the segment.
	 *
	 * @return string
	 */
    public function getValue(): string;

	/**
	 * Checks if the segment is navigational.
	 *
	 * @return boolean
	 */
    public function isNavigational(): bool;

	/**
	 * Checks if the segment accepts the given segment.
	 *
	 * @param string $segment
	 * @return boolean
	 */
	public static function test( string $segment ): bool;
}
