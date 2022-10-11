=== MathML Block ===
Contributors:  adamsilverstein
Author URI: http://tunedin.net.com
Plugin URI: https://github.com/adamsilverstein/mathml-block
Tags: MathML, Gutenberg, Block, math, block editor
Requires at least: 5.0
Tested up to: 6.1
Requires PHP: 5.6
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: mathml-block

A MathML block for the WordPress block editor (Gutenberg).

== Description ==

A MathML block for the WordPress block editor (Gutenberg).
Requires PHP 5.4+ and WordPress 5.0+.

Development takes place on the GitHub repository: https://github.com/adamsilverstein/mathml-block.

Screencast: https://cl.ly/c0f6bbfbc3b1

=== What is MathML? ===

Mathematical Markup Language is a mathematical markup language, an application of XML for describing mathematical notations and capturing both its structure and content. It aims at integrating mathematical formulae into World Wide Web pages and other documents.

The MathML block uses MathJax to render MathML formulas in the editor and on the front end of a website. MathJax (https://www.mathjax.org/) is _A JavaScript display engine for mathematics that works in all browsers._

To test a MathML block and enter a formula, for example: `\[x = {-b \pm \sqrt{b^2-4ac} \over 2a}\]`.

To test using math formulas inline, type an formula into a block of text, select it and hit the 'M' icon in the control bar. For example: `\( \cos(θ+φ)=\cos(θ)\cos(φ)−\sin(θ)\sin(φ) \)`. _Note: if you are copying and pasting formulas into the rich text editor, switching to HTML/code editor mode is less likely to reformat your pasted formula._

This plugin is compatible with the [official AMP plugin](https://amp-wp.org/) by rendering [`amp-mathml`](https://amp.dev/documentation/components/amp-mathml/) on [AMP pages](https://amp.dev/).

=== Technical Notes ===

* Requires PHP 5.6+.
* Requires WordPress 5.0+.
* Issues and Pull requests welcome on the GitHub repository: https://github.com/adamsilverstein/mathml-block.

== Screenshots ==

1. Example of adding a MathML block.


== Installation ==
1. Install the plugin via the plugin installer, either by searching for it or uploading a .zip file.
2. Activate the plugin.
3. Use the MathML block!

== Changelog ==

= 1.2.2 =
Tested up to 6.1.

= 1.2.0 =
* Add AMP compatibility, props @westonruter. Leverages the `amp-mathml` component.

= 1.1.5 =
* Make JavaScript translatable, take 2.

= 1.1.1 =
* Improve translations, make JavaScript translatable.
* Update all packages.

= 1.1.0 =
* Add support for inline formulas.

= 1.0.0 =
* Initial plugin release
