# Leynos
Lightweight PHP7 MVC framework featuring chainable controllers.

The Leynos framework project aims to provide an open source MVC framework written entirely in PHP7. This framework is designed to be as flexible as it is compact. Most PHP applications built with the MVC pattern running atop a framework or master controller inhernetly require that the code in the framework always executes on every request. Therefore the objective should be to provide the essentials to facilitate a functional MVC design in the application, and nothing more.

Leynos is designed to be flexible and reprogrammable via configuration. With proper interfacing, libraries can plug right into Leynos to power it which in turn powers your application. Connect template engines such as Twig, Blade, or Smarty, and memory stores such as Redis or memcached, as well as any database that can be accessed through PHP's PDO abstraction layer.

This is the beginning of the release of this personal project, therefore it may not be appropriate for production deployment. This README shall be updated as development advances, to elaborate upon Leynos' functionality and to document its usage, as well as provide friendly instructions on hacking Leynos, for building new extensions for it which may not be integrated to the mainline, and even for hacking the kernel of the framework itself to truly customise its behaviour to tailor it for a specific application altogether.
