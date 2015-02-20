/**
 * Created by Sam <sam@samwaters.com> on 20/02/15.
 */
CKEDITOR.plugins.add("cme-placeholder", {
    init: function(editor) {
        editor.ui.addRichCombo("cme-placeholder", {
            label: "Placeholder",
            title: "Placeholder",
            multiSelect: false,
            init: function() {
                var xhr = new XMLHttpRequest();
                xhr.open("GET", editor.config.cmePlaceholderEndpoint, false);
                xhr.send();
                if(xhr.status == 200) {
                    var items = JSON.parse(xhr.responseText);
                    if(typeof items == "object") {
                        for(var i in items) {
                            this.add(items[i].value, items[i].text, items[i].label);
                        }
                    }
                }
            },
            panel: {
                css : [ editor.config.contentsCss, CKEDITOR.getUrl( CKEDITOR.skin.getPath("editor") + 'editor.css' ) ]
            },
            onClick: function(value) {
                editor.focus();
                editor.fire("saveSnapshot");
                editor.insertHtml(value);
                editor.fire("saveSnapshot");
            }
        });
    }
});
