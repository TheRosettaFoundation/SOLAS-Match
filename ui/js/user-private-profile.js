<script type="text/javascript">
    var fields = 0;
    var MAX_SECONDARY_LANGUAGES = 5;
    var isRemoveButtonHidden = true;
    
    window.onload = init;

    function init()
    {
        fields = jQuery("#extraSecondaryLanguages").children("span").length;
        
        if(fields == MAX_SECONDARY_LANGUAGES) {
            document.getElementById('alertinfo').style.display = 'block';
            document.getElementById('addNewSecondaryLanguageBtn').disabled = true;
        }    
        
        if(isRemoveButtonHidden) {
            document.getElementById('removeNewSecondaryLanguageBtn').style.visibility = 'visible';
            document.getElementById('removeNewSecondaryLanguageBtn').disabled = false;
            isRemoveButtonHidden = false;
        }
    }

    function addNewSecondaryLanguage() {

        if(isRemoveButtonHidden) {
            document.getElementById('removeNewSecondaryLanguageBtn').style.visibility = 'visible';
            document.getElementById('removeNewSecondaryLanguageBtn').disabled = false;
            isRemoveButtonHidden = false;
        }

        if(fields < MAX_SECONDARY_LANGUAGES) {

            var table = document.getElementById('extraSecondaryLanguages');            
            var paragraph = document.createElement('span');
            paragraph.setAttribute('id', 'newSecondaryLanguage' + fields);

            var langs = document.getElementById('nativeLanguage').cloneNode(true);
            langs.setAttribute('id', 'secondaryLanguage_' + fields);
            langs.setAttribute('name', 'secondaryLanguage_' + fields);
            paragraph.appendChild(langs);

            var countries = document.getElementById('nativeCountry').cloneNode(true);
            countries.setAttribute('id', 'secondaryCountry_' + fields);
            countries.setAttribute('name', 'secondaryCountry_' + fields);
            paragraph.appendChild(countries);
            table.appendChild(paragraph);

            var size = document.getElementById('secondaryLanguagesArraySize');
            fields++;   
            size.setAttribute('value', parseInt(size.getAttribute('value'))+1);        
        }

        if(fields == MAX_SECONDARY_LANGUAGES) {
            document.getElementById('alertinfo').style.display = 'block';
            document.getElementById('addNewSecondaryLanguageBtn').disabled = true;
        }            
    } 


    function removeNewSecondaryLanguage() {    
        var id = fields-1;  

        var table = document.getElementById('extraSecondaryLanguages');
        var tableRow = document.getElementById('newSecondaryLanguage' + id);
        table.removeChild(tableRow);  

        if(fields == MAX_SECONDARY_LANGUAGES) {
            document.getElementById('addNewSecondaryLanguageBtn').disabled = false;
            document.getElementById('alertinfo').style.display = 'none';
        }

        var size = document.getElementById('secondaryLanguagesArraySize');
        fields--;
        size.setAttribute('value', parseInt(size.getAttribute('value'))-1);

        if(fields == 0) {
            document.getElementById('removeNewSecondaryLanguageBtn').disabled = true;
            isRemoveButtonHidden = true;
        }         
    }  
</script>