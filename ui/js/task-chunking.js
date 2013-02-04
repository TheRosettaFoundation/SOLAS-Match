<script language='javascript'>
    var MAX_CHUNKS = 10;
    var CURR_CHUNKS = 2;

    $(document).ready(function() {     
        var chunkElements = document.getElementById('chunkingElements');        
        var formSelect = document.createElement('select');
        formSelect.setAttribute('name', 'chunkValue');
        formSelect.setAttribute('onchange', "chunkSelectChange(this);")

        for(var i=0; i < MAX_CHUNKS-1; ++i) {
            var optionNode = document.createElement('option');
            optionNode.setAttribute('value', i+2);
            optionNode.innerHTML += (i+2);
            formSelect.appendChild(optionNode);            
        }        
        chunkElements.appendChild(formSelect); 
    }) 

    function taskTypeSelection(type) {
        var translationCheckBox = document.getElementById('translation_0');
        var proofreadingCheckBox = document.getElementById('proofreading_0');
        var posteditingCheckBox = document.getElementById('postediting_0');

        if(type == 'translation') {
            if(translationCheckBox.checked) {            
                translationCheckBox.disabled = false;
                proofreadingCheckBox.disabled = true;
                posteditingCheckBox.disabled = true;            
            } else {
                translationCheckBox.disabled = false;
                proofreadingCheckBox.disabled = false;
                posteditingCheckBox.disabled = false;             
            }
        } else if(type == 'proofreading') {
            if(proofreadingCheckBox.checked) {
                proofreadingCheckBox.disabled = false;            
                translationCheckBox.disabled = true;
                posteditingCheckBox.disabled = true;
            } else {
                proofreadingCheckBox.disabled = false;            
                translationCheckBox.disabled = false;
                posteditingCheckBox.disabled = false;            
            }
        } else if(type == 'postediting') {
            if(posteditingCheckBox.checked) {            
                posteditingCheckBox.disabled = false;            
                translationCheckBox.disabled = true;
                proofreadingCheckBox.disabled = true;
            } else {
                posteditingCheckBox.disabled = false;            
                translationCheckBox.disabled = false;
                proofreadingCheckBox.disabled = false;            
            }            
        }    
    }

    function chunkSelectChange(node) {
        var index = node.selectedIndex; 
        var value = parseInt(node.options[index].value);
        var templateNode = document.getElementById('taskUploadTemplate_0');
        var taskChunks = document.getElementById('taskChunks');

        if(value < CURR_CHUNKS) { 
            for(var i=CURR_CHUNKS; i > value; i--) {
                var del = document.getElementById('taskUploadTemplate_' + (i-1));
                taskChunks.removeChild(del);
            }

        } else if(value > CURR_CHUNKS) {
            for(var i=CURR_CHUNKS; i < value; i++) {
                var clonedNode = templateNode.cloneNode(true);
                var inputs = clonedNode.getElementsByTagName('input');
                clonedNode.setAttribute('id',clonedNode.getAttribute("id").replace("0",i));
                for(var j=0; j < inputs.length; j++){
                    inputs.item(j).setAttribute('id', inputs.item(j).getAttribute('id').replace("0", i));
                    inputs.item(j).setAttribute('name', inputs.item(j).getAttribute('id'));
                }
                taskChunks.appendChild(clonedNode);
            }             
        }  
        CURR_CHUNKS = value;              
    } 
</script>
