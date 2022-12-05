# Translations Extractor plugin for Craft CMS 3.5

WORK IN PROGRESS, USE AT OWN RISK

## Requirements

This plugin requires Craft CMS 3.5 later.

## Installation

```
composer require rauwebieten/craft-translations-extractor
```

## Usage

   php craft translations-extractor/index
   
This command will search for translations strings in your templates folder.
This command uses a regular expression to find all translation strings, it will find following
translation strings:

```twig
<p>
    {# translation with t filter #}
    {{ "Hello world"|t }}

    {# translation with translate filter #}
    {{ "Hello craft"|translate }}

    {# translation with single quotes #}
    {{ 'Hello template'|t }}

    {# translation with single/double quotes #}
    {{ 'This is a "quoted string"'|t }}
    {{ "This is another \"quoted string\""|t }}
    {{ 'This too is a \'quoted string\''|t|raw }}
</p>
```

Foreach defined language in your project it will create a translation file.

- Translation string that are not in use anymore, are removed from the translation file
- Existing translations strings are merge with the newly found translations strings

```
translations/en/site-extracted.php
```

Do whatever you like with this file. For example, include it in the main translation file.
This way you can still use a site.php file with your custom translation strings.

```php
// translations/en/site.php
<?php return array_merge(include __DIR__ .'site-extracted.php',[
  'other-translation-strings' => 'blah blah'
]);
```

## Not supported.

This plugin is a work in progress. Use at own risk.

This plugin cannot:

1. detect translation strings with parameters, like ```"{num} pages"(params={num:3})```
2. detect translation strings with other namespaces, like ```"hello"|t('plugin-handle')```

## Roadmap

1. resolve issues as mentioned above
2. use a translation API 
3. Craft 4 support
