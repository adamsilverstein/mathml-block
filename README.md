# A MathML block for the WordPress block editor (Gutenberg).

## Description

* Enables MathML math formulas blocks in the editor.
* Uses the MathJax library to render the formulas: https://www.mathjax.org
* Compatible with the [official AMP plugin](https://amp-wp.org/) by rendering [`amp-mathml`](https://amp.dev/documentation/components/amp-mathml/) on [AMP pages](https://amp.dev/).

### What is MathML?

Mathematical Markup Language is a mathematical markup language, an application of XML for describing mathematical notations and capturing both its structure and content. It aims at integrating mathematical formulae into World Wide Web pages and other documents.

The MathML block uses MathJax to render MathML formulas in the editor and on the front end of a website. MathJax (https://www.mathjax.org/) is _A JavaScript display engine for mathematics that works in all browsers._

To test a MathML block and enter a formula, for example: `\[x = {-b \pm \sqrt{b^2-4ac} \over 2a}\]`.

To test using math formulas inline, type an formula into a block of text, select it and hit the 'M' icon in the control bar. For example: `\( \cos(θ+φ)=\cos(θ)\cos(φ)−\sin(θ)\sin(φ) \)`. _Note: if you are copying and pasting formulas into the rich text editor, switching to HTML/code editor mode is less likely to reformat your pasted formula._

## Screencast

![](https://cl.ly/c0f6bbfbc3b1/Screen%252520Recording%2525202018-12-25%252520at%25252008.12%252520AM.gif)

## Development

### Testing

#### Visual Regression Tests

The plugin includes visual regression tests using Storybook and Playwright.

1. Start Storybook:
   ```bash
   npm run storybook
   ```

2. Run the tests:
   ```bash
   npm run test:visual
   ```

3. Update snapshots if needed:
   ```bash
   npm run test:visual:update
   ```

#### PHP Unit Tests

The plugin includes PHP unit tests for the PHP functions.

1. Install PHP dependencies:
   ```bash
   composer install
   ```

2. Run the tests:
   ```bash
   composer test
   ```

### Code Quality

Run PHP CodeSniffer:
```bash
composer phpcs
```

Fix coding standards issues automatically:
```bash
composer phpcbf
```
