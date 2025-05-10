/**
 * Responsive visual regression tests for MathML block.
 */

const path = require('path');

describe('MathML Block Responsive Visual Tests', () => {
  const viewports = [
    { width: 375, height: 667, name: 'mobile' },     // iPhone SE
    { width: 768, height: 1024, name: 'tablet' },    // iPad
    { width: 1280, height: 800, name: 'desktop' },   // Standard desktop
  ];

  beforeEach(async () => {
    // Navigate to the test page
    const testPagePath = path.join(__dirname, 'fixtures', 'mathml-test.html');
    await page.goto(`file://${testPagePath}`);

    // Wait for MathJax to fully render
    await page.waitForFunction(() => {
      return window.MathJax && window.MathJax.Hub && window.MathJax.Hub.queue.running === 0;
    }, { timeout: 10000 });

    // Additional wait to ensure rendering is complete
    await page.evaluate(() => new Promise(resolve => setTimeout(resolve, 1000)));
  });

  for (const viewport of viewports) {
    describe(`at ${viewport.name} viewport (${viewport.width}x${viewport.height})`, () => {
      beforeEach(async () => {
        await page.setViewport({
          width: viewport.width,
          height: viewport.height,
        });
        // Allow time for any responsive adjustments
        await page.evaluate(() => new Promise(resolve => setTimeout(resolve, 500)));
      });

      test('Simple equation renders correctly', async () => {
        const element = await page.$('#simple-equation');
        const screenshot = await element.screenshot();
        expect(screenshot).toMatchImageSnapshot({
          customSnapshotIdentifier: `simple-equation-${viewport.name}`,
          failureThreshold: 0.01,
          failureThresholdType: 'percent',
        });
      });

      test('Quadratic formula renders correctly', async () => {
        const element = await page.$('#quadratic-formula');
        const screenshot = await element.screenshot();
        expect(screenshot).toMatchImageSnapshot({
          customSnapshotIdentifier: `quadratic-formula-${viewport.name}`,
          failureThreshold: 0.01,
          failureThresholdType: 'percent',
        });
      });

      test('Matrix renders correctly', async () => {
        const element = await page.$('#matrix');
        const screenshot = await element.screenshot();
        expect(screenshot).toMatchImageSnapshot({
          customSnapshotIdentifier: `matrix-${viewport.name}`,
          failureThreshold: 0.01,
          failureThresholdType: 'percent',
        });
      });

      test('Full page renders correctly', async () => {
        const screenshot = await page.screenshot({ fullPage: true });
        expect(screenshot).toMatchImageSnapshot({
          customSnapshotIdentifier: `full-page-${viewport.name}`,
          failureThreshold: 0.01,
          failureThresholdType: 'percent',
        });
      });
    });
  }
});
