{include file="header.tpl"}

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
    {if isset($flash['Warning'])}
        <div class="alert alert-error">
            <h3>Please fill in all required information:</h3>        
            {$flash['Warning']}
        </div>        
    {/if}
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
                    <input type="file" name="chunkUpload_0" id="chunkUpload_0"/>
                    <hr/>
                </td>                
            </tr>
            <tr id="taskUploadTemplate_1" valign="top">
                <td colspan="4"> 
                    <p class="desc">Upload your chunked file. Max file size is 8 MB.</p>
                    <input type="file" name="chunkUpload_1" id="chunkUpload_1"/>
                    <hr/>
                </td>                
            </tr>
        </tbody>    
    </table> 
    <table width="100%">
        <tr>
            <td align="center" colspan="5">
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