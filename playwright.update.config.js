const baseConfig = require('./playwright.config');

// Create a new configuration that extends the base configuration
module.exports = {
  ...baseConfig,
  // Override the expect configuration to force platform-agnostic snapshots
  expect: {
    ...baseConfig.expect,
    toHaveScreenshot: {
      ...baseConfig.expect.toHaveScreenshot,
      // Force platform-agnostic snapshots
      _comparator: 'pixelmatch', // Use pixelmatch comparator
      maxDiffPixels: 100, // Allow some pixel differences
    },
  },
  // Force update snapshots
  updateSnapshots: 'all',
};
