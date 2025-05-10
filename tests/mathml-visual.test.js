/**
 * Visual regression tests for MathML block.
 */

const path = require('path');

describe('MathML Block Visual Tests', () => {
  beforeAll(async () => {
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

  test('Simple equation renders correctly', async () => {
    const element = await page.$('#simple-equation');
    const screenshot = await element.screenshot();
    expect(screenshot).toMatchImageSnapshot({
      customSnapshotIdentifier: 'simple-equation',
      failureThreshold: 0.01,
      failureThresholdType: 'percent',
    });
  });

  test('Quadratic formula renders correctly', async () => {
    const element = await page.$('#quadratic-formula');
    const screenshot = await element.screenshot();
    expect(screenshot).toMatchImageSnapshot({
      customSnapshotIdentifier: 'quadratic-formula',
      failureThreshold: 0.01,
      failureThresholdType: 'percent',
    });
  });

  test('Matrix renders correctly', async () => {
    const element = await page.$('#matrix');
    const screenshot = await element.screenshot();
    expect(screenshot).toMatchImageSnapshot({
      customSnapshotIdentifier: 'matrix',
      failureThreshold: 0.01,
      failureThresholdType: 'percent',
    });
  });

  test('Integral renders correctly', async () => {
    const element = await page.$('#integral');
    const screenshot = await element.screenshot();
    expect(screenshot).toMatchImageSnapshot({
      customSnapshotIdentifier: 'integral',
      failureThreshold: 0.01,
      failureThresholdType: 'percent',
    });
  });

  test('Full page renders correctly', async () => {
    const screenshot = await page.screenshot({ fullPage: true });
    expect(screenshot).toMatchImageSnapshot({
      customSnapshotIdentifier: 'full-page',
      failureThreshold: 0.01,
      failureThresholdType: 'percent',
    });
  });
});
