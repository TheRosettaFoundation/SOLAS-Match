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
