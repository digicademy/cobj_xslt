CKEDITOR.plugins.add( 'xslt', {
    icons: 'xslt',
    lang: 'en',
    init: function( editor ) {
        editor.addCommand( 'xslt', new CKEDITOR.dialogCommand( 'xsltDialog', {
            allowedContent: 'xslt[stylesheet]',
            requiredContent: 'xslt[stylesheet]'
        } ) );
        editor.ui.addButton( 'Xslt', {
            label: "XSLT",
            command: 'xslt',
            toolbar: 'insert'
        });

        CKEDITOR.dialog.add( 'xsltDialog', this.path + 'dialogs/xslt.js' );
//        CKEDITOR.template + ' .cke_button__xslt_label { display: inline !important; }';
    }
});

/*
Currently it is not possibly to display a text label besides a custom button in TYPO3's CKEDITOR implementation.
For showing texts besides a button (like with the "source" button the CSS styles in the skin would have to be
overriden by a custom CSS stylesheet in the head of CKEDITORS iframe in the backen. But there is no way to
add a custom stylesheet to this iframe.
Workaround: Since the plugin.js of this extension is executed in the head of the relevant iframe, the following
functions directly inject a CSS rule (in a cross browser way) into the head of the iframe.
*/

var addRule;

if (typeof document.styleSheets != "undefined" && document.styleSheets) {
    addRule = function(selector, rule) {
        var styleSheets = document.styleSheets, styleSheet;
        if (styleSheets && styleSheets.length) {
            styleSheet = styleSheets[styleSheets.length - 1];
            if (styleSheet.addRule) {
                styleSheet.addRule(selector, rule)
            } else if (typeof styleSheet.cssText == "string") {
                styleSheet.cssText = selector + " {" + rule + "}";
            } else if (styleSheet.insertRule && styleSheet.cssRules) {
                styleSheet.insertRule(selector + " {" + rule + "}", styleSheet.cssRules.length);
            }
        }
    }
} else {
    addRule = function(selector, rule, el, doc) {
        el.appendChild(doc.createTextNode(selector + " {" + rule + "}"));
    };
}

function createCssRule(selector, rule, doc) {
    doc = doc || document;
    var head = doc.getElementsByTagName("head")[0];
    if (head && addRule) {
        var styleEl = doc.createElement("style");
        styleEl.type = "text/css";
        styleEl.media = "screen";
        head.appendChild(styleEl);
        addRule(selector, rule, styleEl, doc);
        styleEl = null;
    }
};

createCssRule(".cke_button__xslt_label", "display: inline !important;");
