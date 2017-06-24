CKEDITOR.dialog.add( 'xsltDialog', function( editor ) {
    return {
        title: 'XSLT properties',
        minWidth: 400,
        minHeight: 200,
        contents: [
            {
                id: 'tab-basic',
                label: 'Settings',
                elements: [
                    {
                        type: 'text',
                        id: 'xslt',
                        label: 'Path to XML source',
                        validate: CKEDITOR.dialog.validate.notEmpty( "XML source field cannot be empty." )
                    },
                    {
                        type: 'text',
                        id: 'stylesheet',
                        label: 'Path to XSL stylesheet',
                        validate: CKEDITOR.dialog.validate.notEmpty( "XSL stylesheet field cannot be empty." )
                    }
                ]
            }
        ],
        onOk: function() {
            var dialog = this;

            var xslt = editor.document.createElement( 'xslt' );
            xslt.setAttribute( 'stylesheet', dialog.getValueOf( 'tab-basic', 'stylesheet' ) );
            xslt.setText( dialog.getValueOf( 'tab-basic', 'xslt' ) );

            editor.insertElement( xslt );
        }
    };
});
