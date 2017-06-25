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
                        validate: CKEDITOR.dialog.validate.notEmpty( "XML source field cannot be empty." ),

                        setup: function( element ) {
                            this.setValue( element.getText() );
                        },

                        commit: function( element ) {
                            element.setText( this.getValue() );
                        }
                    },
                    {
                        type: 'text',
                        id: 'stylesheet',
                        label: 'Path to XSL stylesheet',
                        validate: CKEDITOR.dialog.validate.notEmpty( "XSL stylesheet field cannot be empty." ),

                        setup: function( element ) {
                            this.setValue( element.getAttribute( "stylesheet" ) );
                        },

                        commit: function( element ) {
                            element.setAttribute( "title", this.getValue() );
                        }
                    }
                ]
            }
        ],

        onShow: function() {
            var selection = editor.getSelection();
            var element = selection.getStartElement();

            if ( element )
                element = element.getAscendant( 'xslt', true );

            if ( !element || element.getName() != 'xslt' ) {
                element = editor.document.createElement( 'xslt' );
                this.insertMode = true;
            }
            else
                this.insertMode = false;

            this.element = element;
            if ( !this.insertMode )
                this.setupContent( this.element );
        },

        onOk: function() {
            var dialog = this;

            var xslt = editor.document.createElement( 'xslt' );
            xslt.setAttribute( 'stylesheet', dialog.getValueOf( 'tab-basic', 'stylesheet' ) );
            xslt.setText( dialog.getValueOf( 'tab-basic', 'xslt' ) );

            editor.insertElement( xslt );
        }

    };
});
