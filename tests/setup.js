const { toMatchImageSnapshot } = require('jest-image-snapshot');

expect.extend({ toMatchImageSnapshot });

// Set a longer timeout for visual tests
jest.setTimeout(30000);
