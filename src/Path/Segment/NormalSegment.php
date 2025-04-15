<?php

namespace Iceylan\Urlify\Path\Segment;

/**
 * Represents a normal segment of a path.
 */
final class NormalSegment implements SegmentInterface
{
	/**
	 * The value of the segment.
	 *
	 * @var string
	 */
	private string $value;

	/**
	 * Checks if the given segment is a normal segment.
	 *
	 * A normal segment is any segment that is not an empty string, a current segment
	 * or a parent segment.
	 *
	 * @param string $segment The segment to test.
	 * @return bool Returns true if the segment is a normal segment, false otherwise.
	 */
	public static function test( string $segment ): bool
	{
		return ! in_array( $segment, [ '', '.', '..' ]);
	}

    /**
     * Initializes a new instance of the NormalSegment class.
     *
     * @param string $value The value of the segment.
     */
	public function __construct( string $value )
	{
		$this->value = $value;
	}

    /**
     * Returns the value of the segment.
     *
     * @return string The value of the segment.
     */
	public function getValue(): string
	{
		return $this->value;
	}

    /**
     * Checks if the segment is navigational.
     *
     * @return bool Always returns false as normal segments are not navigational.
     */
	public function isNavigational(): bool
	{
		return false;
	}
}
