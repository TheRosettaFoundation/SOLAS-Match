<script type="text/javascript">
    var fields = 0;
    var MAX_SECONDARY_LANGUAGES = 5;
    var isRemoveButtonHidden = true;
    
    window.onload = init;

    function init()
    {
        fields = jQuery("#extraSecondaryLanguages").children("span").length;
        jQuery("#secondaryLanguagesArraySize").attr("value", fields);

        
        if(fields == MAX_SECONDARY_LANGUAGES) {
            jQuery('#alertinfo').css('display', 'block');
            jQuery('#addNewSecondaryLanguageBtn').attr('disabled', true);
        }    
        
        if(isRemoveButtonHidden && fields != 0) {
            jQuery('#removeNewSecondaryLanguageBtn')
                    .css('visibility', 'visible')
                    .attr('disabled', false);
            
            isRemoveButtonHidden = false;
        }
    }

    function addNewSecondaryLanguage() {

        if(isRemoveButtonHidden) {           
            jQuery('#removeNewSecondaryLanguageBtn')
                    .css('visibility', 'visible')
                    .attr('disabled', false);
            
            isRemoveButtonHidden = false;
        }

        if(fields < MAX_SECONDARY_LANGUAGES) {
            
            var clonedTarget = jQuery('#userNativeLanguage').clone();
            
            clonedTarget.attr('id', 'newSecondaryLanguage' + fields);
            
            clonedTarget.find('#nativeLanguage')
                        .attr('name', 'secondaryLanguage_' + fields)           
                        .attr('id', 'secondaryLanguage_' + fields);   

            clonedTarget.find('#nativeCountry')
                        .attr('name', 'secondaryCountry_' + fields)
                        .attr('id', 'secondaryCountry_' + fields);
                
            if(jQuery('#newSecondaryLanguage' + (fields-1)).length) {
                jQuery('#newSecondaryLanguage' + (fields-1)).after(clonedTarget);
            } else {
                jQuery('#extraSecondaryLanguages').append(clonedTarget);
            }

            fields++;   
            jQuery('#secondaryLanguagesArraySize').attr('value', parseInt(jQuery('#secondaryLanguagesArraySize').val()) + 1);
        }

        if(fields == MAX_SECONDARY_LANGUAGES) {
            jQuery('#alertinfo').css('display', 'block');
            jQuery('#addNewSecondaryLanguageBtn').attr('disabled', true);
        }            
    } 


    function removeNewSecondaryLanguage() {    
        var id = fields-1;  
        
        jQuery("#newSecondaryLanguage" + id).remove();

        if(fields == MAX_SECONDARY_LANGUAGES) {
            jQuery('#addNewSecondaryLanguageBtn').attr('disabled', false);
            jQuery('#alertinfo').css('display', 'none');
        }

        fields--;
        jQuery('#secondaryLanguagesArraySize').attr('value', parseInt(jQuery('#secondaryLanguagesArraySize').val()) - 1);

        if(fields == 0) {
            jQuery('#removeNewSecondaryLanguageBtn').attr('disabled', true);
            isRemoveButtonHidden = true;
        }         
    }  
</script>