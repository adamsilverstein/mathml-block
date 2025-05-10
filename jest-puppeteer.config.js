module.exports = {
  launch: {
    headless: 'new',
    args: ['--no-sandbox', '--disable-setuid-sandbox'],
    defaultViewport: {
      width: 1280,
      height: 800,
    },
  },
  browserContext: 'default',
};
