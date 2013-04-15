<script type="text/javascript">
    var fields = 0;
    var MAX_SECONDARY_LANGUAGES = 4;
    var isRemoveButtonHidden = true;

    function addNewSecondaryLanguage() {

        if(isRemoveButtonHidden) {
            document.getElementById('removeNewSecondaryLanguageBtn').style.visibility = 'visible';
            document.getElementById('removeNewSecondaryLanguageBtn').disabled = false;
            isRemoveButtonHidden = false;
        }

        if(fields < MAX_SECONDARY_LANGUAGES) {

            var table = document.getElementById('extraSecondaryLanguages');            
            var paragraph = document.createElement('p');
            paragraph.setAttribute('id', 'newSecondaryLanguage' + fields);
//
//            newRow.setAttribute('id', 'newSecondaryLanguage' + fields);
//            var newColumnLangCountry = document.createElement('td');
//            newColumnLangCountry.setAttribute('width', "50%");

            var langs = document.getElementById('nativeLanguage').cloneNode(true);
            langs.setAttribute('id', 'secondaryLanguage_' + (fields + 1));
            langs.setAttribute('name', 'secondaryLanguage_' + (fields + 1));
            paragraph.appendChild(langs);

            var countries = document.getElementById('nativeCountry').cloneNode(true);
            countries.setAttribute('id', 'secondaryCountry_' + (fields + 1));
            countries.setAttribute('name', 'secondaryCountry_' + (fields + 1));
            paragraph.appendChild(countries);
            table.appendChild(paragraph);
//
//            table.appendChild(newRow);

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