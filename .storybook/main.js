module.exports = {
  stories: ['../src/**/*.stories.mdx', '../src/**/*.stories.@(js|jsx|ts|tsx)'],
  addons: ['@storybook/addon-essentials'],
  framework: {
    name: '@storybook/react-webpack5',
    options: {}
  },
  docs: {
    autodocs: true
  },
  staticDirs: ['../assets'], // If you have static assets like images or fonts for stories
  babel: async (options) => ({
    ...options,
    presets: [
      '@babel/preset-env',
      '@babel/preset-react'
    ],
  }),
  webpackFinal: async (config) => {
    // Add .js files to be processed by Babel
    config.module.rules.push({
      test: /\.js$/,
      exclude: /node_modules/,
      use: {
        loader: 'babel-loader',
        options: {
          presets: ['@babel/preset-env', '@babel/preset-react']
        }
      }
    });

    return config;
  }
};
