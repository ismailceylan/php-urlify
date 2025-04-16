<?php

namespace Iceylan\Urlify\Query;

/**
 * Represents a query entry of a URL.
 */
class QueryEntry
{
	/**
	 * The key of the query entry.
	 *
	 * @var string
	 */
	public string $key;

	/**
	 * The value of the query entry.
	 *
	 * @var string
	 */
	public string $value;
	
	/**
	 * Indicates whether the query entry is a flag.
	 *
	 * @var boolean
	 */
	public bool $isFlag = false;

	/**
	 * Constructs a new QueryEntry object.
	 *
	 * @param string $key    the key of the query entry
	 * @param string $value  the value of the query entry
	 * @param bool   $isFlag true if the query entry is a flag, false otherwise
	 */
	public function __construct( string $key, string $value, bool $isFlag = false )
	{
		$this->key = $key;
		$this->value = $value;
		$this->isFlag = $isFlag;
	}

	/**
	 * Returns a string representation of the query entry.
	 *
	 * @return string
	 */
	public function __toString(): string
	{
		return $this->isFlag
			? $this->key
			: "{$this->key}={$this->value}";
	}

	/**
	 * Sets the flag of the query entry.
	 *
	 * @param bool $flag true if the query entry is a flag, false otherwise
	 * @return self
	 */
	public function setFlag( bool $flag ): self
	{
		$this->isFlag = $flag;
		return $this;
	}
}
