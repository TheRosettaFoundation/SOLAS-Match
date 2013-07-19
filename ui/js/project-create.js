<script type="text/javascript">
    var fields = 0;
    $(window).load(function () {
      fields = parseInt(jQuery('#targetLanguageArraySize').val())-1;
      
      if(!isNaN(fields) && fields > 0) {
            jQuery('#removeBottomTargetBtn')
                    .css('visibility', 'visible')
                    .attr('disabled', false);
            
            var totalTargetLanguages = parseInt(jQuery('#targetLanguageArraySize').val());
            for(var iter=0; iter < totalTargetLanguages; iter++) {
                if(jQuery('#segmentation_'+iter).is(':checked')) {
                    isEnabledArray[iter] = true;
                    segmentationEnabled[iter];
                }
            }
      } 
    });

    var MAX_FIELDS = 10;
    var isRemoveButtonHidden = true;

    var isEnabledArray = new Array(false); 
    
    function addNewTarget() {

        if(isRemoveButtonHidden) {
            jQuery('#removeBottomTargetBtn')
                    .css('visibility', 'visible')
                    .attr('disabled', false);
            
            isRemoveButtonHidden = false;
        }

        if(fields < MAX_FIELDS) {
            
            var clonedTarget = jQuery('#targetLanguageTemplate_0').clone();
            var clonedHorizLine = jQuery('#horizontalLine_0').clone();
            
            clonedHorizLine.attr('id', 'horizontalLine_' + (++fields));
            
            clonedTarget.attr('id', 'targetLanguageTemplate_' + fields);
            
            clonedTarget.find('#targetLanguage_0')
                        .attr('name', 'targetLanguage_' + fields)
                        .attr('id', 'targetLanguage_' + fields);   

            clonedTarget.find('#targetCountry_0')
                        .attr('name', 'targetCountry_' + fields)
                        .attr('id', 'targetCountry_' + fields);
            
            clonedTarget.find('#segmentation_0')
                        .attr('name', 'segmentation_' + fields)
                        .attr('onchange', 'segmentationEnabled(' + fields + ')')
                        .attr('id', 'segmentation_' + fields)
                        .prop("disabled", false)
                        .prop("checked", false);                        
            
            clonedTarget.find('#translation_0')
                        .attr('name', 'translation_' + fields)
                        .attr('id', 'translation_' + fields)
                        .prop("disabled", false)
                        .prop("checked", true);
                
            clonedTarget.find('#proofreading_0')
                        .attr('name', 'proofreading_' + fields)
                        .attr('id', 'proofreading_' + fields)
                        .prop("disabled", false)
                        .prop("checked", true);
            

            jQuery('#horizontalLine_' + (fields-1)).after(clonedTarget);            
            jQuery('#targetLanguageTemplate_' + fields).after(clonedHorizLine);            
          
            isEnabledArray.push(false);

            jQuery('#targetLanguageArraySize').attr('value', parseInt(jQuery('#targetLanguageArraySize').val()) + 1);
        }

        if(fields == MAX_FIELDS) {
            jQuery('#alertinfo').css('display', 'block');
            jQuery('#addMoreTargetsBtn').attr('disabled', true);
        }            
    } 


    function removeNewTarget() {    
        var id = fields;  
        
        jQuery("#targetLanguageTemplate_" + id).remove();
        jQuery("#horizontalLine_" + id).remove();
        isEnabledArray.pop();

        if(fields == MAX_FIELDS) {
            jQuery('#addMoreTargetsBtn').attr('disabled', false);
            jQuery('#alertinfo').css('display', 'none');
        }

        fields--;
        jQuery('#targetLanguageArraySize').attr('value', parseInt(jQuery('#targetLanguageArraySize').val()) - 1);

        if(fields == 0) {            
            jQuery('#removeBottomTargetBtn')
                    .css('visibility', 'hidden')
                    .attr('disabled', true);
            isRemoveButtonHidden = true;
        }         
    }

    function segmentationEnabled(index)
    {
        if(!isEnabledArray[index]) {
            jQuery('#translation_' + index)
                    .attr('checked', false)
                    .attr('disabled', true);
                        
            jQuery('#proofreading_' + index)
                    .attr('checked', false)
                    .attr('disabled', true);
            
            isEnabledArray[index] = true;
        } else {
            jQuery('#translation_' + index)
                    .prop('checked', true)
                    .attr('disabled', false);
            
            jQuery('#proofreading_' + index)
                    .prop('checked', true)
                    .attr('disabled', false);

            isEnabledArray[index] = false;
        }
    }    
    
    function checkFormat()
    {
        var inputElement = jQuery("input[type='file']");
        var filePath = inputElement.val();
        var pos = filePath.lastIndexOf(".")+1;
        var extension = filePath.substring(pos);
        if(extension == 'pdf') {
            return alert('Please note that (.pdf) documents are difficult to work with in translation because they must first be converted to an editable format. Are you sure want you want to upload a (.pdf)?');
        }
    }
    
    function checkWordCount()
    {
        var segCheckedSize = isEnabledArray.length;
        var checkedCount = 0;
        
        for(var i=0; i < segCheckedSize; i++) {
            if(isEnabledArray[i]) checkedCount++;
        }
        
        if(checkedCount === segCheckedSize) return;
        
        var wordCount = jQuery("#word_count").val();
        if(wordCount > 3500) {
            return confirm("Please note that the word count of your file is excessively high - the recommended limit is 3000 words. We suggest you create a segmentation task instead. Are you sure you want to continue?");
        }
    }
</script>