{include file="header.tpl"}

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
                    {if (isset($title_err) || isset($deadline_err) || isset($targetLanguage_err) || isset($file_upload_err)) }
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
                                {if isset($file_upload_err)}
                                    <li><b>Source Text</b> - {$file_upload_err}</li>
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
                    <textarea wrap="soft" cols="1" rows="3" name="title" >{if isset($project)}{$project->getTitle()}{/if}</textarea>
                    <p style="margin-bottom:30px;"></p>

                    <label for="description"><h2>Description:</h2></label>
                    <p class="desc">A brief summary of the project.</p>                    
                    <textarea wrap="soft" cols="1" rows="6" name="description">{if isset($project)}{$project->getDescription()}{/if}</textarea>                    
                    <p style="margin-bottom:30px;"></p>

                    <label for="reference"><h2>Reference:</h2></label>
                    <p class="desc">Enter a URL that gives context to this project.</p>
                    <textarea wrap="soft" cols="1" rows="3" name="reference">{if isset($project)}{$project->getReference()}{/if}</textarea>    
                    <p style="margin-bottom:30px;"></p> 
                </td>
                <td width="493" align="center" valign="middle">    
                    <label for="{$field_name}"><h2>Source Text: <font color='red'>*</font></h2></label>
                    <p class="desc">Upload your source file for the project. Max file size is 8 MB.</p> {*$max_file_size_mb*}
                    <input type="hidden" name="MAX_FILE_SIZE" value="{$max_file_size_bytes}"/> {*$max_file_size_bytes*}
                    <input type="file" name="{$field_name}" id="{$field_name}"/>
                    <input type="hidden" name="organisation_id" value="1"/>
                    <p style="margin-bottom:30px;"></p>
                    
                    <label for="word_count"><h2>Word Count:</h2></label>
                    <p class="desc">Approximate or use a site such as 
                        <a href="http://wordcounttool.net/" target="_blank">Word Count Tool</a>.
                    </p>
                    <input type="text" name="word_count" id="word_count" maxlength="6" 
                            value="{if isset($project)}{$project->getWordCount()}{/if}"/>
                    <p style="margin-bottom:30px;"></p> 
                    
                    <label for="deadline"><h2>Deadline: <font color='red'>*</font></h2></label>
                    <p class="desc">When the project and its tasks should be completed by.</p>
                    <input type="text" id="deadline" name="deadline" value="{if isset($project)}{$project->getDeadline()}{/if}"/>
                    <p style="margin-bottom:30px;"></p>

                    <label for="tags"><h2>Tags:</h2></label>
                    <p class="desc">Separated by spaces. For multiword tags: join-with-hyphens.</p>
                    <textarea wrap="soft" cols="1" rows="3" name="tags">{if isset($tagList)}{$tagList}{/if}</textarea>
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
                        <input type="text" name="source" id="source"/>
                        <input type="text" name="sourceCountry" id="source"/>
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
                            <td width="25%"><b>Postediting</b></td>
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
                        <input type="text" name="source" id="source"/>
                        <input type="text" name="sourceCountry" id="source"/>
                    {/if}  
                </td>
                <td valign="top">
                    <table border="0" width="100%"> 
                        <tr align="center">
                            <td bgcolor=""><input type="checkbox" id="chunking_0" name="chunking_0" value="y" onchange="chunkingEnabled(0)"/></td>                            
                            <td><input type="checkbox" id="translation_0" checked="true" name="translation_0" value="y"/></td>
                            <td><input type="checkbox" id="proofreading_0" name="proofreading_0" value="y"/></td>
                            <td><input type="checkbox" id="postediting_0" name="postediting_0" value="y"/></td>
                        </tr>                        
                    </table>                    
                </td>
                    
            </tr>
            <tr>
                <td colspan="2">     
                    <table id="moreTargetLanguages" border="0" width="100%"></table>
                </td>
            </tr> 
            <tr>
                <td colspan="2">
                    <div id="alertinfo" class="alert alert-info" style="display: none;"><center>You have reached the maximum number of target translation fields allowed.</center></div>  
                    <input id="addMoreTargetsBtn" type="button" onclick="addNewTarget()" value="Add More Target Languages"/>
                    <input id="removeBottomTargetBtn" type="button" onclick="removeNewTarget()" value="Remove" disabled="true" style="visibility: hidden"/>  
                    <input type="hidden" id="targetLanguageArraySize" name="targetLanguageArraySize" value="1"/>
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
