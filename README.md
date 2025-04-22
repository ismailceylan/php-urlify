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

We can also keep the chain alive with builder methods, for example:

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
echo $scheme->set( 'tel' ); // 'tel:'
```

Or set it through Url:

```php
echo $url->setScheme( 'sms' ); // 'sms:'
```

`Urlify` reconizes the known schemes and automatically appends the correct suffix.

### 🧹 Cleaning the scheme
Clear the scheme completely:

```php
echo $scheme->clean(); // ''
```

Alternative methods:

```php
echo $scheme->set( null ); // ''
echo $url->setScheme( null ); // ''
```

### 🔐 Is the scheme secure?
Check whether the scheme is marked as secure:

```php
$scheme->set( 'ftp' )->isSecure(); // false
$scheme->set( 'ftps' )->isSecure(); // true
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

echo $scheme; // 'asgardia://'
```

### 🔄 JSON Serialization
`Scheme` objects can be serialized into JSON.

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

// Or separately
$auth->setUser( 'root' );
$auth->setPass( '1234' );

echo $auth; // 'root:1234@'
```

Partial credentials are also supported:

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
// equivalent to clean method
$auth->set( null, null );

// clear separately
$auth->setUser( null );
$auth->setPass( null );
```

On Url:

```php
// echo triggers Url::__toString method
echo $url->setUsername( null )->setPassword( null ); // ''
```

### ❓ Check If Empty
Determine whether the auth section (both username and password) is currently empty:

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

## 🔸 Host
The `host` component of a URL specifies the domain address that identifies the resource's location on the network. In `Urlify`, the `host` property is an instance of the `Iceylan\Urlify\Host` class, providing methods for manipulation and inspection of the host part.

This library uses a list of top-level domain names to separate the top-level domain names. It doesn't just take the latest part of a string separated by dots and treat it as the main domain. This approach tells us that `co.uk` is a top-level domain name.

### 📥 Instantiating
Accessing host instance via `Url` object:

```php
use Iceylan\Urlify\Url;

$host = ( new Url( 'https://example.com' ))->host;
```

Using Host class standalone:

```php
use Iceylan\Urlify\Host;

$host = new Host( 'www.foo.example.co.uk' );
```

Without an initial value:

```php
$host = new Host;
```

### 👁️ Retrieving Host Parts
After setting the host, you can retrieve host parts by meaningful methods.

```php
$host->getSubdomainName();      // www.foo
$host->getSubdomains();         // ['www', 'foo']
$host->getPrimaryDomainName();  // example
$host->getTopLevelDomainName(); // co.uk
$host->getRootDomainName();     // example.co.uk
```

If any of these is not set, `null` is returned.

### ✍️ Manupulating Host
You can manipulate the host parts with powerful methods.

#### Set Host As a Whole String
Sometimes setting the host with a whole string is can be enough for you. We have a method for this:

```php
echo $host->set( 'subdomain.example.com' );
// 'subdomain.example.com'
```

When the set method is called, parsing processes will be start for given host and all the getter methods will return the parsed values.

You can also set the host directly on the `Url` object:

```php
echo $url->setHost( 'api.example.com' );
// 'https://api.example.com'
```

#### Subdomain Manipulations
Sometimes you need to set the subdomain as a whole string. We have a method for this:

```php
echo $host->setSubdomain( null ); // example.com
echo $host->setSubdomain( 'bar.baz' ); // bar.baz.example.com
```

You may also need to append or prepend existing subdomains.

```php
echo $host->appendSubdomain( 'zoo' ); // bar.baz.zoo.example.com
echo $host->prependSubdomain( 'chat' ); // chat.bar.baz.zoo.example.com
```

#### Primary Domain Manipulations
You can also set the primary domain name with the `setPrimaryDomainName` method:

```php
echo $host->setPrimaryDomainName( 'exam' ); // chat.bar.baz.zoo.exam.com
```

#### Top Level Domain Manipulations
You can also set the top-level domain name with the `setTopLevelDomainName` method:

```php
echo $host->setTopLevelDomainName( 'co.uk' ); // chat.bar.baz.zoo.exam.co.uk
```

### 🧹 Clearing the Host
Clear the host completely:

```php
echo $host->clean(); // ''
// or
echo $host->set( null ); // ''
```

### 📤 JSON Serialization
`Host` objects can be converted into JSON.

```php
json_encode( $host );
```

Result:

```JSON
{
	"subdomainName": "chat.bar.baz.qux.zoo",
	"subdomains": [ "chat", "bar", "baz", "qux", "zoo" ],
	"primaryDomainName": "exam",
	"rootDomain": "exam.co.uk",
	"topLevelDomain": "co.uk"
}
```

---

## 🔸 Port
The `port` component of a URL specifies the port number used for communication with the resource.

### 📥 Instantiating
Accessing port instance via `Url` object:

```php
use Iceylan\Urlify\Url;

$port = ( new Url( 'https://example.com:8001' ))->port;
```

Using Port class standalone:

```php
use Iceylan\Urlify\Port;

$port = new Port( 8001 );
```

Without an initial value:

```php
$port = new Port;
```

### 👁️ Retrieving Port
After setting the port, you can retrieve the port with the `get` method:

```php
$port->get(); // 8001
```

If the port is not set, `null` is returned.

### ✍️ Setting Port
You can set the port with the `set` method:

```php
echo $port->set( 8001 ); // ':8001'
```

### Checking if Port is Defined
You can check if the port is defined with the `isEmpty` method:

```php
echo $port->isEmpty(); // false
```

### Retrieving Effective Port
You can retrieve the effective port with the `getEffective` method. Effective port is the port set or the default port for the scheme if the port is not set.

```php
echo $port->getEffective(); // 8001
```

### Default Port
You can use `Port` class to query the default port for a given scheme:

```php
use Iceylan\Urlify\Port;

echo Port::getDefaultPortForScheme( 'https' ); // 443
```

### 🧹 Clearing the Port
Clear the port completely:

```php
echo $port->clean(); // ''
// or
echo $port->set( null ); // ''
```

### 📤 JSON Serialization
`Port` objects can be converted into JSON.

```php
json_encode( $port );
```

Result:

```JSON
{
    "address" => null,
    "effective" => 443
}
```

---
