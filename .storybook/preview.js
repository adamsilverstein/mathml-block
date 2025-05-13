// Import global CSS files or setup global decorators here
// import '../src/mathml-block.css'; // Example: if your block has global styles

export const parameters = {
  actions: { argTypesRegex: '^on[A-Z].*' },
  controls: {
    matchers: {
      color: /(background|color)$/i,
      date: /Date$/,
    },
  },
};
