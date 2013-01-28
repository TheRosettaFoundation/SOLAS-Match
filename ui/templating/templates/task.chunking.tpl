{include file="header.tpl"}

     
<script language='javascript'>     
    var MAX_CHUNKS = {$maxChunks};

    window.onload = function(){    

        var chunkElements = document.getElementById('chunkingElements');
        var formSelect = document.createElement('select');
    
        for(var i=0; i < MAX_CHUNKS-1; ++i) {
            var optionNode = document.createElement('option');
            optionNode.setAttribute('value', i+2);
            optionNode.innerHTML += (i+2);
            formSelect.appendChild(optionNode);            
        }
        
        chunkElements.appendChild(formSelect);    
    }            
</script>

<h1 class="page-header">
    {if $task->getTitle() != ''}
        {$task->getTitle()}
    {else}
        Task {$task->getId()}
    {/if}
    <small>
        <b>
            -
            {assign var="type_id" value=$task->getTaskType()}
            {if $type_id == TaskTypeEnum::CHUNKING}
                <span style="color: {$taskTypeColours[TaskTypeEnum::CHUNKING]}">Chunking Task</span>                                    
            {elseif $type_id == TaskTypeEnum::TRANSLATION}
                <span style="color: {$taskTypeColours[TaskTypeEnum::TRANSLATION]}">Translation Task
            {elseif $type_id == TaskTypeEnum::PROOFREADING}
                <span style="color: {$taskTypeColours[TaskTypeEnum::PROOFREADING]}">Proofreading Task
            {elseif $type_id == TaskTypeEnum::POSTEDITING}
                <span style="color: {$taskTypeColours[TaskTypeEnum::POSTEDITING]}">Postediting Task
            {/if}
        </b>
    </small>   
    {assign var="task_id" value=$task->getId()}
</h1>
        
        
<table class="table table-striped">
    <thead>            
        <th style="text-align: left"><b>Project</b></th>

        <th><b>Source Language</b></th>
        <th><b>Target Language</b></th>
        <th><b>Created</b></th> 
        <th><b>Task Deadline</b></th>
        <th><b>Word Count</b></th>
        <th><b>Status</b></th>
    </thead>
    <tbody>
        <tr>
            <td>
                {if isset($project)}
                    {assign var="projectId" value=$project->getId()}
                    <a href="{urlFor name="project-view" options="project_id.$projectId"}">
                        {$project->getTitle()}
                    </a>
                {/if}
            </td>

            <td>
                {TemplateHelper::getTaskSourceLanguage($task)} 
            </td>
            <td>
                {TemplateHelper::getTaskTargetLanguage($task)}
            </td>
            <td>
                {date("D dS, M Y", strtotime($task->getCreatedTime()))}
            </td>
            <td>
                {date("D dS, M Y", strtotime($task->getDeadline()))}
            </td>
            <td>
                {$task->getWordCount()}                
            </td> 
            <td>                            
                {assign var="status_id" value=$task->getTaskStatus()}
                {if $status_id == TaskStatusEnum::WAITING_FOR_PREREQUISITES}
                    Waiting
                {elseif $status_id == TaskStatusEnum::PENDING_CLAIM}
                    Unclaimed
                {elseif $status_id == TaskStatusEnum::IN_PROGRESS}
                    <a href="{urlFor name="task-feedback" options="task_id.$task_id"}">In Progress</a>
                {elseif $status_id == TaskStatusEnum::COMPLETE}
                    <a href="{urlFor name="api"}v0/tasks/{$task_id}/file/?">Complete</a>
                {/if}
            </td>
        </tr> 
    </tbody>
</table>        
      
<div class="well">
    <table width="100%">
        <thead>
        <th width="48%" align="left">Task Comment:<hr/></th>
        <th></th>
        <th width="48%" align="left">Project Description:<hr/></th>
        </thead>
        <tbody>
            <tr>
                <td>
                    <i>
                    {if $task->getComment() != ''}
                        {$task->getComment()}
                    {else}
                       No comment has been added.
                    {/if}
                    </i>
                </td>
                <td></td>
                <td>
                    <i>
                    {if $project->getDescription() != ''}
                        {$project->getDescription()}
                    {else}
                        No description has been added.
                    {/if}
                    </i>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<div class="well">
    <form method="post" action="{urlFor name="project-view" options="project_id.$projectId"}">
    <table border="0" width="100%">
        <tbody>
            <tr>
                <td colspan="5">
                    <label for="title"><h2>Chunking:</h2></label>
                    <p class="desc">Divide large source files into smaller and more managable tasks.</p>
                    <hr/>
                </td>    
            </tr>
            <tr>
                <td width="15%">Number of chunks:</td>
                <td width="45%" id="chunkingElements"></td>            
                <td align="center" valign="bottom">Translation</td>
                <td align="center" valign="bottom">Proofreading</td>
                <td align="center" valign="bottom">Postediting</td>
            </tr>
            <tr>
                <td colspan="5">
                <hr/>
                </td>
            </tr>
            <tr valign="top">
                <td> 
                    {if isset($languages)}
                        <select name="targetLanguage_0" id="targetLanguage_0" >
                            {foreach $languages as $language}
                                <option value="{$language->getCode()}">{$language->getName()}</option>
                            {/foreach}
                        </select>
                    {/if}
                </td>
                <td>
                    {if isset($countries)}
                        <select name="targetCountry_0" id="targetCountry_0">
                            {foreach $countries as $country}
                                <option value="{$country->getCode()}">{$country->getName()}</option>
                            {/foreach}
                        </select> 
                    {/if}
                </td>

                      
                <td align="center"><input type="checkbox" id="translation_0" checked="true" name="translation_0" value="y"></td>
                <td align="center"><input type="checkbox" id="proofreading_0" name="proofreading_0" value="y"></td>
                <td align="center"><input type="checkbox" id="postediting_0" name="postediting_0" value="y"></td>
                   
                 

                    
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
        </tbody>    
    </table>
    </form>
</div>

{include file="footer.tpl"}