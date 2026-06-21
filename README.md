# DynamicJsonLD

Adds a Scribunto Lua API for adding JSON-LD data to pages, along with a boilerplate JSON-LD template.

## Installation

DynamicJsonLD required MediaWiki 1.43 or later.

You can use Git to install DynamicJsonLD in your MediaWiki extensions folder:

```
cd extensions/
git clone https://github.com/obbywiki/mediawiki-extensions-DynamicJsonLD.git
```

Then load the extension in your `LocalSettings.php` file:

```php
wfLoadExtension( 'DynamicJsonLD' );
```

Requires the SchemaOrg metadata generator to be disabled if using WikiSEO.

```php
$wgMetadataGenerators = ['OpenGraph', 'Twitter'];
```

## Usage

Require the library in your Lua module.

```lua
local JsonLD = mw.ext.JsonLD
```

Set the main entity in the JSON-LD. Please note that the root data will always be an `Article` with this extension.

```lua
JsonLD.setMainEntity( {
    ["@type"] = "VideoGame",
    ["name"] = "My Game",
    ["description"] = "My game description",
    ["url"] = "https://example.com",
    ["image"] = "https://example.com/wiki/Special:FilePath/Game_Thumbnail.webp",
    ["publisher"] = {
        ["@type"] = "Organization",
        ["name"] = "Company",
        ["url"] = "https://example.com",
        ["logo"] = {
            ["@type"] = "ImageObject",
            ["url"] = "https://example.com/logo.png",
        }
    }
} )
```

Add any additional root data to the JSON-LD.

```lua
JsonLD.addData( {
    ["image"] = "https://example.com/wiki/Special:FilePath/My_Article_Thumbnail.webp",
} )
```

# TODO

* No TODO tasks left!

---

[ObbyWiki OSS](https://obbywiki.github.io/)
