# Leynos
## Overview ##
Lightweight PHP7 MVC framework featuring atomic controller chains dubbed as slices. PHP ≥ 7.1.0 is required.

The Leynos framework project aims to provide an open source MVC framework written entirely in PHP7. This framework is
designed to be as flexible as it is compact. Most PHP applications built with the MVC pattern running atop a framework
or master controller inherently require that the code in the framework always executes on every request. Therefore the
objective should be to provide the essentials to facilitate a functional MVC design in the application, and nothing
more.

Leynos is designed to be flexible and reprogrammable via configuration. With proper interfacing, libraries can plug
right into Leynos to power it which in turn powers your application. Connect template engines such as Twig or Smarty,
and memory stores such as Redis or memcached, as well as any database that can be accessed through PHP's PDO abstraction
layer.

Instructions for installing and using the framework will be documented in the
[wiki](https://github.com/kitsunenokenja/leynos/wiki).

## Features ##
The following list of features is not exhaustive, but indicates some of this framework's capabilities.
* Chaining single-purpose atomic controllers, called slices
* Custom routing patterns and route aliasing
* Permission token protection for routes
* Sanitisation and wrapping of PHP's super-globals
* I/O mapping and aliasing support for controllers
* Response/routing handling with unlimited exit status handling
* Lazy-loading of PDO DB connections
* Support for memcached & Redis memory stores included
* Support for Smarty & Twig template engines included
* Namespaces of keys for memory stores for global and local storage
* Volatile memory store interface providing a native self-expiring mechanic
* Binary view support with PDF and CSV/ODS/XLSX support included, provided by [FPDF](http://www.fpdf.org/) and
[Spout](https://github.com/box/spout) libraries respectively
* HTTP/2 compliant

Please see the [wiki](https://github.com/kitsunenokenja/leynos/wiki) for further details of the aforementioned features.
