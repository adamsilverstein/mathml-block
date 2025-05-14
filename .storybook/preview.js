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

  // Configure MathJax v3
  window.MathJax = {
    tex: {
      inlineMath: [['$', '$'], ['\\(', '\\)']]
    },
    options: {
      skipHtmlTags: ['script', 'noscript', 'style', 'textarea', 'pre'],
      ignoreHtmlClass: 'tex2jax_ignore',
      processHtmlClass: 'tex2jax_process'
    },
    startup: {
      typeset: false // We'll trigger typesetting manually
    }
  };

  // Create script element to load MathJax
  const script = document.createElement('script');
  script.src = '../vendor/MathJax/es5/tex-mml-chtml.js';
  script.async = true;
  document.head.appendChild(script);
};

// Create a decorator to ensure MathJax is loaded and configured
export const decorators = [
  (Story) => {
    useEffect(() => {
      loadMathJax();

      // Re-render MathJax content after the story renders
      if (window.MathJax) {
        setTimeout(() => {
          if (window.MathJax.typesetPromise) {
            // MathJax v3 API
            window.MathJax.typesetPromise().catch((err) => {
              console.error('MathJax typesetting failed: ', err);
            });
          } else if (window.MathJax.Hub) {
            // Fallback for MathJax v2 API
            window.MathJax.Hub.Queue(['Typeset', window.MathJax.Hub]);
          }
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
