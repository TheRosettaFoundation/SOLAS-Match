{include file="header.tpl"}

<!-- Pass via ProjectRouteHandler -->
<script>
    var isEnabled = false;
    function chunkingEnabled()
    {        
        if(!isEnabled) {
           document.getElementById("translation").checked = false;
           document.getElementById("proofreading").checked = false;
           document.getElementById("postediting").checked = false;        
           document.getElementById("translation").disabled = true;
           document.getElementById("proofreading").disabled = true;
           document.getElementById("postediting").disabled = true;
           isEnabled = true;
        } else {
           document.getElementById("translation").disabled = false;
           document.getElementById("proofreading").disabled = false;
           document.getElementById("postediting").disabled = false;       
           isEnabled = false;
        }        
    }
</script>

<div class="grid_8">
    <div class="page-header">
        <h1>
            Create your project <small>Provide as much information as possible.</small><br>   
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
    <table border="0">
        <form method="post" action="{$url_project_describe}"> {*$project_id*}

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

                    <label for="title"><h2>Tags:</h2></label>
                    <p class="desc">Separated by spaces. For multiword tags: join-with-hyphens.</p>
                    <textarea wrap="soft" cols="1" rows="3" name="title">asd</textarea> {*$project->getTitle()*}
                </td>                    
            </tr>
            <tr>
                <td align="left" valign="top" colspan="2">
                    <hr />
                </td>

            </tr>
            <tr>
                <td align="left" valign="top">
                {if isset($languages)}
                        <h2>Source Language: <font color='red'>*</font></h2><br>
                        <select name="source" id="source">
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
                        <h2>Target Language(s): <font color='red'>*</font></h2><br>
                        <select name="target_0" id="target_0">
                            {foreach $languages as $language}
                                <option value="{$language->getCode()}">{$language->getName()}</option>
                            {/foreach}
                        </select>
                        {if isset($countries)}
                            <select name="targetCountry_0" id="targetCountry">
                                {foreach $countries as $country}
                                    <option value="{$country->getCode()}">{$country->getName()}</option>
                                {/foreach}
                            </select> 
                        {/if}
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
                        <div id="alertinfo" class="alert alert-info" style="display: none;">You have reached the maximum number of target translation fields allowed.</div>  
                        <input id="addMoreTargetsBtn" type="button" onclick="addInput()" value="Add More Target Languages"/>
                        <input id="removeBottomTargetBtn" type="button" onclick="removeInput()" value="Remove" style="visibility: hidden;"/>
   
                    <p style="margin-bottom:30px;"></p>   
                {else}

                        <label for="source"><h2>Source Language: <font color='red'>*</font></h2></label>
                        <input type="text" name="source" id="source">
                        <input type="text" name="sourceCountry" id="source">
                {/if}  
                </td>

                <td rowspan="2">
                    <table border="0" width="100%">
                        <tr align="center">
                            <td colspan="4"><h2>Task Types: <font color='red'>*</font></h2>
                            <p class="desc">Specify which task types you require for your workflow.</p>
                            </td>
                        </tr>
                        <tr align="center">
                            <div id="testing"></div>
                            <td>Chunking</td>
                            <td>Translation</td>
                            <td>Proofreading</td>
                            <td>Post-Editing</td>
                        </tr>    
                        <tr align="center">
                            <td><input id="chunking" type="checkbox" name="chunking" value="y" onchange="chunkingEnabled()"></td>                            
                            <td><input id="translation" type="checkbox" checked="true" name="translation" value="y"></td>
                            <td><input id="proofreading" type="checkbox" name="proofreading" value="y"></td>
                            <td><input id="postediting" type="checkbox" onchange="" name="postediting" value="y"></td>
                        </tr>                        
                    </table>                    
                </td>
            </tr>            
            <tr>
                <td>
                    <label for="target"><h2>Target Language(s): <font color='red'>*</font></h2></label>
                    <input type="text" name="target" id="target">
                    <input type="text" name="targetCountry" id="source">

                    <div id="alertinfo" class="alert alert-info" style="display: none;">You have reached the maximum number of target translation fields allowed.</div>  
                    <input id="addMoreTargetsBtn" type="button" onclick="addInput()" value="Add More Target Languages"/>
                    <input id="removeBottomTargetBtn" type="button" onclick="removeInput()" value="Remove" style="visibility: hidden;"/>
                </td>

            </tr>
            
            <tr align="center">
                <td colspan="2">
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
