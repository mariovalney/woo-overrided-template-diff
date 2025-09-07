# Diff Overrided Template Files for WooCommerce #
**Contributors:** mariovalney  
**Donate link:** https://github.com/mariovalney/diff-overrided-template-for-woocommerce  
**Tags:** woocommerce, diff, development, debug, mariovalney  
**Requires at least:** 4.7  
**Tested up to:** 6.8  
**Requires PHP:** 8.2  
**Stable tag:** 1.0.0  
**License:** GPLv2 or later  
**License URI:** http://www.gnu.org/licenses/gpl-2.0.html  

A tool to help developers fix template files.

## Description ##

Add a Tool to make easy to developers check overrided template files.

Just go to "Tools > WooCommerce Templates Diff" and check diff to start updating your templates.

Click in template name to select it and go to your best code editor!

After update a template file you should reload page or click in "check again" in page bottom to get the next diff.

When all templates are updated you'll finish working.

Take a coffee!

### Development ###

This plugin was developer using the [official docs](https://www.diffchecker.com/docs/getting-started/) of Diffchecker.

None of developers have link with Diffchecker or WooCommerce (but we love them).

### Compatibility ###

We tested this plugin against version 10.1.2+ of WooCommerce.

### Configuration ###

After installing the plugin, you should may:

1. Define in your config `DOT_DIFF_EMAIL`.

This is your e-mail to be considered in [API limits](https://www.diffchecker.com/docs/getting-started).

It's not required as we'll get admin mail as default.

2. Define `DOT_THEME_PATH` and `DOT_TEXT_EDITOR_PROTOCOL`.

You can define `DOT_THEME_PATH` constant to your theme files path in your environment to add a "edit file" link.

It's not required but we'll show a VSCode link to open files to edit.

Not a VSCode user? Define `DOT_TEXT_EDITOR_PROTOCOL` to whenever editor you use (check FAQ for Sublime Text in Ubuntu).

### Translations ###

You can [translate Diff Overrided Template Files for WooCommerce](https://translate.wordpress.org/projects/wp-plugins/diff-overrided-template-for-woocommerce) to your language.

## Installation ##

* Install "Diff Overrided Template Files for WooCommerce" by plugins dashboard.

Or

* Upload the entire `diff-overrided-template-for-woocommerce` folder to the `/wp-content/plugins/` directory.

Then

* Activate the plugin through the 'Plugins' menu in WordPress.

## Frequently Asked Questions ##

### Does it works for another e-commerce plugin? ###

Nope. This is a WooCommerce extension.

### How to open files in Sublime Text in Linux ? ###

By default, Sublime Text doesn't understand `subl://` URLs: ee need to create a custom URL protocol handler.

1. Create a Desktop entry: `nano ~/.local/share/applications/subl-handler.desktop`.

2. Paste this:

```bash
[Desktop Entry]
Name=Sublime Text (URL Handler)
Type=Application
NoDisplay=true
Categories=Utility;
MimeType=x-scheme-handler/subl;
Exec=sh -c 'p="${1#subl://file}"; exec /snap/bin/subl "$p"' dummy %u
```

Adjust `/snap/bin/subl` if your Sublime binary is in another location (check with `which subl`).

3. Register the handler: `xdg-mime default subl-handler.desktop x-scheme-handler/subl && update-desktop-database ~/.local/share/applications`.

4. Define constants:

```
define( 'DOT_THEME_PATH', '/your/path/to/project/www/wp-content/themes' );
define( 'DOT_TEXT_EDITOR_PROTOCOL', 'subl://file//' );
```

### I don't like the image after finish my work. ###

You can change it or just remove: `add_filter( 'dot_itsok_image', '__return_false' );`.

### Who are the developers? ###

* [Mário Valney](https://mariovalney.com/me) is a Brazilian developer who integrates the [WordPress community](https://profiles.wordpress.org/mariovalney).

### Can I help you? ###

Yes! Visit [GitHub repository](https://github.com/mariovalney/diff-overrided-template-for-woocommerce).

## Screenshots ##

### 1. Mais screen ###
![1. Mais screen](http://ps.w.org/diff-overrided-template-files-for-woocommerce/assets/screenshot-1.png)

### 2. Everything is finished! ###
![2. Everything is finished!](http://ps.w.org/diff-overrided-template-files-for-woocommerce/assets/screenshot-2.png)


## Changelog ##

### 1.0 ###

* It's alive!
