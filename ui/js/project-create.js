<script language='javascript'>
    var fields = 0;
    var MAX_FIELDS = 10;
    var isRemoveButtonHidden = true;

    var isEnabledArray = new Array(false); 

    function addNewTarget() {

        if(isRemoveButtonHidden) {
            document.getElementById('removeBottomTargetBtn').style.visibility = 'visible';
            document.getElementById('removeBottomTargetBtn').disabled = false;
            isRemoveButtonHidden = false;
        }

        if(fields < MAX_FIELDS) {

            var table = document.getElementById('moreTargetLanguages');            
            var newRow = document.createElement('tr');                        

            newRow.setAttribute('id', 'newTargetLanguage' + fields);
            var newColumnLangCountry = document.createElement('td');
            newColumnLangCountry.setAttribute('width', "50%");

            var langs = document.getElementById('sourceLanguage').cloneNode(true);
            langs.setAttribute('id', 'targetLanguage_' + (fields + 1));
            langs.setAttribute('name', 'targetLanguage_' + (fields + 1));
            newColumnLangCountry.appendChild(langs);

            var countries = document.getElementById('sourceCountry').cloneNode(true);
            countries.setAttribute('id', 'targetCountry_' + (fields + 1));
            countries.setAttribute('name', 'targetCountry_' + (fields + 1));
            newColumnLangCountry.appendChild(countries);
            newRow.appendChild(newColumnLangCountry);

            var tableColumnChunking = document.createElement('td');  
            tableColumnChunking.setAttribute('align', 'middle');
            tableColumnChunking.setAttribute('valign', 'top');
            var inputChunking = document.createElement('input');
            inputChunking.setAttribute('type', 'checkbox');
            inputChunking.setAttribute('id', 'chunking_' + (fields + 1));
            inputChunking.setAttribute('name', 'chunking_' + (fields + 1));
            inputChunking.setAttribute('value', 'y');
            inputChunking.setAttribute('onchange', 'chunkingEnabled(' + (fields + 1) +')');     
            inputChunking.setAttribute('title', 'Create a chunking task for dividing large source files into managable chunks of 5,000 words or less.');
            tableColumnChunking.appendChild(inputChunking);


            var tableColumnTranslation = document.createElement('td');
            tableColumnTranslation.setAttribute('align', 'middle'); 
            tableColumnTranslation.setAttribute('valign', 'top');
            var inputTranslation = document.createElement('input');
            inputTranslation.setAttribute('type', 'checkbox');
            inputTranslation.setAttribute('id', 'translation_' + (fields + 1));
            inputTranslation.setAttribute('checked', 'true');
            inputTranslation.setAttribute('name', 'translation_' + (fields + 1))
            inputTranslation.setAttribute('value', 'y');
            inputTranslation.setAttribute('title', 'Create a translation task for volunteer translators to pick up.');
            tableColumnTranslation.appendChild(inputTranslation);


            var tableColumnReading = document.createElement('td');
            tableColumnReading.setAttribute('align', 'middle');
            tableColumnReading.setAttribute('valign', 'top');
            var inputProofReading = document.createElement('input');
            inputProofReading.setAttribute('type', 'checkbox');
            inputProofReading.setAttribute('id', 'proofreading_' + (fields + 1));
            inputProofReading.setAttribute('checked', 'true');
            inputProofReading.setAttribute('name', 'proofreading_' + (fields + 1));
            inputProofReading.setAttribute('value', 'y');
            inputProofReading.setAttribute('title', 'Create a proofreading task for evaluating the translation provided by a volunteer.');
            tableColumnReading.appendChild(inputProofReading);

            var tableColumnPostEditing = document.createElement('td');
            tableColumnPostEditing.setAttribute('align', 'middle');
            tableColumnPostEditing.setAttribute('valign', 'top');
            var inputPostEditing = document.createElement('input');
            inputPostEditing.setAttribute('type', 'checkbox');
            inputPostEditing.setAttribute('id', 'postediting_' + (fields + 1));
            inputPostEditing.setAttribute('name', 'postediting_' + (fields + 1));
            inputPostEditing.setAttribute('value', 'y'); 
            inputPostEditing.setAttribute('title', 'Create a postediting task for merging together task chunks created by a chunking task.');
            tableColumnPostEditing.appendChild(inputPostEditing);

            newRow.appendChild(tableColumnChunking);
            newRow.appendChild(tableColumnTranslation);
            newRow.appendChild(tableColumnReading);
            newRow.appendChild(tableColumnPostEditing);
            table.appendChild(newRow);
            isEnabledArray.push(false);

            var size = document.getElementById('targetLanguageArraySize');
            fields++;   
            size.setAttribute('value', parseInt(size.getAttribute('value'))+1);        
        }

        if(fields == MAX_FIELDS) {
            document.getElementById('alertinfo').style.display = 'block';
            document.getElementById('addMoreTargetsBtn').disabled = true;
        }            
    } 


    function removeNewTarget() {    
        var id = fields-1;  

        var table = document.getElementById('moreTargetLanguages');
        var tableRow = document.getElementById('newTargetLanguage' + id);
        table.removeChild(tableRow);  
        isEnabledArray.pop();

        if(fields == MAX_FIELDS) {
            document.getElementById('addMoreTargetsBtn').disabled = false;
            document.getElementById('alertinfo').style.display = 'none';
        }

        var size = document.getElementById('targetLanguageArraySize');
        fields--;
        size.setAttribute('value', parseInt(size.getAttribute('value'))-1);

        if(fields == 0) {
            document.getElementById('removeBottomTargetBtn').style.visibility = 'hidden';
            document.getElementById('removeBottomTargetBtn').disabled = true;
            isRemoveButtonHidden = true;
        }         
    }

    function chunkingEnabled(index)
    {
        if(!isEnabledArray[index]) {
            document.getElementById('translation_' + index).checked = false;
            document.getElementById('proofreading_' + index).checked = false;
            document.getElementById('postediting_' + index).checked = false;        
            document.getElementById('translation_' + index).disabled = true;
            document.getElementById('proofreading_' + index).disabled = true;
            document.getElementById('postediting_' + index).disabled = true;    
            isEnabledArray[index] = true;
        } else {
            document.getElementById('translation_' + index).disabled = false;
            document.getElementById('proofreading_' + index).disabled = false;
            document.getElementById('postediting_' + index).disabled = false;
            document.getElementById('translation_' + index).checked = true;
            document.getElementById('proofreading_' + index).checked = true;
            isEnabledArray[index] = false;
        }
    }    
</script>