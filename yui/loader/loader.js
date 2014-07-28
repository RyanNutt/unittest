YUI.add('moodle-qtype_javaunittest-loader', function(Y) {
    var ModulenameNAME = 'javaunittest_loader';
    var MODULENAME = function() {
        MODULENAME.superclass.constructor.apply(this, arguments);
    };
    Y.extend(MODULENAME, Y.Base, {
        initializer : function(config) { // 'config' contains the parameter values
            
        }
    }, {
        NAME : ModulenameNAME, 
        ATTRS : {
                 aparam : {}
        } 
    });
    M.javaunittest_loader = M.javaunittest_loader || {}; // This line use existing name path if it exists, otherwise create a new one. 
        // This is to avoid to overwrite previously loaded module with same name.
    M.javaunittest_loader.init = function(config) { // 'config' contains the parameter values
        
        return new MODULENAME(config); // 'config' contains the parameter values
    };
    
//    var editor = ace.edit("description");
//var textarea = $('textarea[name="description"]').hide();
//editor.getSession().setValue(textarea.val());
//editor.getSession().on('change', function(){
//  textarea.val(editor.getSession().getValue());
//});
    
    M.javaunittest_loader.edit_page = function(config) {
        var elGiven = document.getElementById('id_givencode');
        var divGiven = document.createElement('div');
        divGiven.setAttribute('id', 'given_code');
        divGiven.setAttribute('style', 'height:' + elGiven.scrollHeight + 'px;width:' + elGiven.scrollWidth + 'px;');
        document.getElementById('id_givencode').parentNode.insertBefore(divGiven, elGiven); 
        
        var edGiven = ace.edit("given_code");
        edGiven.getSession().setMode("ace/mode/java");
        edGiven.setFontSize(16);
        edGiven.getSession().setValue(elGiven.value);
        edGiven.getSession().on('change', function() {
            elGiven.value = edGiven.getSession().getValue();
        });
        
        elGiven.style.display = 'none'; 
        
        var elJunit = document.getElementById('id_junitcode');
        var divJunit = document.createElement('div');
        divJunit.setAttribute('id', 'junit_code');
        divJunit.setAttribute('style', 'height:' + elJunit.scrollHeight + 'px;width:' + elJunit.scrollWidth + 'px;');
        document.getElementById('id_junitcode').parentNode.insertBefore(divJunit, elJunit);
        
        var edJunit = ace.edit('junit_code');
        edJunit.getSession().setMode('ace/mode/java');
        edJunit.setFontSize(16);
        edJunit.getSession().setValue(elJunit.value);
        edJunit.getSession().on('change', function() {
            elJunit.value = edJunit.getSession().getValue();
        });
        elJunit.style.display = 'none'; 
        
        return new MODULENAME(config); 
    }
    
    M.javaunittest_loader.question_page = function(config) {
        var el = document.getElementById(config.element);
        var div = document.createElement('div');
        div.setAttribute('style', 'height:' + el.scrollHeight + 'px;width:' + el.scrollWidth + 'px;');
        div.setAttribute('id', 'editor_' + config.element);
        el.parentNode.insertBefore(div, el); 
        
        var ed = ace.edit(div);
        ed.getSession().setMode('ace/mode/java');
        ed.setFontSize(16);
        ed.getSession().setValue(el.value);
        ed.getSession().on('change', function(e) {
            document.getElementById(config.element).value = ace.edit('editor_' + config.element).getSession().getValue(); 
        });
        ed.setReadOnly(el.readOnly); 
        el.style.display = 'none'; 

        return new MODULENAME(config); 
    }
    
  }, '@VERSION@', {
      requires:['base']
  });