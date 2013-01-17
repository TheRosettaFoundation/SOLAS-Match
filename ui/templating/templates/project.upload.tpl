{include file="header.tpl"}


<script language='javascript'>

    var fields = 0;
    var MAX_FIELDS = 10; 
    var isRemoveButtonHidden = true;

    function addNewTarget() {

        if(isRemoveButtonHidden) {
            document.getElementById('removeBottomTargetBtn').style.visibility = 'visible';
            isRemoveButtonHidden = false;
        }

        if(fields < MAX_FIELDS) {
            var outer = document.getElementById('moreTargetLanguages');
            var innerDiv = document.createElement('div');
            innerDiv.setAttribute('id', fields);
            
            var langs = document.getElementById('sourceLanguage').cloneNode(true);
            langs.setAttribute('id', "targetLanguage_" + (fields + 1));
            langs.setAttribute('name', "targetLanguage_" + (fields + 1));
            innerDiv.appendChild(langs);
            outer.appendChild(innerDiv);
            
            var countries = document.getElementById('sourceCountry').cloneNode(true);
            countries.setAttribute('id', "targetCountry_" + (fields + 1));
            countries.setAttribute('name', "targetCountry_" + (fields + 1));
            innerDiv.appendChild(countries);
            outer.appendChild(innerDiv);
            
            
            var taskTypes = document.getElementById('moreTaskTypeTables');
            
            var table = document.createElement('table');            
            table.setAttribute('width', "100%");
            taskTypes.appendChild(table);
            
            var tableRow = document.createElement('tr');
            tableRow.setAttribute('align', "center");
            table.appendChild(tableRow);
            
            var tableColumnChunking = document.createElement('td');            
            var inputChunking = document.createElement('input');
            inputChunking.setAttribute('type', "checkbox");
            inputChunking.setAttribute('id', "chunking_" + (fields + 1));
            inputChunking.setAttribute('name', "chunking");
            inputChunking.setAttribute('value', "y");
            inputChunking.setAttribute('onchange', "chunkingEnabled()");       

            
            var tableColumnTranslation = document.createElement('td');
            var inputTranslation = document.createElement('input');
            inputTranslation.setAttribute('type', "checkbox");
            inputTranslation.setAttribute('id', "translation_" + (fields + 1));
            inputTranslation.setAttribute('checked', "true");
            inputTranslation.setAttribute('name', "translation");
            inputTranslation.setAttribute('value', "y");
            
            var tableColumnReading = document.createElement('td');
            var inputProofReading = document.createElement('input');
            inputProofReading.setAttribute('type', "checkbox");
            inputProofReading.setAttribute('id', "proofreading_" + (fields + 1));
            inputProofReading.setAttribute('name', "proofreading");
            inputProofReading.setAttribute('value', "y");
            
            var tableColumnPostEditing = document.createElement('td');
            var inputPostEditing = document.createElement('input');
            inputPostEditing.setAttribute('type', "checkbox");
            inputPostEditing.setAttribute('id', "postediting_" + (fields + 1));
            inputPostEditing.setAttribute('name', "postediting");
            inputPostEditing.setAttribute('value', "y");            
            
            
            
            /*
                     <table border="1" width="100%"> 
                        <tr align="center">
                            <td><input type="checkbox" id="chunking_0" type="checkbox" name="chunking" value="y" onchange="chunkingEnabled()"></td>                            
                            <td><input type="checkbox" id="translation_0" type="checkbox" checked="true" name="translation" value="y"></td>
                            <td><input type="checkbox" id="proofreading_0" type="checkbox" name="proofreading" value="y"></td>
                            <td><input type="checkbox" id="postediting_0" type="checkbox" onchange="" name="postediting" value="y"></td>
                        </tr>                        
                    </table>             
            */

   
            fields++;                
        }

        if(fields == MAX_FIELDS) {
            document.getElementById('alertinfo').style.display = 'block';
            document.getElementById('addMoreTargetsBtn').style.visibility = 'hidden';
        }            
    } 


    function removeNewTarget() {    
        var id = fields-1;  
        
        var div = document.getElementById('moreTargetLanguages');
        var innerDiv = document.getElementById(id);
        div.removeChild(innerDiv);
        
        if(fields == MAX_FIELDS) {
            document.getElementById('addMoreTargetsBtn').style.visibility = 'visible';
            document.getElementById('alertinfo').style.display = 'none';
        }

        fields--;

        if(fields == 0) {
            document.getElementById('removeBottomTargetBtn').style.visibility = 'hidden';
            isRemoveButtonHidden = true;
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
    <table border="1">
        <form method="post" enctype="multipart/form-data" action="{$url_project_upload}"> {*$project_id*}

            <tr>
                <td width="493" align="center" valign="middle">
                    <label for="title"><h2>Title: <font color='red'>*</font></h2></label>
                    <p class="desc">Provide a meaningful title for your project.</p>
                    <textarea wrap="soft" cols="1" rows="3" name="title">asd</textarea> {*$project->getTitle()*}
                    <p style="margin-bottom:30px;"></p>

                    <label for="description"><h2>Description:</h2></label>
                    <p class="desc">A brief summary of the project.</p>
                    <textarea wrap="soft" cols="1" rows="6" name="description">asf</textarea> {*$project->getDescription()*}
                    <p style="margin-bottom:30px;"></p>

                    <label for="reference"><h2>Reference:</h2></label>
                    <p class="desc">Enter a URL that gives context to this project.</p>
                    <textarea wrap="soft" cols="1" rows="3" name="reference">asf</textarea> {*$project->getReference()*}    
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
                    <input type="text" name="word_count" id="word_count" maxlength="6" value="999"> {*$project->getWordCount()*}
                    <p style="margin-bottom:30px;"></p> 
                    
                    <label for="deadline"><h2>Deadline: <font color='red'>*</font></h2></label>
                    <p class="desc">When the project and its tasks should be completed by.</p>
                    <textarea wrap="soft" cols="1" rows="2" name="deadline">asf</textarea> {*$project->getDeadline()*}   
                    <p style="margin-bottom:30px;"></p>

                    <label for="tags"><h2>Tags:</h2></label>
                    <p class="desc">Separated by spaces. For multiword tags: join-with-hyphens.</p>
                    <textarea wrap="soft" cols="1" rows="3" name="tags">asd</textarea> {*$project->getTitle()*}
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
                <td align="center">
                    <h2>Task Type: <font color='red'>*</font></h2>
                    <p class="desc">Specify which task types you require for your workflow.</p>                 
                </td>
            <tr>
                <td>                    
                    <h2>Target Language(s): <font color='red'>*</font></h2><br>
                </td>
                <td valign="bottom">
                    <table border="1" width="100%"> 
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
                        <select name="targetLanguage_0" id="targetLanguage_0">
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
                <td valign="baseline">
                    <table border="1" width="100%"> 
                        <tr align="center">
                            <td><input type="checkbox" id="chunking_0" name="chunking" value="y" onchange="chunkingEnabled()"></td>                            
                            <td><input type="checkbox" id="translation_0" checked="true" name="translation" value="y"></td>
                            <td><input type="checkbox" id="proofreading_0" name="proofreading" value="y"></td>
                            <td><input type="checkbox" id="postediting_0" onchange="" name="postediting" value="y"></td>
                        </tr>                        
                    </table>                    
                </td>
                    
            </tr>
            <tr>
                <td>
            

                        <div id="moreTargetLanguages"></div>
                        <!--
                        <div id="text0"></div>
                        <div id="text1"></div>
                        <div id="text2"></div>
                        <div id="text3"></div>
                        <div id="text4"></div>
                        <div id="text5"></div>
                        <div id="text6"></div>
                        <div id="text7"></div>
                        <div id="text8"></div>
                        <div id="text9"></div>
                        -->
                        <div id="alertinfo" class="alert alert-info" style="display: none;">You have reached the maximum number of target translation fields allowed.</div>  
                        <input id="addMoreTargetsBtn" type="button" onclick="addNewTarget()" value="Add More Target Languages"/>
                        <input id="removeBottomTargetBtn" type="button" onclick="removeNewTarget()" value="Remove" style="visibility: hidden;"/>

 
 
                </td>
                <td>
                    <div id="moreTaskTypeTables"</div>
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
                        <a href='{urlFor name="home"}' class='btn btn-danger'>
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
        </form>            
    </table>                        
</div>
    
{include file="footer.tpl"}
