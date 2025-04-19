<?php

namespace Iceylan\Urlify;

use JsonSerializable;

/**
 * Represents authentication parts of a URL.
 */
class Auth implements JsonSerializable
{
	/**
	 * The username of the user.
	 * 
	 * @var string
	 */
	private ?string $user;

	/**
	 * The password of the user.
	 * 
	 * @var string
	 */
	private ?string $pass;

	/**
	 * Constructs a new Auth object.
	 * 
	 * @param string|null $user the username of the user
	 * @param string|null $pass the password of the user
	 */
	public function __construct( ?string $user = null, ?string $pass = null )
	{
		$this->user = $user == '' ? null : $user;
		$this->pass = $pass == '' ? null : $pass;
	}

	/**
	 * Gets the username of the user.
	 *
	 * @return string|null The username of the user if set, null otherwise.
	 */
	public function getUser(): ?string
	{
		return $this->user;
	}

	/**
	 * Gets the password of the user.
	 *
	 * @return string|null The password of the user if set, null otherwise.
	 */
	public function getPass(): ?string
	{
		return $this->pass;
	}

	/**
	 * Sets the username of the user.
	 * 
	 * @param ?string $user The username of the user.
	 * @return $this The object itself, for method chaining.
	 */
	public function setUser( ?string $user ): self
	{
		$this->user = $user;
		return $this;
	}

	/**
	 * Sets the password of the user.
	 *
	 * @param ?string $pass The password of the user.
	 * @return $this The object itself, for method chaining.
	 */
	public function setPass( ?string $pass ): self
	{
		$this->pass = $pass;
		return $this;
	}

	/**
	 * Sets both the username and password for the Auth object.
	 *
	 * @param string|null $user The username to set, or null to unset.
	 * @param string|null $pass The password to set, or null to unset.
	 * @return $this The object itself, for method chaining.
	 */
	public function set( ?string $user = null, ?string $pass = null ): self
	{
		$this->user = $user;
		$this->pass = $pass;
		return $this;
	}

	/**
	 * Checks if the Auth object has either a username or a password set.
	 * 
	 * @return bool True if either the username or the password is set, false otherwise.
	 */
	public function hasAuth(): bool
	{
		return $this->user !== null || $this->pass !== null;
	}

	/**
	 * Converts the Auth object to a string.
	 *
	 * If the Auth object has both a username and a password set, the method
	 * returns a string of the form "username:password@". If the Auth object has
	 * only a username set, the method returns a string of the form "username@".
	 * If the Auth object has only a password set, the method returns a string of
	 * the form ":password@". If the Auth object has neither a username nor a
	 * password set, the method returns an empty string.
	 *
	 * @return string The string representation of the Auth object.
	 */
	public function __toString()
	{
		$t = '';

		if( $this->user !== null )
		{
			$t .= $this->user;
		}

		if( $this->pass !== null )
		{
			$t .= ':' . $this->pass;
		}

		if( $t !== '' )
		{
			$t = "$t@";
		}

		return $t;
	}

	/**
	 * Returns the authentication parts of the URL as an associative array.
	 *
	 * The returned array has two keys, "user" and "pass", which are the username and the password,
	 * respectively. If either of them is not set, the corresponding value will be null.
	 *
	 * @return array The authentication parts of the URL as an associative array.
	 */
	public function jsonSerialize(): array
	{
		return [
			'user' => $this->user,
			'pass' => $this->pass,
		];
	}
}
