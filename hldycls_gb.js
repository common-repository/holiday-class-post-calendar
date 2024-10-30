( function( blocks, element, components, blockEditor, serverSideRender  ) {

    const { registerBlockType } = blocks,
        el = element.createElement,
        // { ServerSideRender } = wp.components,
        // { serverSideRender: ServerSideRender } = wp,
        ServerSideRender = serverSideRender,
        Fragment = element.Fragment,
        InspectorControls = blockEditor.InspectorControls,
        TextControl = components.TextControl,
        RadioControl = components.RadioControl,
        useBlockProps = blockEditor.useBlockProps;

    registerBlockType( 'hldycls-pcldr/post-calendar', {
        apiVersion: 2,
        title: 'Holiday Class Post Calendar Block',
        icon: 'smiley',
        category: 'widgets',
        description: '※ブロックで表示されているスタイルの設定は、プラグインのデフォルトスタイルによるもので、オプションページで "class color" の設定がある場合はその設定で上書きされます。',

        attributes: {
            prtwidth: { type: 'string', default: '' },
            lang: { type: 'string', default: '' },
            wf: { type: 'string', default: '' },
            capt: { type: 'string', default: '' },
            footer: { type: 'string', default: '' },
            closewd: { type: 'string', default: '' },
            closel: { type: 'string', default: '' },
            anniver: { type: 'string', default: '' },
            monthly: { type: 'string', default: '' },
            postype: { type: 'string', default: '' },
            acvheader: { type: 'string', default: '' },
            acvoptorlist: { type: 'string', default: '' },
            daypostlink: { type: 'string', default: '' },
            myholidays: { type: 'string', default: '' },
            adddeldays: { type: 'string', default: '' },
            en_cache: { type: 'string', default: '' }
        },

        edit: function ( props ) {
            const blockProps = useBlockProps();

            return(
                el(
                    Fragment,
                    null,
                    el(
                        InspectorControls,
                        null,
                        el( 'div', { id: 'hldyclspcldr_sdbr'},
                            el(
                                TextControl,
                                {
                                    label: 'parent div width',
                                    help: '親ボックスの幅、デフォルトは"100%"、%, px, vw, etc. で指定',
                                    value: props.attributes.prtwidth,
                                    onChange: function( newValue ){ props.setAttributes( { prtwidth: newValue } ) }
                                }
                            ),
                            el(
                                RadioControl,
                                {
                                    label: '日本語か英語表示かの指定',
                                    selected: props.attributes.lang,
                                    options: [ { label: '日本語', value: 'j' },{ label: '英語', value: 'e' } ],
                                    onChange: function( newValue ){ props.setAttributes( { lang: newValue } ) }
                                }
                            ),
                            el(
                                RadioControl,
                                {
                                    label: '週の始めを日曜か月曜かの指定',
                                    selected: props.attributes.wf,
                                    options: [ { label: '日曜', value: 's' },{ label: '月曜', value: 'm' } ],
                                    onChange: function( newValue ){ props.setAttributes( { wf: newValue } ) }
                                }
                            ),
                            el(
                                TextControl,
                                {
                                    label: '表題 caption',
                                    help: '表題のcaptionに表示する文字列',
                                    value: props.attributes.capt,
                                    onChange: function( newValue ){ props.setAttributes( { capt: newValue } ); }
                                }
                            ),
                            el(
                                TextControl,
                                {
                                    label: 'footer 文字列',
                                    help: '下部に表示する文字列',
                                    value: props.attributes.footer,
                                    onChange: function( newValue ){ props.setAttributes( { footer: newValue } ); }
                                }
                            ),
                            el(
                                TextControl,
                                {
                                    label: '休日曜日指定',
                                    help: '独自休日を曜日で指定する場合。毎週水曜日だとか。1が日曜、7が土曜で1～7までの数字で指定。複数ももちろん可でその場合は","カンマ区切りで指定',
                                    value: props.attributes.closewd,
                                    onChange: function( newValue ){ props.setAttributes( { closewd: newValue } ); }
                                }
                            ),
                            el(
                                TextControl,
                                {
                                    label: '休日日付指定',
                                    help: '独自休日を日にちで指定。年月日８桁、月日４桁ないし３桁、日にち２桁以下で指定。日にちだけなら毎月に設定され、月日なら毎年。これも","カンマ区切り',
                                    value: props.attributes.closel,
                                    onChange: function( newValue ){ props.setAttributes( { closel: newValue } ); }
                                }
                            ),
                            el(
                                TextControl,
                                {
                                    label: '休日指定（アニバーサリー）',
                                    help: '上記2つとは別に特別な日を設定するときはこのanniverを使用し、毎年の事であれば４桁もしくは３桁、その年だけなら8桁使用でこの時の class は"lanniversary" になる、複数可、上に同じ',
                                    value: props.attributes.anniver,
                                    onChange: function( newValue ){ props.setAttributes( { anniver: newValue } ); }
                                }
                            ),
                            el(
                                RadioControl,
                                {
                                    label: '月別アーカイブのリストの表示',
                                    selected: props.attributes.monthly,
                                    options: [ { label: '表示', value: '1' },{ label: '表示しない', value: '0' } ],
                                    onChange: function( newValue ){ props.setAttributes( { monthly: newValue } ); }
                                }
                            ),
                            el(
                                TextControl,
                                {
                                    label: '月別アーカイブリストのヘッダー文字指定',
                                    value: props.attributes.acvheader,
                                    onChange: function( newValue ){ props.setAttributes( { acvheader: newValue } ); }
                                }
                            ),
                            el(
                                RadioControl,
                                {
                                    label: '月別アーカイブリスト表示がプルダウンかリストか',
                                    help: '※管理画面内でのブロックにおいての表示は、wp_get_archives の返り値が得られないため、本来のリストは表示されません',
                                    selected: props.attributes.acvoptorlist,
                                    options: [ { label: 'pull-down', value: '1' },{ label: 'list', value: '0' } ],
                                    onChange: function( newValue ){ props.setAttributes( { acvoptorlist: newValue } ); }
                                }
                            ),
                            el(
                                RadioControl,
                                {
                                    label: '投稿がある日にオンマウスで表示させるのはツールチップかその投稿へのリンクか',
                                    selected: props.attributes.daypostlink,
                                    options: [ { label: '投稿へのリンク', value: '1' },{ label: 'ツールチップ', value: '0' } ],
                                    onChange: function( newValue ){ props.setAttributes( { daypostlink: newValue } ); }
                                }
                            ),
                            el(
                                TextControl,
                                {
                                    label: 'post type',
                                    help: 'カレンダーに表示させる投稿の投稿タイプ、デフォルトは"post"。',
                                    value: props.attributes.postype,
                                    onChange: function( newValue ){ props.setAttributes( { postype: newValue } ); }
                                }
                            ),
                            el(
                                TextControl,
                                {
                                    label: 'my holiday list',
                                    help: 'デフォルトで設定されている日本の祝祭日ではなく、独自の祝祭日だけを表示する場合にその日付を","カンマで区切ったリストにして入力。デフォルトは空文字',
                                    value: props.attributes.myholidays,
                                    onChange: function( newValue ){ props.setAttributes( { myholidays: newValue } ); }
                                }
                            ),
                            el(
                                TextControl,
                                {
                                    label: 'デフォルトで設定されている祝祭日リストに加える、または削除する日付。',
                                    help: '年月日８ケタ、月日４ケタないし３ケタ、日にち２ケタ以下で指定。無視させる日付にだけ"-"を付けて","（半角カンマ）で区切って指定。例：3,929-,0927,20140911-。',
                                    value: props.attributes.adddeldays,
                                    onChange: function( newValue ){ props.setAttributes( { adddeldays: newValue } ); }
                                }
                            ),
                            el(
                                TextControl,
                                {
                                    label: 'キャッシュ設定',
                                    help: '"0"でdisable。ここで指定した数字がキャッシュファイル名の末尾に付加される。表示設定が違う場合など、異なる数値を指定することで複数のキャッシュファイルを設定できる',
                                    value: props.attributes.en_cache,
                                    onChange: function( newValue ){ props.setAttributes( { en_cache: newValue } ); }
                                }
                            ),
                        ),
                    ),
                    el(
                        'div',
                        blockProps,
                        el( ServerSideRender, {
                            block: 'hldycls-pcldr/post-calendar',
                            attributes: props.attributes
                        } )
                    )
                )
            );
        },
    } );
})(
    window.wp.blocks,
    window.wp.element,
    window.wp.components,
    window.wp.blockEditor,
    window.wp.serverSideRender
);
