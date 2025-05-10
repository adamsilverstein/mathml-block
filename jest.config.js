module.exports = {
  preset: 'jest-puppeteer',
  testMatch: ['**/tests/**/*.test.js'],
  setupFilesAfterEnv: ['./tests/setup.js'],
  transform: {
    '^.+\\.jsx?$': 'babel-jest',
  },
};
