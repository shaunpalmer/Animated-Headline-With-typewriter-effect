import { registerBlockType } from '@wordpress/blocks';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, ColorPalette, RangeControl, SelectControl } from '@wordpress/components';
import metadata from './block.json';

registerBlockType(metadata.name, {
    edit: ({ attributes, setAttributes }) => {
        const {
            message,
            fontColor,
            fontSize,
            animationSpeed,
            backgroundColor,
            googleFont,
            animationStyle,
        } = attributes;

        const fonts = [
            { label: 'Montserrat', value: 'Montserrat' },
            { label: 'Roboto', value: 'Roboto' },
            { label: 'Open Sans', value: 'Open Sans' },
            { label: 'Lato', value: 'Lato' },
            { label: 'Oswald', value: 'Oswald' },
        ];

        const animationStyles = [
            { label: 'Typewriter', value: 'typewriter' },
            { label: 'Fly In from Left', value: 'fly-in-left' },
            { label: 'Bounce on Hover', value: 'bounce-hover' },
        ];

        return (
            <div>
                <InspectorControls>
                    <PanelBody title="Headline Settings">
                        <TextControl
                            label="Headline Message"
                            value={message}
                            onChange={(val) => setAttributes({ message: val })}
                        />
                        <RangeControl
                            label="Font Size (px)"
                            value={fontSize}
                            onChange={(val) => setAttributes({ fontSize: val })}
                            min={10}
                            max={100}
                        />
                        <SelectControl
                            label="Google Font"
                            value={googleFont}
                            options={fonts}
                            onChange={(val) => setAttributes({ googleFont: val })}
                        />
                    </PanelBody>
                    <PanelBody title="Animation Settings" initialOpen={false}>
                        <SelectControl
                            label="Animation Style"
                            value={animationStyle}
                            options={animationStyles}
                            onChange={(val) => setAttributes({ animationStyle: val })}
                        />
                        <RangeControl
                            label="Animation Speed (ms)"
                            value={animationSpeed}
                            onChange={(val) => setAttributes({ animationSpeed: val })}
                            min={50}
                            max={500}
                            step={10}
                        />
                    </PanelBody>
                    <PanelBody title="Color Settings" initialOpen={false}>
                        <p><strong>Font Color</strong></p>
                        <ColorPalette
                            value={fontColor}
                            onChange={(val) => setAttributes({ fontColor: val })}
                        />
                        <p><strong>Background Color</strong></p>
                        <ColorPalette
                            value={backgroundColor}
                            onChange={(val) => setAttributes({ backgroundColor: val })}
                        />
                    </PanelBody>
                </InspectorControls>
                <div style={{
                    fontFamily: googleFont,
                    fontSize: `${fontSize}px`,
                    color: fontColor,
                    backgroundColor: backgroundColor,
                    padding: '10px'
                }}>
                    {message}
                </div>
            </div>
        );
    },
    save: () => {
        return null; // Rendering is handled by PHP
    },
});