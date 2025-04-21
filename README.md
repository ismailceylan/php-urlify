# Urlify
**Urlify** is a lightweight, object-oriented PHP library designed for parsing, manipulating, and reconstructing URLs in a highly modular and intuitive way.

It provides fine-grained access to every component of a URL, such as scheme, authorization, host, path, query, and fragment through dedicated classes. Unlike typical URL parsers that only offer string-based access, Urlify structures these components as first-class objects, enabling more powerful and flexible manipulations.

---

## 🧩 Why Use Urlify?
URLs are often treated as plain strings, but in modern applications, we frequently need to:

- Add or remove query parameters
- Modify the fragment (`#`) portion of the URL
- Update only the path, keeping the query and host untouched
- Serialize the full URL again from its parts

Urlify abstracts all of this with clean, readable, and immutable-friendly interfaces, while giving you full control over the inner structure of the URL.

---

## 🧱 Architecture Overview
Urlify is built around a **component-based architecture**, where each part of the URL is encapsulated in its own class:

| Component | Class                   | Responsibility                                                  |
|----------:|-------------------------|------------------------------------------------------------------|
| Scheme    | `Scheme`                | Handles URL scheme (e.g., `http`, `https`, `ftp`, etc.)          |
| Host      | `Host`                  | Handles domain or IP address, subdomains, and port               |
| Path      | `Path`                  | Manages the path portion of the URL                              |
| Query     | `Query`                 | Represents the query string as a structured, iterable object     |
| Fragment  | `Fragment`              | Represents the URL fragment (`#...`), also compatible with Query |
| Entry     | `QueryEntry`            | Represents an individual key-value pair or flag in the query     |
| URL       | `Url`                   | Central orchestrator class that ties all parts together          |

All of these classes are designed to be loosely coupled and easily testable.

---

## 🔧 Primary Features
- **Full URL Parsing**  
  Create a `Url` object from a string and instantly access all parts via method calls.
- **Immutable-Friendly API**  
  Most methods support chaining, enabling fluent and predictable transformations.
- **Query Intelligence**  
  Flags (`?foo&bar=baz`) and key-value entries are separately handled via `QueryEntry`.
- **Path Segments as Query**
  The path segments can be parsed as a query object.
- **Fragment as Query**  
  The fragment part (`#...`) can be parsed as a query object as well, useful for hash-based routing in SPAs.
- **JSON Serializable**  
  All components can be converted to structured arrays or serialized into JSON easily.
- **Object-Oriented Everything**  
  No need to use `parse_url()` and `http_build_query()` manually anymore.

---

## ✅ Use Cases
- Modify query parameters in a clean and testable way
- Dynamically generate URLs based on runtime conditions
- Convert fragments into navigable query structures
- Build router-like logic for backend or frontend integration
- Serialize or debug complex URLs in JSON format

---

## 📦 Installation
Urlify can be installed via [Composer](https://getcomposer.org/), the standard PHP dependency manager.

```bash
composer require iceylan/urlify
```

After installation, make sure to include Composer’s autoloader in your project:

```php
require 'vendor/autoload.php';
```

All Urlify classes are namespaced under `Iceylan\Urlify`. You can either import specific classes or use them directly with their fully qualified names:

```php
use Iceylan\Urlify\Url;
use Iceylan\Urlify\Query\Query;
use Iceylan\Urlify\Fragment;
```

ℹ️ Urlify is compatible with `PHP 8.1` or higher.

---

## ⚡ Quick Example
Let’s say you have a URL where a **query string is embedded inside a path segment**. Let's make it more compicated and add another level of nesting with a different separation. Now let's say you want to extract a value from that messy URL.

```php
use Iceylan\Urlify\Url;
use Iceylan\Urlify\Query\Query;

// The URL we're working with
$url = new Url(
    'https://example.com/something/utm_medium=target:readme|foo:bar&utm_source=github/its-me'
);

// Get the dataset as a Query object (we can use negative indexes)
$segmentAsQuery = $url->path->getSegmentAsQuery( -2 );

// Extract the utm_medium value as a Query object
$mediumAsQuery = $segmentAsQuery->getAsQuery( 'utm_medium', '|', ':' );

// access the target key's value
echo $mediumAsQuery->get( 'target' ); // "readme"
```

We can easily chain the above code into a single liner:

```php
echo $url
        ->path
        ->getSegmentAsQuery( -2 )
        ->getAsQuery( 'utm_medium', '|', ':' )
        ->get( 'target' );
// readme
```

This example shows how flexible Urlify can be:

It separates the URL structure cleanly. You can treat path segments as independent values. You can easily re-parse segments, fragments, or query strings as new Query objects.

---

## 🔧 Basic Usage
The `Url` class is the main entry point to working with URL components. Once instantiated, it provides clean access to each part of the URL, and allows full manipulation with method chaining.

### 🔹 Creating a URL instance
```php
use Iceylan\Urlify\Url;

$url = new Url( 'https://user:pass@example.com:8080/users//foo/../profile?view=full#section1' );
```

### 🔹 Accessing URL Components
```php
$url->scheme;
$url->auth;
$url->host;
$url->port;
$url->path;
$url->query;
$url->fragment;
```

### 🔹 Modifying the URL
We can set a value for a segment and the value we give it will be parsed.

All setters return $this, so they support method chaining:

```php
echo $url
    ->setHost( 'iceylan.dev' )
    ->setScheme( 'http' )
    ->setPort( 3000 )
    ->setFragment( 'new-section' );

// Output: http://iceylan.dev:3000/users/profile?view=full#new-section
```

Each component (path, query, fragment) has its own powerful interface. In the next sections, we'll dive deeper into those.

### 🔹 String Conversion
You can also cast the URL object into string directly:

```php
echo $url; // Outputs: https://example.com:8080/users/profile?view=full#section1
```

### 🔹 Array Conversion
Or you can also convert it to an array:

```php
var_dump( $url->toArray());

// Outputs:
// [
// 	'scheme'   => 'https://',
// 	'user'     => 'user',
// 	'pass'     => 'pass',
// 	'host'     => 'example.com',
// 	'port'     => ':8080',
// 	'path'     => '/user/profile',
// 	'query'    => '?view=full',
// 	'fragment' => '#section1',
// ]
```

### 🔹 JSON Serialization
And finally, you can convert it to JSON:

```php
echo json_encode( $url );
```

and output:

```json
{
    "scheme":{
        "name": "https",
        "isSecure": true,
        "isKnown": true
    },
    "auth": {
        "user": "user",
        "pass": "pass"
    },
    "host": {
        "subdomains": [],
        "subdomainName": null,
        "primaryDomainName": "example",
        "topLevelDomain": "com",
        "rootDomain": "example.com"
    },
    "port": {
        "address": 8080,
        "effective": 8080
    },
    "path": {
        "rawSegments": [ "", "users", "", "foo", "..", "profile" ],
        "resolvedSegments": [ "users", "profile" ]
    },
    "query": {
        "view": [ "full" ]
    },
    "fragment": {
        "fragment": "section1",
        "asQuery": null
    }
}
```

---

## 🧩 Builder Mode
You can also use Urlify as a URL builder. It's a simple way to create URLs from scratch.

```php
use Iceylan\Urlify\Url;

echo ( new Url )
    ->setScheme( 'ws' )
    ->setHost( 'example.com' )
    ->setPath( '/users/profile' )
    ->setQuery( 'view=full&flag' )
    ->setFragment( 'section1' );

// Outputs: wa://example.com/users/profile?view=full&flag#section1
```

You can also use component methods to modify them more precisely:

```php
use Iceylan\Urlify\Url;

$url = ( new Url )
    ->setScheme( 'ws' )
    ->setHost( 'example.com' );

$url->path
    ->append( 'profile' )
    ->prepend( 'users' );

echo $url;
// Outputs: ws://example.com/users/profile
```

We can also keep the chain alive:

```php
$url = ( new Url )
    ->setScheme( 'ws' )
    ->setHost( 'example.com' )
	->buildPath( fn ( $path ) =>
		$path
			->append( 'profile' )
			->prepend( 'users' )
	)
	->setQuery( 'view=full&flag' );

echo $url;
// Outputs: ws://example.com/users/profile?view=full&flag
```

---

## 🔸 Scheme
The `scheme` (also known as "protocol") represents the beginning of a URL and indicates how resources should be accessed, for example: `http`, `https`, `ftp`, etc.

The `Url::$scheme` property holds an instance of the `Iceylan\Urlify\Scheme` class, which allows both manipulation and introspection of the scheme.

### 📥 Instantiating
Accessing via `Url` object:

```php
use Iceylan\Urlify\Url;

$scheme = ( new Url( 'https://example.com' ))->scheme;
```

Using `Scheme` class standalone:

```php
use Iceylan\Urlify\Scheme;

$scheme = new Scheme( 'https' );
```

Without an initial value:

```php
$scheme = new Scheme;
```

### 👁️ Getting the Scheme
You can retrieve the current scheme:

```php
$scheme->get(); // 'https'
(string) $scheme; // 'https://'
```

If not set, it returns null or an empty string on cast.

### ✍️ Setting the scheme
Set the scheme value:

```php
$scheme->set( 'tel' );
(string) $scheme; // 'tel:'
```

Or set it through Url:

```php
$url->setScheme( 'sms' );
(string) $url; // 'sms:'
```

`Urlify` reconizes the known schemes and automatically appends the correct suffix.

### 🧹 Cleaning the scheme
Clear the scheme completely:

```php
$scheme->clean();
(string) $scheme; // ''
```

Alternative methods:

```php
$scheme->set( null );
$url->setScheme( null );
```

### 🔐 Is the scheme secure?
Check whether the scheme is marked as secure:

```php
$scheme->set( 'ftp' )->isKnown(); // false
$scheme->set( 'ftps' )->isKnown(); // true
```

### 🤔 Is the scheme known?
Determine if the scheme is one of the known/registered ones:

```php
$scheme->set( 'mysql' )->isKnown(); // true
$scheme->set( 'asgardia' )->isKnown(); // false
```

### ➕ Registering a custom scheme
Custom schemes can be registered globally:

```php
Scheme::registerScheme( name: 'asgardia', suffix: '://', secure: true );

$scheme->set( 'asgardia' );

$scheme->isKnown(); // true
$scheme->isSecure(); // true

(string) $scheme; // 'asgardia://'
```

### 🔄 JSON Serialization
`Scheme` objects can be serialized to JSON.

```php
json_encode( $scheme );
```

Yields:

```JSON
{
	"name": "asgardia",
	"suffix": "://",
	"isSecure": true,
	"isKnown": true
}
```

---

## 🔸 Auth
The `auth` property holds an instance of the `Iceylan\Urlify\Auth` class which represents the authentication part of a URL (i.e., `username:password@`).

### 📥 Instantiating
You can access the auth part directly via the `Url` instance:

```php
use Iceylan\Urlify\Url;

$auth = ( new Url( 'https://username:password@example.com' ))->auth;
```

Or use the `Auth` class directly:

```php
use Iceylan\Urlify\Auth;

$auth = new Auth( 'username', 'password' );
```

Or even instantiate without any credentials:

```php
$auth = new Auth;
```

### 👁️ Reading Username and Password
You can retrieve the username and password values separately.

```php
$auth->getUser(); // 'username'
$auth->getPass(); // 'password'
```

If either is not set, null is returned.


### ✍️ Setting Username and Password
Credentials can be set individually or together:

```php
// You can set simultaneously
$auth->set( 'username', 'password' );

$auth->setUser( 'root' );
$auth->setPass( '1234' );

echo $auth; // 'root:1234@'
```

You can also do this directly on the Url object:

```php
$url->setAuth( 'username', 'password' );
```

Partial values are supported:

```php
echo $url->auth->set( 'username', null ); // 'username@'
echo $url->auth->set( null, 'password' ); // ':password@'
```

### 🧹 Cleaning Credentials
Clear all authentication data with:

```php
$auth->clean();
```

Or reset via setters:

```php
// clear simultaneously
$auth->set( null, null );

// clear separately
$auth->setUser( null );
$auth->setPass( null );
```

On Url:

```php
$url->setAuth( null, null );
```

Output will be:

```php
echo $auth; // ''
```

### ❓ Check If Empty
Determine whether the auth section is currently empty:

```php
$auth->isEmpty(); // true or false
```

### 📤 JSON Serialization
`Auth` objects can be converted into JSON.

```php
json_encode( $auth );
```

Result:

```JSON
{
	"user": "username",
	"pass": "password"
}
```

---

