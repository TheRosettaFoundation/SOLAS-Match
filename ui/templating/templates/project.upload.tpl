{include file="header.tpl"}


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

            //var projectForm = document.getElementById('createProjectForm');
            var table = document.getElementById('moreTargetLanguages');            
            var newRow = document.createElement('tr');                        
    
            newRow.setAttribute('id', "newTargetLanguage" + fields);
            var newColumnLangCountry = document.createElement('td');
            newColumnLangCountry.setAttribute('width', "50%");
            
            var langs = document.getElementById('sourceLanguage').cloneNode(true);
            langs.setAttribute('id', "targetLanguage_" + (fields + 1));
            langs.setAttribute('name', "targetLanguage_" + (fields + 1));
            newColumnLangCountry.appendChild(langs);
            
            var countries = document.getElementById('sourceCountry').cloneNode(true);
            countries.setAttribute('id', "targetCountry_" + (fields + 1));
            countries.setAttribute('name', "targetCountry_" + (fields + 1));
            newColumnLangCountry.appendChild(countries);
            newRow.appendChild(newColumnLangCountry);
            
            var tableColumnChunking = document.createElement('td');  
            tableColumnChunking.setAttribute('align', 'middle');
            tableColumnChunking.setAttribute('valign', "top");
            var inputChunking = document.createElement('input');
            inputChunking.setAttribute('type', "checkbox");
            inputChunking.setAttribute('id', "chunking_" + (fields + 1));
            inputChunking.setAttribute('name', "chunking_" + (fields + 1));
            inputChunking.setAttribute('value', "y");
            inputChunking.setAttribute('onchange', "chunkingEnabled(" + (fields + 1) +")");       
            tableColumnChunking.appendChild(inputChunking);
            
            
            var tableColumnTranslation = document.createElement('td');
            tableColumnTranslation.setAttribute('align', 'middle'); 
            tableColumnTranslation.setAttribute('valign', "top");
            var inputTranslation = document.createElement('input');
            inputTranslation.setAttribute('type', "checkbox");
            inputTranslation.setAttribute('id', "translation_" + (fields + 1));
            inputTranslation.setAttribute('checked', "true");
            inputTranslation.setAttribute('name', "translation_" + (fields + 1))
            inputTranslation.setAttribute('value', "y");
            tableColumnTranslation.appendChild(inputTranslation);
           
            
            var tableColumnReading = document.createElement('td');
            tableColumnReading.setAttribute('align', 'middle');
            tableColumnReading.setAttribute('valign', "top");
            var inputProofReading = document.createElement('input');
            inputProofReading.setAttribute('type', "checkbox");
            inputProofReading.setAttribute('id', "proofreading_" + (fields + 1));
            inputProofReading.setAttribute('name', "proofreading_" + (fields + 1));
            inputProofReading.setAttribute('value', "y");
            tableColumnReading.appendChild(inputProofReading);
            
            var tableColumnPostEditing = document.createElement('td');
            tableColumnPostEditing.setAttribute('align', 'middle');
            tableColumnPostEditing.setAttribute('valign', "top");
            var inputPostEditing = document.createElement('input');
            inputPostEditing.setAttribute('type', "checkbox");
            inputPostEditing.setAttribute('id', "postediting_" + (fields + 1));
            inputPostEditing.setAttribute('name', "postediting_" + (fields + 1));
            inputPostEditing.setAttribute('value', "y"); 
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
            //document.getElementById('addMoreTargetsBtn').style.visibility = 'hidden';
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
            //document.getElementById('addMoreTargetsBtn').style.visibility = 'visible';
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
            document.getElementById("translation_" + index).checked = false;
            document.getElementById("proofreading_" + index).checked = false;
            document.getElementById("postediting_" + index).checked = false;        
            document.getElementById("translation_" + index).disabled = true;
            document.getElementById("proofreading_" + index).disabled = true;
            document.getElementById("postediting_" + index).disabled = true;    
            isEnabledArray[index] = true;
        } else {
            document.getElementById("translation_" + index).disabled = false;
            document.getElementById("proofreading_" + index).disabled = false;
            document.getElementById("postediting_" + index).disabled = false;
            document.getElementById("translation_" + index).checked = true;
            isEnabledArray[index] = false;
        }
    }    
</script>


<div class="grid_8">
    <div class="page-header">
        <h1>
            Create A Project <small>Provide as much information as possible.</small><br>   
            <small>
                Note:
                <font color='red'>*</font>
                denotes a required field.
            </small>
        </h1>
    </div>           

    {if isset($error)}
        <div class="alert alert-error">
                {$error}
        </div>
    {/if}
</div>  
<p style="margin-bottom:20px;"></p>
<div class="well">
    <form id="createProjectForm" method="post" enctype="multipart/form-data" action="{$url_project_upload}"> {*$project_id*}
        <table border="0">
            <tr>
                <td colspan="2">
                    {if (isset($title_err) || isset($deadline_err) || isset($targetLanguage_err))}
                        <div class="alert alert-error">
                            <h3>Please fill in all required information:</h3>
                            <ol>
                                {if isset($title_err)}
                                    <li>{$title_err}</li>
                                {/if}
                                {if isset($deadline_err)}
                                    <li>{$deadline_err}</li>
                                {/if}
                                {if isset($targetLanguage_err)}
                                    <li>{$targetLanguage_err}</li>
                                {/if}
                            </ol>
                        </div>                        
                    {/if}
                </td>
            </tr>
            <tr>
                <td width="493" align="center" valign="middle">
                    <label for="title"><h2>Title: <font color='red'>*</font></h2></label>
                    <p class="desc">Provide a meaningful title for your project.</p>
                    <textarea wrap="soft" cols="1" rows="3" name="title"></textarea>
                    <p style="margin-bottom:30px;"></p>

                    <label for="description"><h2>Description:</h2></label>
                    <p class="desc">A brief summary of the project.</p>                    
                    <textarea wrap="soft" cols="1" rows="6" name="description">{if isset($projectModel)}{$projectModel->getDescription()}{/if}</textarea>                    
                    <p style="margin-bottom:30px;"></p>

                    <label for="reference"><h2>Reference:</h2></label>
                    <p class="desc">Enter a URL that gives context to this project.</p>
                    <textarea wrap="soft" cols="1" rows="3" name="reference">{if isset($projectModel)}{$projectModel->getReference()}{/if}</textarea>    
                    <p style="margin-bottom:30px;"></p> 
                </td>
                <td width="493" align="center" valign="middle">    
                    <label for="tasktrans"><h2>Source Text: <font color='red'>*</font></h2></label>  {*$field_name*}
                    <p class="desc">Upload your source file for the project. Max file size is 8 MB.</p> {*$max_file_size_mb*}
                    <input type="hidden" name="MAX_FILE_SIZE" value="8096"> {*$max_file_size_bytes*}
                    <input type="file" name="tasktrans" id="tasktrans">{*$field_name*}
                    <input type="hidden" name="organisation_id" value="1">
                    <p style="margin-bottom:30px;"></p>
                    
                    <label for="word_count"><h2>Word Count:</h2></label>
                    <p class="desc">Approximate or use a site such as <a href="http://wordcounttool.net/" target="_blank">Word Count Tool</a>.</p>
                    <input type="text" name="word_count" id="word_count" maxlength="6" value="{if isset($projectModel)}{$projectModel->getWordCount()}{/if}">
                    <p style="margin-bottom:30px;"></p> 
                    
                    <label for="deadline"><h2>Deadline: <font color='red'>*</font></h2></label>
                    <p class="desc">When the project and its tasks should be completed by.</p>
                    <textarea wrap="soft" cols="1" rows="2" name="deadline">{if isset($projectModel)}{$projectModel->getDeadline()}{/if}</textarea> 
                    <p style="margin-bottom:30px;"></p>

                    <label for="tags"><h2>Tags:</h2></label>
                    <p class="desc">Separated by spaces. For multiword tags: join-with-hyphens.</p>
                    <textarea wrap="soft" cols="1" rows="3" name="tags"></textarea> {*{if isset($projectModel)}{$projectModel->getTags()}{/if}*}
                </td>                    
            </tr>
            <tr>
                <td colspan="2">
                    <hr/>
                </td>
            </tr>
            <tr>
                <td align="left" valign="top">
                    {if isset($languages)}
                            <h2>Source Language: <font color='red'>*</font></h2><br>
                            <select name="sourceLanguage" id="sourceLanguage">
                                {foreach $languages as $language}
                                        <option value="{$language->getCode()}">{$language->getName()}</option>
                                {/foreach}
                            </select>
                            {if isset($countries)}
                                <select name="sourceCountry" id="sourceCountry">
                                    {foreach $countries as $country}
                                         <option value="{$country->getCode()}">{$country->getName()}</option>
                                    {/foreach}                                
                                </select>
                            {/if}
                    {else}
                        <label for="source"><h2>Source Language: <font color='red'>*</font></h2></label>
                        <input type="text" name="source" id="source">
                        <input type="text" name="sourceCountry" id="source">
                    {/if}                     
                </td>                
                <td align="center" valign="bottom">
                    <h2>Task Type: <font color='red'>*</font></h2>
                    <p class="desc">Specify which task types you require for your workflow.</p>                 
                </td>
            <tr>
                <td>                    
                    <h2>Target Language(s): <font color='red'>*</font></h2><br>
                </td>
                <td valign="center">
                    <table border="0" width="100%"> 
                        <tr align="center">
                            <td width="25%"><b>Chunking</b></td>
                            <td width="25%"><b>Translation</b></td>
                            <td width="25%"><b>Proofreading</b></td>
                            <td width="25%"><b>Post-Editing</b></td>
                        </tr> 
                    </table>
                </td>
            </tr>
            <tr>
                <td> 
                    {if isset($languages)}
                        <select name="targetLanguage_0" id="targetLanguage_0" >
                            {foreach $languages as $language}
                                <option value="{$language->getCode()}">{$language->getName()}</option>
                            {/foreach}
                        </select>
                        {if isset($countries)}
                            <select name="targetCountry_0" id="targetCountry_0">
                                {foreach $countries as $country}
                                    <option value="{$country->getCode()}">{$country->getName()}</option>
                                {/foreach}
                            </select> 
                        {/if}
                    {else}
                        <label for="source"><h2>Source Language: <font color='red'>*</font></h2></label>
                        <input type="text" name="source" id="source">
                        <input type="text" name="sourceCountry" id="source">
                    {/if}  
                </td>
                <td valign="top">
                    <table border="0" width="100%"> 
                        <tr align="center">
                            <td bgcolor=""><input type="checkbox" id="chunking_0" name="chunking_0" value="y" onchange="chunkingEnabled(0)"></td>                            
                            <td><input type="checkbox" id="translation_0" checked="true" name="translation_0" value="y"></td>
                            <td><input type="checkbox" id="proofreading_0" name="proofreading_0" value="y"></td>
                            <td><input type="checkbox" id="postediting_0" name="postediting_0" value="y"></td>
                        </tr>                        
                    </table>                    
                </td>
                    
            </tr>
            <tr>
                <!-- <div id="moreTaskTypeTables"</div> -->
                <!-- <td id="moreTargetLanguages" width="50%"> -->
                <td colspan="2">     
                    <table id="moreTargetLanguages" border="0" width="100%"></table>
                </td>
            </tr> 
            <tr>
                <td colspan="2">
                    <div id="alertinfo" class="alert alert-info" style="display: none;"><center>You have reached the maximum number of target translation fields allowed.</center></div>  
                    <input id="addMoreTargetsBtn" type="button" onclick="addNewTarget()" value="Add More Target Languages"/>
                    <input id="removeBottomTargetBtn" type="button" onclick="removeNewTarget()" value="Remove" disabled="true" style="visibility: hidden"/>  
                    <input type="hidden" id="targetLanguageArraySize" name="targetLanguageArraySize" value="1">
                </td>
            </tr>                
            <tr>
                <td colspan="2">
                    <hr/>
                </td> 
            </tr>
            <tr align="center">
                <td>
                    <p style="margin-bottom:20px;"></p> 
                        <a href='{urlFor name="org-dashboard"}' class='btn btn-danger'>
                            <i class="icon-ban-circle icon-white"></i> Cancel
                        </a>
                    <p style="margin-bottom:20px;"></p> 
                </td>
                <td>
                    
                    <p style="margin-bottom:20px;"></p> 
                        <button type="submit" name="submit" value="createproject" class="btn btn-success">
                            <i class="icon-upload icon-white"></i> Submit Project
                        </button>                            
                    <p style="margin-bottom:20px;"></p>                     
                </td>
            </tr>          
        </table>   
    </form>  
</div>
    
{include file="footer.tpl"}
