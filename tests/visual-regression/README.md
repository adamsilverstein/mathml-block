# Visual Regression Tests

This directory contains the configuration and tests for visual regression testing of the MathML block using Storybook and Playwright.

## How to Run

1.  **Start Storybook:**
    First, ensure all dependencies are installed (`npm install`). Then, start the Storybook development server:
    ```bash
    npm run storybook
    ```
    This will typically open Storybook in your browser at `http://localhost:6006`.

2.  **Run Playwright Tests:**
    Once Storybook is running, you can execute the Playwright tests.

    *   **To generate initial snapshots (run this the first time or after intentional visual changes):**
        ```bash
        npm run test:visual:update
        ```
        This command runs the tests and saves new snapshot images in a directory like `tests/visual-regression/specs/mathml-block.spec.js-snapshots`.

    *   **To compare against existing snapshots:**
        ```bash
        npm run test:visual
        ```
        This command runs the tests and compares the current rendering against the previously saved snapshots. If there are differences, the tests will fail, and Playwright will generate diff images showing the discrepancies. These will be available in the HTML report (usually in `playwright-report/index.html`) and potentially in a `test-results` directory if configured.

## Test Structure

*   **Playwright Configuration:** `playwright.config.js` (in the project root)
*   **Storybook Stories:** Located in `src/` alongside the components (e.g., `src/mathml-block.stories.js`)
*   **Playwright Test Specs:** Located in `tests/visual-regression/specs/` (e.g., `tests/visual-regression/specs/mathml-block.spec.js`)
*   **Snapshots:** Stored in a directory named after the spec file with `-snapshots` appended (e.g., `tests/visual-regression/specs/mathml-block.spec.js-snapshots/`)

## Updating Snapshots

If visual changes are intentional (e.g., due to a feature update or bug fix), update the reference snapshots by running:
```bash
npm run test:visual:update
```
Review the changes in the generated snapshots and commit them to your repository.
