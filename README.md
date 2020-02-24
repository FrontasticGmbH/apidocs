# Opinionated API Doc Generator

This small tool generates API documentation for PHP (and JavaScript) as
Markdown files, so they can be properly rendered on Github and similar tools.
The tool makes some assumptions (for example: always only one class per file,
type hints over inline documentation, …) to make the generated documentation
easy to read.

## Documentation Goals

The goal of this tool to create simple, digestible and compact documentation.
This includes adding as many cross-references ass possible to make the code &
documentation dicoverable. This tools tries to focus on the relevant
information and does not try to output as much information as somehow possible.

## Configuration File

The configuration of this tools is done using a YAML file called
`.apidocs.yml`, by default. You can also create the file asomewhere else and
provide the file location as an argument to `bin/apidocs`. All paths inside the
configuration file will be *relative to the configuration file*, like:

```
name: "My Example project"
source: ../common/src
target: ../common/docs
autoloader: ../common/vendor/autoload.php
nameSpace: "\\My\\Example"
files: # Relative to `source`
 - php/My/Class.php
 - …
```
