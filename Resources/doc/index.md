Usage guide
==================

## Preamble

This version of the bundle was developped and test with Symfony 2.2 only. However, this version should work with Symfony 2.1 and 2.0 but some bug may happend.

## Installation

### Composer

Add VxJsUploadBundle in your composer.json:

``` json
{
    "require": {
        "vx/js-upload-bundle": "*"
    }
}
```

Now you can download the bundle with composer:

``` bash
$ php composer.phar update vx/js-upload-bundle
```

### AppKernel

Enable the bundle in the kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Vx\JsUploadBundle\VxJsUploadBundle(),
    );
}
```

## Configuration & Usage

This bundle provie several profile for an easy use of the jQuery package [BlueImp jQuery file uploader](https://github.com/blueimp/jQuery-File-Upload/).

### Profile

You can configure any profile you want in config.yml:

```yaml
# app/config/config.yml

vx_js_upload:
    profile:
        upload:
            upload_dir: vx/upload
            image_versions:
                thumbnail:
                    max_height: 80
                    max_width: 80
        user:
            upload_dir: users/avatar
            erase_file: true
            image_versions: ~
```

To see all options, take a loot at the [Profile Options](https://github.com/Vixys/VxJsUploadBundle/wiki/Profile-Options) page.

**Caution**: The _default_ profile can't be set.

### Usage

To use it, just add the following line in your code:

#### For single upload

``` twig
{# random.html.twig #}

{# ... #}

<input id="fileupload" type="file" name="files[]" data-url="{{ path('vx_js_upload', { 'profile' : 'avatar' }) }}">

{# ... #}
```

If you want to change the name of the file after upoad:

``` twig
{# random.html.twig #}

{# ... #}

<input id="fileupload" type="file" name="files[]" data-url="{{ path('vx_js_upload', { 'profile' : 'avatar', 'filename' : your_filename }) }}">

{# ... #}
```

*your_filename* shouldn't contain the extension. The uploader will add it.

#### For multiple upload

``` twig
{# random.html.twig #}

{# ... #}

<input id="fileupload" type="file" name="files[]" data-url="{{ path('vx_js_upload', { 'profile' : 'avatar' }) }}" multiple>

{# ... #}
```

#### Custom behaviour

To add some custom behaviour when you add a file or after uploaded a file, take a look at the [BlueImp jQuery file uploader](https://github.com/blueimp/jQuery-File-Upload/wiki/Options#callback-options) documentation.
