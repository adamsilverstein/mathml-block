# MathML Block Visual Regression Tests

This directory contains visual regression tests for the MathML block. These tests ensure that the block renders correctly in different scenarios and across different screen sizes.

## Overview

The tests use Jest, Puppeteer, and jest-image-snapshot to capture screenshots of the rendered MathML formulas and compare them against baseline images. If the rendered output changes significantly, the tests will fail, alerting you to potential visual regressions.

## Test Structure

- `fixtures/`: Contains HTML files used for testing
  - `mathml-test.html`: Test page with various MathML formulas
  - `mathml-editor-test.html`: Test page simulating the editor experience

- `mathml-visual.test.js`: Tests for rendered MathML formulas
- `mathml-editor-visual.test.js`: Tests for the editor experience
- `mathml-responsive.test.js`: Tests for responsive behavior across different screen sizes

## Running Tests

You can run the tests using the following npm scripts:

```bash
# Run all tests
npm test

# Run specific test suites
npm run test:visual       # Run basic visual tests
npm run test:editor       # Run editor experience tests
npm run test:responsive   # Run responsive tests

# Update snapshots (baseline images)
npm run test:update-snapshots
```

## Understanding Test Results

When tests fail, jest-image-snapshot will generate diff images in the `__diff_output__` directory. These images highlight the differences between the expected and actual screenshots, making it easier to identify what changed.

## Maintaining Tests

### Updating Baseline Images

When you intentionally change the appearance of the MathML block, you'll need to update the baseline images:

```bash
npm run test:update-snapshots
```

### Adding New Tests

To add a new test case:

1. Add the MathML formula to the appropriate fixture file
2. Create a new test in the relevant test file
3. Run the tests with `--updateSnapshot` to create the baseline image

## Troubleshooting

- **Inconsistent rendering**: MathJax rendering can sometimes be slightly inconsistent. The tests include a small failure threshold (1%) to account for minor variations.
- **Font differences**: Different environments may have different fonts installed, which can affect rendering. Consider using web fonts for more consistent results.
- **Viewport size**: Make sure to test at the same viewport sizes to avoid false positives.

## CI Integration

These tests can be integrated into CI workflows to automatically catch visual regressions. See the GitHub Actions workflow files for examples of how to run these tests in CI.
