(function (blocks, editor, components) {
    const el = wp.element.createElement;
    const { InspectorControls } = editor;
    const { PanelBody, TextControl, ColorPicker, ToggleControl } = components;

    blocks.registerBlockType('ssm/styled-map', {
        title: 'SWELL Styled Map',
        icon: 'location-alt',
        category: 'swell',
        attributes: {
            src: { type: 'string', default: '' },
            color: { type: 'string', default: '#0091ff' },
            height: { type: 'string', default: '450' },
            fullWidth: { type: 'boolean', default: true },
            invert: { type: 'boolean', default: false },
            effect: { type: 'boolean', default: true }
        },

        edit: function (props) {
            const { attributes, setAttributes } = props;

            return [
                el(InspectorControls, { key: 'controls' },
                    el(PanelBody, { title: 'マップ設定', initialOpen: true },
                        el(TextControl, {
                            label: '埋め込みURL (src)',
                            value: attributes.src,
                            onChange: (val) => setAttributes({ src: val }),
                            help: 'Googleマップのiframeからsrc属性のURLをコピーして貼り付けてください。'
                        }),
                        el(TextControl, {
                            label: '高さ (例: 800, 70vh, 100%)',
                            value: attributes.height,
                            onChange: (val) => setAttributes({ height: val }),
                            help: '数値のみ入力した場合はピクセル(px)として扱われます。'
                        }),
                        el('div', { style: { marginBottom: '15px' } },
                            el('label', { className: 'components-base-control__label' }, 'オーバーレイ色'),
                            el(ColorPicker, {
                                color: attributes.color,
                                onChangeComplete: (val) => setAttributes({ color: val.hex }),
                                disableAlpha: true
                            })
                        ),
                        el(ToggleControl, {
                            label: '全幅表示',
                            checked: attributes.fullWidth,
                            onChange: (val) => setAttributes({ fullWidth: val })
                        }),
                        el(ToggleControl, {
                            label: '黒い地図（反転）',
                            checked: attributes.invert,
                            onChange: (val) => setAttributes({ invert: val })
                        }),
                        el(ToggleControl, {
                            label: 'フェード出現エフェクト',
                            checked: attributes.effect,
                            onChange: (val) => setAttributes({ effect: val })
                        })
                    )
                ),
                // エディター内プレビュー（実際の表示はサーバーサイド）
                el('div', { className: 'swell-styled-map-preview' },
                    el('p', { style: { textAlign: 'center', background: '#f0f0f0', padding: '20px' } },
                        attributes.src ? '地図が表示されます（プレビュー）' : 'URLを入力してください'
                    )
                )
            ];
        },

        save: function (props) {
            // ショートコードとして出力するか、PHPでレンダリングするためにnullを返す（サーバーサイドレンダリング）
            return null;
        }
    });
})(
    window.wp.blocks,
    window.wp.blockEditor || window.wp.editor,
    window.wp.components
);
