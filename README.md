# Urlify
**Urlify** is a lightweight, object-oriented PHP library designed for parsing, manipulating, and reconstructing URLs in a highly modular and intuitive way.

It provides fine-grained access to every component of a URL—such as scheme, authorization, host, path, query, and fragment—through dedicated classes. Unlike typical URL parsers that only offer string-based access, Urlify structures these components as first-class objects, enabling more powerful and flexible manipulations.

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
- 🔍 **Full URL Parsing**  
  Create a `Url` object from a string and instantly access all parts via method calls.
- 🧬 **Immutable-Friendly API**  
  Most methods support chaining, enabling fluent and predictable transformations.
- 🧠 **Query Intelligence**  
  Flags (`?foo&bar=baz`) and key-value entries are separately handled via `QueryEntry`.
- 🎯 **Fragment as Query**  
  The fragment part (`#...`) can be parsed as a query object as well, useful for hash-based routing in SPAs.
- 🧪 **JSON Serializable**  
  All components can be converted to structured arrays or serialized into JSON easily.
- 🧰 **Object-Oriented Everything**  
  No need to use `parse_url()` and `http_build_query()` manually anymore.

---

## ✅ Use Cases
- Modify query parameters in a clean and testable way
- Dynamically generate URLs based on runtime conditions
- Convert fragments into navigable query structures
- Build router-like logic for backend or frontend integration
- Serialize or debug complex URLs in JSON format

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
Let’s say you have a URL where a **query string is embedded inside a path segment**, and let's make it more compicated and add another level of nesting. Now let's say you want to extract a value from it.

```php
use Iceylan\Urlify\Url;
use Iceylan\Urlify\Query\Query;

// The URL we're working with
$url = new Url(
	'https://example.com/track/other-stuff/utm_source=github&utm_medium=target:readme|foo:bar/its-me'
);

// Get the dataset as a Query object (we can use negative indexes)
$pathSegmentAsQuery = $url->path->getSegmentAsQuery( -2 );

// Extract the utm_source value as a Query object
$utmMediumAsQuery = $pathSegmentAsQuery->getAsQuery( 'utm_medium', '|', ':' );

// access the target key's value
echo $utmMediumAsQuery->get( 'target' ); // "readme"
```

We can easily chain the above code into a single line:

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

You can also cast the URL object to string directly:

```php
echo $url; // Outputs: https://example.com:8080/users/profile?view=full#section1
```

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

## Url Builder Mode
You can also use Urlify as a URL builder. It's a simple way to create URLs from scratch. Here's an example:

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

You can also use segment handlers to modify them more precisely:

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
