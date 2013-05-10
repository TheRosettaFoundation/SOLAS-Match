{include file="header.tpl"}

    <div class="grid_8">
        <div class="page-header">
            <h1>
                Create A Project <small>Provide as much information as possible.</small><br>   
                <small>
                    Note:
                    <span style="color: red">*</span>
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
    <p style="margin-bottom:20px;"/>
    
    <div class="well">
        <form id="createProjectForm" method="post" enctype="multipart/form-data" action="{$url_project_upload}"> {*$project_id*}
            <table id="createProjectTable">
                <tr>
                    <td colspan="2">
                        {if (isset($title_err) || isset($description_err) || isset($wordcount_err) || isset($deadline_err)
                            || isset($impact_err) || isset($targetLanguage_err) || isset($uniqueLanguageCountry_err)
                            || isset($file_upload_err))}
                            <div class="alert alert-error">
                                <h3>Please fill in all required fields:</h3>
                                <ol>
                                    {if isset($title_err)}
                                        <li>{$title_err}</li>
                                    {/if}
                                    {if isset($description_err)}
                                        <li>{$description_err}</li>
                                    {/if}
                                    {if isset($wordcount_err)}
                                        <li>{$wordcount_err}</li>
                                    {/if}
                                    {if isset($deadline_err)}
                                        <li>{$deadline_err}</li>
                                    {/if}
                                    {if isset($impact_err)}
                                        <li>{$impact_err}</li>
                                    {/if}
                                    {if isset($targetLanguage_err)}
                                        <li>{$targetLanguage_err}</li>
                                    {/if}
                                    {if isset($uniqueLanguageCountry_err)}
                                        <li>{$uniqueLanguageCountry_err}</li>
                                    {/if}
                                    {if isset($file_upload_err)}
                                        <li><strong>Source Text</strong> - {$file_upload_err}</li>
                                    {/if}
                                </ol>
                            </div>                        
                        {/if}
                    </td>
                </tr>
                <tr valign="middle">
                    <td width="493" align="center" valign="middle">
                        <label for="title"><h2>Title: <span style="color: red">*</span></h2></label>
                        <p class="desc">Provide a meaningful title for the project.</p>
                        <textarea wrap="soft" cols="1" rows="3"name="title" style="width: 400px" >{if isset($project)}{$project->getTitle()}{/if}</textarea>
                        <p style="margin-bottom:20px;"></p>

                        <label for="description"><h2>Description: <span style="color: red">*</span></h2></label>
                        <p class="desc">A brief summary of the project.</p>                    
                        <textarea wrap="soft" cols="1" rows="8" name="description" style="width: 400px">{if isset($project)}{$project->getDescription()}{/if}</textarea>                    
                        <p style="margin-bottom:20px;"></p>

                        <label for="impact"><h2>Impact: <span style="color: red">*</span></h2></label>
                        <p class="desc">Who or what will benefit from contributions to this project.<br/> Will be read by volunteers considering assigning themselves to your project.</p>
                        <textarea wrap="soft" cols="1" rows="3" name="impact" style="width: 400px">{if isset($project)}{$project->getImpact()}{/if}</textarea>    
                        <p style="margin-bottom:20px;"></p>

                        <label for="reference"><h2>Reference:</h2></label>
                        <p class="desc">Enter a URL that gives context to this project.</p>
                        <input type="text" name="reference" {if isset($project)}value="{$project->getReference()}"{/if} style="width: 400px" />    
                    </td>
                    <td width="493" align="center" valign="middle">    
                        <div style="margin-bottom:25px;">
                            <label for="{$field_name}"><h2>Source Text: <span style="color: red">*</span></h2></label>
                            <p class="desc">Upload your source file for the project. Max file size is 8 MB.</p> {*$max_file_size_mb*}
                            <input type="hidden" name="MAX_FILE_SIZE" value="{$max_file_size_bytes}"/> {*$max_file_size_bytes*}
                            <input type="file" name="{$field_name}" id="{$field_name}"/>
                            <input type="hidden" name="organisation_id" value="1"/>
                        </div>
                        <div style="margin-bottom: 25px;">
                        <label for="word_count"><h2>Word Count: <span style="color: red">*</span></h2></label>
                            <p class="desc">Approximate or use a site such as 
                                <a href="http://wordcounttool.net/" target="_blank">Word Count Tool</a>.
                            </p>
                            <input type="text" name="word_count" id="word_count" maxlength="6" 
                                    value="{if isset($project)}{$project->getWordCount()}{/if}" style="width: 400px"/>
                        </div>                    
                        <div style="margin-bottom:25px;">                    
                            <label><h2>Deadline: <span style="color: red">*</span></h2></label>
                            <p class="desc">When the project and its tasks should be completed by.</p>
                            <input class="hasDatePicker" type="text" id="deadline" name="deadline" value="{if isset($project)}{$project->getDeadline()}{/if}" style="width: 400px"/>                    
                        </div>
                        <div style="margin-bottom:25px;">
                            <label for="tags"><h2>Tags:</h2></label>
                            <p class="desc">The tags you provide will be used to match volunteers to your project. <br/>Separated by spaces. For multiword tags: join-with-hyphens.</p>
                            <input id="tags" name="tags" value="{if isset($tagList)}{$tagList}{/if}" style="width: 400px" />
                        </div>
                        <div style="margin-bottom:25px;">
                            <label for="publishtasks"><h2>Publish Tasks:</h2></label>
                            <p class="desc">If checked, tasks will appear in the task stream.</p>
                            <input type="checkbox" name="publishTasks" value="1" checked="true"/>
                        </div>
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
                            <h2>Source Language: <span style="color: red">*</span></h2><br>
                                <select name="sourceLanguage" id="sourceLanguage" style="width: 400px">
                                    {foreach $languages as $language}
                                            <option value="{$language->getCode()}">{$language->getName()}</option>
                                    {/foreach}
                                </select>
                                {if isset($countries)}
                                    <select name="sourceCountry" id="sourceCountry" style="width: 400px">
                                        {foreach $countries as $country}
                                             <option value="{$country->getCode()}">{$country->getName()}</option>
                                        {/foreach}                                
                                    </select>
                                {/if}
                        {else}
                            <label for="source"><h2>Source Language: <span style="color: red">*</span></h2></label>
                            <input type="text" name="source" id="source"/>
                            <input type="text" name="sourceCountry" id="source"/>
                        {/if}                     
                    </td>                
                    <td align="center" valign="middle">
                        <h2>Task Type: <span style="color: red">*</span></h2>
                        <p class="desc">Specify which task types you require for your workflow.</p>                 
                    </td>
                <tr>
                    <td>                    
                        <h2>Target Language(s): <span style="color: red">*</span></h2><br>
                    </td>
                    <td valign="center">
                        <table border="0" width="100%"> 
                            <tr align="center">
                                <td width="33%"><strong>Segmentation</strong></td>
                                <td width="33%"><strong>Translation</strong></td>
                                <td width="33%"><strong>Proofreading</strong></td>
                            </tr> 
                        </table>
                    </td>
                </tr>
                <tr id="targetLanguageTemplate_0">
                    <td> 
                        {if isset($languages)}
                            <select name="targetLanguage_0" id="targetLanguage_0" style="width: 400px">
                                {foreach $languages as $language}
                                    <option value="{$language->getCode()}">{$language->getName()}</option>
                                {/foreach}
                            </select>
                            {if isset($countries)}
                                <select name="targetCountry_0" id="targetCountry_0" style="width: 400px">
                                    {foreach $countries as $country}
                                        <option value="{$country->getCode()}">{$country->getName()}</option>
                                    {/foreach}
                                </select> 
                            {/if}
                        {else}
                            <label for="source"><h2>Source Language: <span style="color: red">*</span></h2></label>
                            <input type="text" name="source" id="source"/>
                            <input type="text" name="sourceCountry" id="source"/>
                        {/if} 
                    </td>
                    <td valign="middle">
                        <table> 
                            <tr align="center">
                                <td valign="middle"><input title="Create a segmentation task for dividing large source files into managable segments of up to 4,000 words or less." type="checkbox" id="segmentation_0" name="segmentation_0" value="y" onchange="segmentationEnabled(0)"/></td>                            
                                <td valign="middle"><input title="Create a translation task for volunteer translators to pick up." type="checkbox" id="translation_0" checked="true" name="translation_0" value="y"/></td>
                                <td valign="middle"><input title="Create a proofreading task for evaluating the translation provided by a volunteer." type="checkbox" id="proofreading_0" checked="true" name="proofreading_0" value="y"/></td>
                            </tr>                        
                        </table>  
                    </td>
                </tr>
                <tr id="horizontalLine_0">
                    <td colspan="2"><hr/></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div id="alertinfo" class="alert alert-info" style="display: none; text-align: center">You have reached the maximum number of target translation fields allowed.</div>  

                        <button id="addMoreTargetsBtn" class="btn btn-success" type="button" onclick="addNewTarget()"><i class="icon-upload icon-white"></i> Add More Target Languages</button>
                        <button id="removeBottomTargetBtn" class="btn btn-inverse" type="button" onclick="removeNewTarget()" disabled="true" style="visibility: hidden"><i class="icon-fire icon-white"></i> Remove</button>
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
                        <p style="margin-bottom:20px;"/> 
                            <a href='{urlFor name="org-dashboard"}' class='btn btn-danger'>
                                <i class="icon-ban-circle icon-white"></i> Cancel
                            </a>
                        <p style="margin-bottom:20px;"/> 
                    </td>
                    <td>

                        <p style="margin-bottom:20px;"/> 
                            <button type="submit" name="submit" value="createproject" class="btn btn-success">
                                <i class="icon-upload icon-white"></i> Create Project
                            </button>                            
                        <p style="margin-bottom:20px;"/>                     
                    </td>
                </tr>          
            </table>   
        </form>  
    </div>
    
{include file="footer.tpl"}
