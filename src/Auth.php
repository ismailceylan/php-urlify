<?php

namespace Iceylan\Urlify;

/**
 * Represents authentication parts of a URL.
 */
class Auth
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
	public function __construct( ?string $user, ?string $pass )
	{
		$this->user = $user;
		$this->pass = $pass;
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
	 * Checks if the Auth object has either a username or a password set.
	 * 
	 * @return bool True if either the username or the password is set, false otherwise.
	 */
	public function hasAuth(): bool
	{
		return $this->user !== null || $this->pass !== null;
	}

	/**
	 * Returns the authentication parts of the URL as a string.
	 * 
	 * The returned string is in the format of "username:password".
	 * 
	 * @return string The authentication parts of the URL as a string.
	 */
	public function __toString()
	{
		return $this->user . ':' . $this->pass;
	}
}
