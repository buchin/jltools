# jltools
Helper for interacting with JSON Lines
## Installation
`composer require buchin/jltools`
## Example
Here is some example. Currently only do one thing: add new field.
### Add new field
This script will add key and value pair to every line in json file
```php
	<?php 
	use Buchin\Jltools\Jltools;
	$jltools = new Jltools;
	$jltools->setPath('/Path/to/jsonline.jsonl');
	$jltools->addField('key', 'value', 'output.jsonl');
```
## Pull Request
If you want to add new feature to this package, please send a pull request. All pull request should come with it’s own test. I currently use [kahlan][1] for my testing purpose.

[1]:	https://kahlan.github.io/docs/index.html "PHP Testing Framework"
