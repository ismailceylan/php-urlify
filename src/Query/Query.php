<?php

namespace Iceylan\Urlify\Query;

use JsonSerializable;
use SplDoublyLinkedList;

/**
 * Represents a query of a URL.
 */
class Query implements JsonSerializable
{
	/**
	 * The query entries.
	 *
	 * @var SplDoublyLinkedList
	 */
	private SplDoublyLinkedList $entries;

	/**
	 * Constructs a new Query object.
	 *
	 * @param ?string $query the original query string
	 * @param ?string $seperator the separator of the query string's segments
	 * @param ?string $equals the equals sign of the query string's segments
	 */
	public function __construct(
		?string $query = null,
		?string $seperator = '&',
		?string $equals = '='
	)
	{
		$this->entries = new SplDoublyLinkedList();

		if( $query )
		{
			$this->parse( $query, $seperator ?? '&', $equals ?? '=' );
		}
	}

	/**
	 * Returns a string representation of the query, which is a URL query string
	 * where each query entry is a key-value pair separated by an ampersand (&).
	 *
	 * @return string a URL query string
	 */
	public function __toString()
	{
		$stack = [];

		foreach( $this->entries as $entry )
		{
			$stack[] = $entry;
		}

		return empty( $stack )
			? ''
			: '?' . implode( "&", $stack );
	}

	/**
	 * Parses a query string into query entries.
	 *
	 * @param string $query  the query string to be parsed
	 * @param string $separator  the separator of the query string's segments
	 * @param string $equals  the separator of the query string's key-value pairs
	 */
	private function parse( string $query, string $separator, string $equals )
	{
		$segments = explode( $separator, $query );

		foreach( $segments as $segment )
		{
			$isFlag = strpos( $segment, '=' ) === false;
			$keypair = explode( $equals, $segment );

			// flags (foo) and empty values (foo=)
			if( count( $keypair ) === 1 )
			{
				$keypair[] = '';
			}

			$this->entries->push(
				( new QueryEntry( ...$keypair ))
					->setFlag( $isFlag ?? false )
			);
		}
	}

	/**
	 * Returns the value of the given key from the query entries.
	 *
	 * If the key does not exist, the second argument is returned. If the second
	 * argument is not provided, null is returned.
	 *
	 * @param string $key the key to be retrieved
	 * @param mixed $default the default value if the key does not exist
	 * @return string|null the value of the given key if it exists, null otherwise
	 */
	public function get( string $key, mixed $default = null ): ?string
	{
		$latest = null;

		foreach( $this->entries as $entry )
		{
			if( $entry->key === $key )
			{
				$latest = $entry->value;
			}
		}

		return $latest ?? $default;
	}

	/**
	 * Returns the value of the given key from the query entries as a new Query object.
	 *
	 * If the key does not exist, the forth ($default) argument is returned. If the default
	 * argument is not provided, null is returned.
	 *
	 * @param string $key the key to be retrieved
	 * @param string $seperator the separator of the query string's segments, defaults to '&'
	 * @param string $equals the separator of the query string's key-value pairs, defaults to '='
	 * @param mixed $default the default value if the key does not exist
	 * @return Query the value of the given key if it exists, null otherwise
	 */
	public function getAsQuery(
		string $key,
		?string $seperator = null,
		?string $equals = null,
		mixed $default = null
	): Query
	{
		return new Query( $this->get( $key, $default ), $seperator, $equals );
	}

	/**
	 * Retrieves all query entries in the query.
	 *
	 * @param string $key The key to search for in the query entries.
	 * @return array an associative array of key-value pairs, where
	 *               each key is the key of a query entry, and each
	 *               value is the value of the query entry
	 */
	public function getAll( string $key ): array
	{
		$values = [];

        foreach( $this->entries as $entry )
		{
            if( $entry->key === $key )
			{
                $values[] = $entry->value;
            }
        }

        return $values;
	}

	/**
	 * Checks if a query entry with the specified key exists.
	 *
	 * @param string $key The key to search for in the query entries.
	 * @return bool True if an entry with the key exists, false otherwise.
	 */
	public function has( string $key ): bool
	{
		return $this->index( $key ) !== false
			? true
			: false;
	}

	/**
	 * Returns the index of a query entry with the specified key,
	 * or false if no such entry exists.
	 *
	 * @param string $key The key to search for in the query entries.
	 * @return int|false The index of the query entry, or false
	 *                   if no such entry exists.
	 */
	public function index( string $key ): int|false
	{
		foreach( $this->entries as $index => $entry )
		{
			if( $entry->key === $key )
			{
				return $index;
			}
		}

		return false;
	}

	/**
	 * Adds a new query entry to the query.
	 *
	 * @param string|QueryEntry $key the key of the query entry
	 * @param string $value the value of the query entry
	 * @param bool $isFlag true if the query entry is a flag, false otherwise
	 * @return self
	 */
	public function add( string|QueryEntry $key, ?string $value = null, bool $isFlag = false ): self
	{
		if( $key instanceof QueryEntry )
		{
			$this->entries->push( $key );
		}
		else
		{
			$this->entries->push(
				( new QueryEntry( $key, $value ))
					->setFlag( $isFlag )
			);
		}

		return $this;
	}

	/**
	 * Adds a new query entry to the query, or updates the value of
	 * an existing query entry with the same key.
	 *
	 * @param string $key   the key of the query entry
	 * @param string $value the value of the query entry
	 * @return self
	 */
	public function set( string $key, string $value ): self
	{
		$this->remove( $key );
		$this->add( $key, $value );

		return $this;
	}

	/**
	 * Sets the query string of the query.
	 *
	 * If the given query string is null, the query string is reset to an empty string.
	 * Otherwise, the given query string is parsed and set as the query string.
	 *
	 * @param ?string $query the query string to set, or null to unset
	 * @param string $seperator the separator of the query string's segments
	 * @param string $equals the separator of the query string's key-value pairs
	 * @return self
	 */
	public function setRaw( ?string $query, string $seperator = '&', string $equals = '=' ): self
	{
		if( $query === null )
		{
			$this->clear();
		}
		else
		{
			$this->parse( $query, $seperator, $equals );
		}

		return $this;
	}

	/**
	 * Removes all query entries with the specified key from the query.
	 *
	 * @param string $key the key of the query entries to remove
	 * @return self
	 */
	public function remove( string $key ): self
	{
		while(( $index = $this->index( $key )) !== false )
		{
			$this->entries->offsetUnset( $index );
		}

		return $this;
	}

	/**
	 * Retrieves all unique keys of query entries in the query.
	 *
	 * @return array an array of all keys of query entries in the query
	 */
	public function keys(): array
	{
		$keys = [];

		foreach( $this->entries as $entry )
		{
			$keys[ $entry->key ] = true;
		}

		return array_keys( $keys );
	}

	/**
	 * Retrieves all unique values of query entries in the query.
	 *
	 * @return array an array of all values of query entries in the query
	 */
	public function values(): array
	{
		$values = [];

		foreach( $this->entries as $entry )
		{
			$values[ $entry->value ] = true;
		}

		return array_keys( $values );
	}

	/**
	 * Retrieves all keys of query entries in the query, including duplicates.
	 *
	 * @return array an array of all keys of query entries in the query
	 */
	public function allKeys(): array
	{
		$keys = [];

		foreach( $this->entries as $entry )
		{
			$keys[] = $entry->key;
		}

		return $keys;
	}

	/**
	 * Retrieves all query entries in the query, grouped by key.
	 *
	 * The returned array will have the key as the index and the value will be an array
	 * of all the values of the query entries with that key.
	 *
	 * @return array an associative array of key-value pairs, where
	 *               each key is the key of a query entry, and each
	 *               value is an array of all the values of query entries
	 *               with that key
	 */
	public function all(): array
	{
		$stack = [];

		foreach( $this->entries as $entry )
		{
			if( ! isset( $stack[ $entry->key ]))
			{
				$stack[ $entry->key ] = [];
			}

			$stack[ $entry->key ][] = $entry->value;
		}

		return $stack;
	}

	/**
	 * Checks if the query is empty.
	 *
	 * @return bool true if the query is empty, false otherwise
	 */
	public function isEmpty(): bool
	{
		return $this->entries->isEmpty();
	}

	/**
	 * Returns the number of query entries in the query.
	 *
	 * @return int the number of query entries
	 */
	public function count(): int
	{
		return $this->entries->count();
	}

	/**
	 * Clears the query by removing all query entries.
	 *
	 * @return self
	 */
	public function clear(): self
	{
		while( $this->entries->count() > 0 )
		{
			$this->entries->pop();
		}

		return $this;
	}

	/**
	 * Merges the given query into this query by adding all entries from the
	 * given query to this query. If a key already exists in this query, it
	 * will be overwritten by the value from the given query.
	 *
	 * @param Query $query the query to merge into this query
	 * @return self this query, with the entries from the given query merged in
	 */
	public function merge( Query $query ): self
	{
		foreach( $query->entries as $entry )
		{
			$this->add( $entry );
		}

		return $this;
	}

	/**
	 * Returns a new query containing all entries from this query that pass the
	 * given callback test.
	 *
	 * The callback function will be called with the following arguments:
	 * - The key of the query entry
	 * - The value of the query entry
	 * - A boolean indicating if the query entry is a flag
	 * - The index of the query entry in the query
	 *
	 * If the callback function returns true, the query entry will be included in
	 * the returned query, otherwise it will be excluded.
	 *
	 * @param callable $callback the callback function to use to filter the query
	 * @return Query a new query containing all entries from this query that pass
	 *         the given callback test
	 */
	public function filter( callable $callback ): Query
	{
		$clone = new static;

		foreach( $this->entries as $index => $entry )
		{
			if( $callback( $entry->key, $entry->value, $entry->isFlag, $index ))
			{
				$clone->add( $entry );
			}
		}

		return $clone;
	}

	/**
	 * Applies a callback function to each entry in the query and returns a new
	 * query with the modified entries.
	 *
	 * The callback function will be called with the following arguments:
	 * - The key of the query entry
	 * - The value of the query entry
	 * - A boolean indicating if the query entry is a flag
	 * - The index of the query entry in the query
	 *
	 * The return value of the callback function will be used as the new entry
	 * in the returned query.
	 *
	 * @param callable $callback The callback function to apply to each query entry.
	 * @return Query A new query with entries resulting from the callback function.
	 */
	public function map( callable $callback ): Query
	{
		$clone = new static;

		foreach( $this->entries as $index => $entry )
		{
			$clone->add(
				$callback( $entry->key, $entry->value, $entry->isFlag, $index )
			);
		}

		return $clone;
	}

	/**
	 * Returns the query entries as an array.
	 *
	 * @return array the query entries as an array
	 */
	public function toArray(): array
	{
		return $this->all();
	}

	/**
	 * Returns the query entries as an array that can be serialized to JSON.
	 *
	 * @return array the query entries as an array
	 */
	public function jsonSerialize(): array
	{
		return $this->toArray();
	}
}
