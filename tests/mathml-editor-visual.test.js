/**
 * Visual regression tests for MathML block editor experience.
 */

const path = require('path');

describe('MathML Block Editor Visual Tests', () => {
  beforeAll(async () => {
    // Navigate to the test page
    const testPagePath = path.join(__dirname, 'fixtures', 'mathml-editor-test.html');
    await page.goto(`file://${testPagePath}`);

    // Wait for MathJax to fully render
    await page.waitForFunction(() => {
      return window.MathJax && window.MathJax.Hub && window.MathJax.Hub.queue.running === 0;
    }, { timeout: 10000 });

    // Additional wait to ensure rendering is complete
    await page.evaluate(() => new Promise(resolve => setTimeout(resolve, 1000)));
  });

  test('Editor with simple equation renders correctly', async () => {
    const element = await page.$('#editor-simple');
    const screenshot = await element.screenshot();
    expect(screenshot).toMatchImageSnapshot({
      customSnapshotIdentifier: 'editor-simple-equation',
      failureThreshold: 0.01,
      failureThresholdType: 'percent',
    });
  });

  test('Preview of simple equation renders correctly', async () => {
    const element = await page.$('#preview-simple');
    const screenshot = await element.screenshot();
    expect(screenshot).toMatchImageSnapshot({
      customSnapshotIdentifier: 'preview-simple-equation',
      failureThreshold: 0.01,
      failureThresholdType: 'percent',
    });
  });

  test('Editor with complex equation renders correctly', async () => {
    const element = await page.$('#editor-complex');
    const screenshot = await element.screenshot();
    expect(screenshot).toMatchImageSnapshot({
      customSnapshotIdentifier: 'editor-complex-equation',
      failureThreshold: 0.01,
      failureThresholdType: 'percent',
    });
  });

  test('Preview of complex equation renders correctly', async () => {
    const element = await page.$('#preview-complex');
    const screenshot = await element.screenshot();
    expect(screenshot).toMatchImageSnapshot({
      customSnapshotIdentifier: 'preview-complex-equation',
      failureThreshold: 0.01,
      failureThresholdType: 'percent',
    });
  });

  test('Editing and updating preview works correctly', async () => {
    // Modify the simple equation
    await page.focus('#simple-equation-input');
    await page.keyboard.press('End');
    await page.keyboard.press('Enter');
    await page.keyboard.type('<!-- Modified equation -->');

    // Click update preview
    await page.click('#editor-simple button');

    // Wait for MathJax to re-render
    await page.waitForFunction(() => {
      return window.MathJax && window.MathJax.Hub && window.MathJax.Hub.queue.running === 0;
    }, { timeout: 5000 });

    // Additional wait to ensure rendering is complete
    await page.evaluate(() => new Promise(resolve => setTimeout(resolve, 1000)));

    // Take screenshot of the updated preview
    const element = await page.$('#preview-simple');
    const screenshot = await element.screenshot();
    expect(screenshot).toMatchImageSnapshot({
      customSnapshotIdentifier: 'preview-simple-equation-after-edit',
      failureThreshold: 0.01,
      failureThresholdType: 'percent',
    });
  });

  test('Full editor page renders correctly', async () => {
    const screenshot = await page.screenshot({ fullPage: true });
    expect(screenshot).toMatchImageSnapshot({
      customSnapshotIdentifier: 'editor-full-page',
      failureThreshold: 0.01,
      failureThresholdType: 'percent',
    });
  });
});
