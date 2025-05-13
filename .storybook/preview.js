// Import global CSS files or setup global decorators here
// import '../src/mathml-block.css'; // Example: if your block has global styles

// Load MathJax from CDN
import React, { useEffect } from 'react';

// Add MathJax configuration and loading
const loadMathJax = () => {
  // Only load MathJax once
  if (window.MathJax) {
    return;
  }

  // Create script element to load MathJax
  const script = document.createElement('script');
  script.src = 'https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.9/MathJax.js?config=TeX-AMS-MML_HTMLorMML';
  script.async = true;

  // Configure MathJax
  window.MathJax = {
    messageStyle: 'none',
    showMathMenu: false,
    tex2jax: {
      inlineMath: [['$','$'], ['\\(','\\)']]
    },
    skipStartupTypeset: true, // We'll trigger typesetting manually
    MMLorHTML: {
      prefer: {
        Firefox: "MML",
        Safari: "MML",
        Chrome: "HTML",
        Opera: "HTML",
        other: "HTML"
      }
    }
  };

  document.head.appendChild(script);
};

// Create a decorator to ensure MathJax is loaded and configured
export const decorators = [
  (Story) => {
    useEffect(() => {
      loadMathJax();

      // Re-render MathJax content after the story renders
      if (window.MathJax && window.MathJax.Hub) {
        setTimeout(() => {
          window.MathJax.Hub.Queue(['Typeset', window.MathJax.Hub]);
        }, 500);
      }
    }, []);

    return <Story />;
  },
];

export const parameters = {
  actions: { argTypesRegex: '^on[A-Z].*' },
  controls: {
    matchers: {
      color: /(background|color)$/i,
      date: /Date$/,
    },
  },
};
