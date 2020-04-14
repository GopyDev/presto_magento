/**
 * @author    Marcin Frymark
 * @email     contact@alekseon.com
 * @company   Alekseon
 * @website   www.alekseon.com
 */
var ProductRow = Class.create();
ProductRow.prototype = {
    initialize: function(form, savingMsg, failureMsg){
        this.onSave  = this.saveSuccess.bindAsEventListener(this);
        this.onFailure = this.saveFailure.bindAsEventListener(this);
        this.savingMsg = savingMsg;
        this.failureMsg = failureMsg;
        this.form = form;
    },

    save: function(saveUrl, rowId){
   
        $('save_button_message_' + rowId).update(this.savingMsg);
        $('save_button_' + rowId).addClassName('disabled');

        var request = new Ajax.Request(
            saveUrl,
            {
                loaderArea: false,
                method: 'post',
                onComplete: '',
                onSuccess: this.onSave,
                onFailure: this.onFailure,
                evalScripts: true,
                parameters: Form.serialize(this.form)
            }
        );
    },
    
    saveSuccess: function(transport){
        response = eval('(' + transport.responseText + ')');;
        rowId = response.rowId;
        successMsg = response.successMsg;

        if (successMsg) {
            if (response.updateData) {
                for (var data in response.updateData) {
                    if ($(data)) {
                        $(data).parentNode.innerHTML = response.updateData[data];
                        parseAjaxResponse(response.updateData[data]);
                    }
                }
            }

            $('save_button_message_' + rowId).update(successMsg);
            $('save_button_' + rowId).addClassName('disabled');
        } else {
            $('save_button_message_' + rowId).update(response.errorMsg);
            $('save_button_' + rowId).removeClassName('disabled');
        }
    },

    saveFailure: function(transport){
        alert(this.failureMsg);
    }    
}

/* fix for openGridRow native function */
function openGridRow(grid, event) {
    var element = Event.findElement(event, 'tr');

    if (Event.element(event).type != 'checkbox') {
        if(['img', 'a', 'input', 'select', 'option', 'img'].indexOf(Event.element(event).tagName.toLowerCase())!=-1) {
            var checkbox = Element.select(element, 'input');
            if(checkbox[0] && !checkbox[0].disabled) {
                grid.setCheckboxChecked(checkbox[0], true);
            }
            return;
        }
    }
    if (element.title) {
        setLocation(element.title);
    }
}

/* Fix required for parsing <script> tags */
function parseAjaxResponse(mixedResponse)
{
    var source = mixedResponse;
    var scripts = new Array();
    while(source.indexOf('<script') > -1 || source.indexOf('</script') > -1) {
        var s = source.indexOf('<script');
        var s_e = source.indexOf('>', s);
        var e = source.indexOf('</script', s);
        var e_e = source.indexOf('>', e);
        scripts.push(source.substring(s_e+1, e));
        source = source.substring(0, s) + source.substring(e_e+1);
    }
    for(var x=0; x<scripts.length; x++) {
        try {
            eval(scripts[x]);
        }
        catch(ex) {
        }
    }
    return source;
}