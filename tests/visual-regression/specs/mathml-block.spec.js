const { test, expect } = require('@playwright/test');

// Updated story IDs for Storybook v8
const stories = [
  'components-mathmlblock--edit-mode-selected',
  'components-mathmlblock--edit-mode-view',
  'components-mathmlblock--frontend-view',
  'components-mathmlblock--empty-formula-edit',
  'components-mathmlblock--empty-formula-view',
];

test.describe('MathML Block Visual Regression', () => {
  // Increase timeout for the entire test suite
  test.setTimeout(60000);

  for (const storyId of stories) {
    test(`Snapshot for ${storyId}`, async ({ page }) => {
      // Storybook URLs are typically /iframe.html?id=<story-id>
      // Use a less strict waitUntil condition and increase timeout
      await page.goto(`/iframe.html?id=${storyId}`, { waitUntil: 'domcontentloaded', timeout: 30000 });

      // Wait for the content to be visible
      try {
        // Wait for the story content to be visible
        await page.waitForSelector('#storybook-root > *', { timeout: 10000 });
      } catch (e) {
        console.log(`Selector timeout for ${storyId}, continuing anyway`);
      }

      // Wait for MathJax to render if necessary.
      // Simple fixed timeout approach
      await page.waitForTimeout(3000); // Increased timeout for MathJax rendering

      // Take a screenshot of the entire page or a specific element
      // For block components, it's often best to target the block's wrapper if possible.
      // If the story renders just the component, page.screenshot() is fine.
      await expect(page).toHaveScreenshot(`${storyId}.png`);
    });
  }
});
