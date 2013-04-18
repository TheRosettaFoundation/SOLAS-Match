<script type="text/javascript">
    var MAX_SEGMENTS = 10;
    var CURR_SEGMENTS = 2;
    var TOTAL_WORD_COUNT = 0;

    $(document).ready(function() {     
        var segmentationElements = document.getElementById('segmentationElements');        
        var formSelect = document.createElement('select');
        formSelect.setAttribute('name', 'segmentationValue');
        formSelect.setAttribute('onchange', "segmentSelectChange(this);");
        TOTAL_WORD_COUNT = $('#totalWordCount').val();
        var defaultWordCount = TOTAL_WORD_COUNT / CURR_SEGMENTS;
        defaultWordCount = parseInt(defaultWordCount);

        for(var i=0; i < MAX_SEGMENTS-1; ++i) {
            var optionNode = document.createElement('option');
            optionNode.setAttribute('value', i+2);
            optionNode.innerHTML += (i+2);
            formSelect.appendChild(optionNode);

            if (i < CURR_SEGMENTS) {
                $('#wordCount_' + i).val(defaultWordCount);
            }
        }        
        segmentationElements.appendChild(formSelect); 
    }) 

    function segmentSelectChange(node) {
        var index = node.selectedIndex; 
        var value = parseInt(node.options[index].value);
        var templateNode = document.getElementById('taskUploadTemplate_0');
        var taskSegments = document.getElementById('taskSegments');
        var defaultWordCount = Math.round(TOTAL_WORD_COUNT / value);

        if(value < CURR_SEGMENTS) { 
            for(var i=CURR_SEGMENTS; i > 0; i--) {
                if (i > value) {
                    var del = document.getElementById('taskUploadTemplate_' + (i-1));
                    taskSegments.removeChild(del);
                } else {
                    $('#wordCount_' + (i-1)).val(defaultWordCount);
                }
            }

        } else if(value > CURR_SEGMENTS) {
            for(var i=0 ; i < value; i++) {
                if (i >= CURR_SEGMENTS) {
                    var clonedNode = templateNode.cloneNode(true);
                    var inputs = clonedNode.getElementsByTagName('input');
                    clonedNode.setAttribute('id',clonedNode.getAttribute("id").replace("0",i));
                    for(var j=0; j < inputs.length; j++){
                        inputs.item(j).setAttribute('id', inputs.item(j).getAttribute('id').replace("0", i));
                        inputs.item(j).setAttribute('name', inputs.item(j).getAttribute('id'));
                    }
                    taskSegments.appendChild(clonedNode);
                }

                $('#wordCount_' + i).val(defaultWordCount);
            }             
        }  
        CURR_SEGMENTS = value;              
    }
</script>
