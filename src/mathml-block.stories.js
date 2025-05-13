import React from 'react';
import { RawHTML } from '@wordpress/element'; // To render HTML content
import { v4 as uuid } from 'uuid'; // For unique IDs if needed by MathJax

// Mock WordPress environment for Storybook
// This is a simplified mock. More complex blocks might need more extensive mocking.
global.wp = {
  i18n: {
    __: (text) => text,
  },
  element: React, // Use React for wp.element
  // Add other wp dependencies if your component uses them
};

// Mock MathJax for Storybook environment if it's not loaded globally
if (typeof window !== 'undefined' && !window.MathJax) {
  window.MathJax = {
    Hub: {
      Queue: (tasks) => {
        // In a real Storybook, you might want to actually render MathJax
        // or provide a visual placeholder. For now, this is a no-op.
        console.log('MathJax.Hub.Queue called with:', tasks);
      },
      Typeset: () => {
        console.log('MathJax.Hub.Typeset called');
      }
    },
  };
}

const renderMathMLStory = (id, formula) => {
  // Attempt to trigger MathJax rendering for the story
  // This might need adjustment based on how MathJax is loaded and initialized in your project
  setTimeout(() => {
    if (window.MathJax && window.MathJax.Hub && document.getElementById(id)) {
      window.MathJax.Hub.Queue(['Typeset', window.MathJax.Hub, document.getElementById(id)]);
    }
  }, 100);
  return <RawHTML id={id}>{formula}</RawHTML>;
};


// --- Storybook Configuration ---
export default {
  title: 'Components/MathMLBlock',
  // component: MathMLBlockEdit, // If you had an Edit component exported
  argTypes: {
    formula: { control: 'text' },
    isSelected: { control: 'boolean' },
  },
};

// --- Template for Edit Mode ---
const EditTemplate = ({ formula, isSelected, className }) => {
  const id = 'story-' + uuid(); // Unique ID for MathJax

  // Simplified version of the block's edit function
  if (isSelected) {
    return (
      <div className={className || ''}>
        <label htmlFor={id}>{wp.i18n.__('MathML formula:', 'mathml-block')}</label>
        <textarea
          id={id}
          className="mathml-formula"
          value={formula}
          onChange={(e) => console.log('Textarea changed:', e.target.value)} // Storybook action placeholder
          style={{ width: '100%', minHeight: '80px', fontFamily: 'monospace' }}
        />
      </div>
    );
  }
  // Non-selected state (view in editor)
  return (
    <div className="mathml-block" style={{ border: '1px solid #eee', padding: '10px' }}>
      {renderMathMLStory(id, formula)}
    </div>
  );
};

// --- Template for Save/Frontend View ---
const SaveTemplate = ({ formula, className }) => {
  const id = 'story-save-' + uuid();
  // Simplified version of the block's save function
  return (
    <div className={className || 'mathml-block'} style={{ border: '1px solid #ccc', padding: '10px' }}>
      {renderMathMLStory(id, formula)}
    </div>
  );
};


// --- Stories ---
export const EditModeSelected = EditTemplate.bind({});
EditModeSelected.args = {
  formula: '<math xmlns="http://www.w3.org/1998/Math/MathML"><mrow><mi>x</mi><mo>=</mo><mfrac><mrow><mo>-</mo><mi>b</mi><mo>&#xB1;</mo><msqrt><mrow><msup><mi>b</mi><mn>2</mn></msup><mo>-</mo><mn>4</mn><mi>a</mi><mi>c</mi></mrow></msqrt></mrow><mrow><mn>2</mn><mi>a</mi></mrow></mfrac></mrow></math>',
  isSelected: true,
  className: 'wp-block-mathml-mathmlblock',
};

export const EditModeView = EditTemplate.bind({});
EditModeView.args = {
  formula: '<math xmlns="http://www.w3.org/1998/Math/MathML"><mrow><mi>E</mi><mo>=</mo><mi>m</mi><msup><mi>c</mi><mn>2</mn></msup></mrow></math>',
  isSelected: false,
  className: 'wp-block-mathml-mathmlblock',
};

export const FrontendView = SaveTemplate.bind({});
FrontendView.args = {
  formula: '<math xmlns="http://www.w3.org/1998/Math/MathML"><mrow><munderover><mo>&#x222B;</mo><mrow><mo>-</mo><mo>&#x221E;</mo></mrow><mrow><mo>&#x221E;</mo></mrow></munderover><msup><mi>e</mi><mrow><mo>-</mo><msup><mi>x</mi><mn>2</mn></msup></mrow></msup><mi>d</mi><mi>x</mi><mo>=</mo><msqrt><mi>&#x3C0;</mi></msqrt></mrow></math>',
  className: 'wp-block-mathml-mathmlblock aligncenter', // Example with an alignment class
};

export const EmptyFormulaEdit = EditTemplate.bind({});
EmptyFormulaEdit.args = {
  formula: '',
  isSelected: true,
  className: 'wp-block-mathml-mathmlblock',
};

export const EmptyFormulaView = SaveTemplate.bind({});
EmptyFormulaView.args = {
  formula: '',
  className: 'wp-block-mathml-mathmlblock',
};
