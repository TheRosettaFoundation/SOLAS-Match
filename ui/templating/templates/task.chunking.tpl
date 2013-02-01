{include file="header.tpl"}

<script language='javascript'>    
    var debug = null;
    var MAX_CHUNKS = {$maxChunks};
    var CURR_CHUNKS = 2;

    $(document).ready(function() {     
        debug = document.getElementById('debug');
        var chunkElements = document.getElementById('chunkingElements');        
        var formSelect = document.createElement('select');
        formSelect.setAttribute('name', 'chunkValue');
        formSelect.setAttribute('onchange', "chunkSelectChange(this);")
    
        for(var i=0; i < MAX_CHUNKS-1; ++i) {
            var optionNode = document.createElement('option');
            optionNode.setAttribute('value', i+2);
            optionNode.innerHTML += (i+2);
            formSelect.appendChild(optionNode);            
        }        
        chunkElements.appendChild(formSelect); 
    }) 
    
    function taskTypeSelection(type) {
        var translationCheckBox = document.getElementById('translation_0');
        var proofreadingCheckBox = document.getElementById('proofreading_0');
        var posteditingCheckBox = document.getElementById('postediting_0');
        
        if(type == 'translation') {
            if(translationCheckBox.checked) {            
                translationCheckBox.disabled = false;
                proofreadingCheckBox.disabled = true;
                posteditingCheckBox.disabled = true;            
            } else {
                translationCheckBox.disabled = false;
                proofreadingCheckBox.disabled = false;
                posteditingCheckBox.disabled = false;             
            }
        } else if(type == 'proofreading') {
            if(proofreadingCheckBox.checked) {
                proofreadingCheckBox.disabled = false;            
                translationCheckBox.disabled = true;
                posteditingCheckBox.disabled = true;
            } else {
                proofreadingCheckBox.disabled = false;            
                translationCheckBox.disabled = false;
                posteditingCheckBox.disabled = false;            
            }
        } else if(type == 'postediting') {
            if(posteditingCheckBox.checked) {            
                posteditingCheckBox.disabled = false;            
                translationCheckBox.disabled = true;
                proofreadingCheckBox.disabled = true;
            } else {
                posteditingCheckBox.disabled = false;            
                translationCheckBox.disabled = false;
                proofreadingCheckBox.disabled = false;            
            }            
        }    
    }
    
    function chunkSelectChange(node) {
        debug.innerHTML = '';
        var index = node.selectedIndex; 
        var value = parseInt(node.options[index].value);
        var templateNode = document.getElementById('taskUploadTemplate_0');
        var taskChunks = document.getElementById('taskChunks');
        
        if(value < CURR_CHUNKS) { 
            for(var i=CURR_CHUNKS; i > value; i--) {
                var del = document.getElementById('taskUploadTemplate_' + (i-1));
                debug.innerHTML += 'Deleting taskUploadTemplate_' + (i-1) + '<br>';
                taskChunks.removeChild(del);
            }
        
        } else if(value > CURR_CHUNKS) {
            for(var i=CURR_CHUNKS; i < value; i++) {
            debug.innerHTML += 'inserting taskUploadTemplate_' + i + '<br>';
                var clonedNode = templateNode.cloneNode(true);
                var inputs = clonedNode.getElementsByTagName('input');
                clonedNode.setAttribute('id',clonedNode.getAttribute("id").replace("0",i));
                for(var j=0; j < inputs.length; j++){
                    inputs.item(j).setAttribute('id', inputs.item(j).getAttribute('id').replace("0", i));
                    inputs.item(j).setAttribute('name', inputs.item(j).getAttribute('id'));
                }
                taskChunks.appendChild(clonedNode);
            }             
        }  
        CURR_CHUNKS = value;              
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
            <td style="text-align: left">
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
                    
<div id="debug">
     
</div>

<div class="well">
    <form method="post" enctype="multipart/form-data" action="{urlFor name="task-chunking" options="task_id.$task_id"}"> {* {urlFor name="project-view" options="project_id.$projectId"} *}
    <table border="0" width="100%">
        <tbody id="taskChunks">
            <tr>
                <td colspan="4">
                    <label for="title"><h2>Chunking:</h2></label>
                    <p class="desc">Divide large source files into smaller and more managable tasks.</p>
                    <hr/>
                </td>    
            </tr>
            <tr>
                <td><b>Number of chunks:</b></td>         
                <td align="center" valign="bottom"><b>Translation</b></td>
                <td align="center" valign="bottom"><b>Proofreading</b></td>
                <td align="center" valign="bottom"><b>Postediting</b></td>
            </tr>
            <tr>
                <td id="chunkingElements"></td>  
                <td align="center" valign="middle"><input type="checkbox" id="translation_0" checked="true" name="translation_0" value="y" onchange="taskTypeSelection('translation')"/></td>
                <td align="center" valign="middle"><input type="checkbox" id="proofreading_0" name="proofreading_0" value="y" onchange="taskTypeSelection('proofreading')" disabled/></td>
                <td align="center" valign="middle"><input type="checkbox" id="postediting_0" name="postediting_0" value="y" onchange="taskTypeSelection('postediting')" disabled/></td>                
            </tr>
            <tr>
                <td colspan="5">
                <hr/>
                </td>
            </tr>
            <tr id="taskUploadTemplate_0" valign="top">
                <td colspan="4"> 
                    <p class="desc">Upload your chunked file. Max file size is 8 MB.</p>
                    <input type="file" name="taskUpload_0" id="taskUpload_0"/>
                    <hr/>
                </td>                
            </tr>
            <tr id="taskUploadTemplate_1" valign="top">
                <td colspan="4"> 
                    <p class="desc">Upload your chunked file. Max file size is 8 MB.</p>
                    <input type="file" name="taskUpload_1" id="taskUpload_1"/>
                    <hr/>
                </td>                
            </tr>
        </tbody>    
    </table> 
    <table width="100%">
        <tr>
            <td width="50%" align="center">   
                <p style="margin-bottom:20px;"></p> 
                <a href='{urlFor name="org-dashboard"}' class='btn btn-danger'>
                    <i class="icon-ban-circle icon-white"></i> Cancel
                </a>
            </td>
            <td align="center" colspan="3">
                <p style="margin-bottom:20px;"></p> 
                <button type="submit" name="createChunking" value="1" class="btn btn-success">
                    <i class="icon-upload icon-white"></i> Submit Chunked Tasks
                </button>
            </td>
        </tr>                        
    </table>
    </form>
</div>


    
    
{include file="footer.tpl"}