var EqnEditors = [];

function ProcessEditors(clickElement) {

    // Readd all removed EqnEditors to the DOM.
    for (var i = 0; i < EqnEditors.length; ++i) {
        var editor = EqnEditors[i]
        $(editor.owner).append(editor);
    }

    var recur = 0;

    function UpdateEditor() {

        // Grab the cke dialog that just opened.
        var $visibleEditor = $('[name="CCEquationEditor"]:visible');

        recur++;
        if (recur >= 100) {
            alert('Editor did not open.');
            return;
        }

        // Also wait for the EqEditor and targetArea to spawn.  Sometimes this all happens too fast for the initial load.
        if($visibleEditor.length === 0 || !window.EqEditor || !window.EqEditor.targetArea) {
            setTimeout(UpdateEditor, 10);
            return;
        }

        // Remove all non-visible EqnEditors from the DOM.  This hides their image maps, allowing proper menu selection for the remaining visible editor.
        $('[name="CCEquationEditor"]:not(:visible)').each(
            function () {
                var $this = $(this);

                // Establish initial array storage of all editors
                if (EqnEditors.indexOf($this[0]) === -1) {
                    EqnEditors.push($this[0]);
                }

                $this[0].owner = $this.parent()[0];
                $this.remove();
            }
        );

        // Redirect the input and preview fields.
        window.EqEditor.targetArea.equation_input = $visibleEditor.find('textarea')[0];
        window.EqEditor.targetArea.equation_preview =
            $visibleEditor.find('img[id*="CCequ"]')[0];

        // If this is a click-in, set the initial values to the clicked image content.
        if (clickElement) {

            $(window.EqEditor.targetArea.equation_input).val($(clickElement.target).attr('alt'));
            $(window.EqEditor.targetArea.equation_preview).attr('src',
                $(clickElement.target).attr('src'));
        }
    };

    setTimeout(UpdateEditor, 10);
};

$(document).on('click', '.editor .cke_button__eqneditor',
    function () {
        // Initialize everything needed for the dblclick action when editing an existing equation in an editor.
        $(this).parents('.editor').find('iframe.cke_wysiwyg_frame').each(
            function () {
                var $contents = $(this).contents();
                $contents.off('dblclick');
                $contents.on('dblclick', 'img[src*="gif.latex"]', ProcessEditors);
            }
        );

        // Go through each editor and do some magic hide-y swap-y stuff.
        ProcessEditors();
    }
);
