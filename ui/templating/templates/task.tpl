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
                <span style="color: {$taskTypeColours[TaskTypeEnum::TRANSLATION]}">Translation Task</span> 
            {elseif $type_id == TaskTypeEnum::PROOFREADING}
                <span style="color: {$taskTypeColours[TaskTypeEnum::PROOFREADING]}">Proofreading Task</span> 
            {elseif $type_id == TaskTypeEnum::POSTEDITING}
                <span style="color: {$taskTypeColours[TaskTypeEnum::POSTEDITING]}">Postediting Task</span> 
            {/if}
        </b>
    </small>   
    {assign var="task_id" value=$task->getId()}
    
    <div class="pull-right">
        <a href="{urlFor name="download-task-preview" options="task_id.$task_id"}" class="btn btn-primary">
        <i class="icon-download icon-white"></i> Claim Task</a>

        {if Settings::get('converter.converter_enabled') == "y"}
            <a href="{urlFor name="download-task-preview" options="task_id.$task_id"}?convertToXliff=true" class="btn btn-primary">
            <i class="icon-download icon-white"></i> Download as XLIFF</a>   
        {/if}
    </div>
</h1>
        
        
<table class="table table-striped">
    <thead>            
        <th style="text-align: left"><b>Project</b></th>

        <th>Source Language</th>
        <th>Target Language</th>
        <th>Created</th> 
        <th>Task Deadline</th>
        <th>Word Count</th>
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
                {if $task->getWordCount() != ''}
                    {$task->getWordCount()}
                {else}
                    -
                {/if}              
            </td> 
        </tr> 
    </tbody>
</table>       
      
<div class="well">
    <table width="100%">
        <tbody>
            <tr>
                <td><b>Task Comment:</b></td>
            </tr>
            <tr>
                <td><hr/></td>
            </tr>
            <tr>
                <td>
                    <i>
                    {if $task->getComment() != ''}
                        {$task->getComment()}
                    {else}
                       No comment has been added.
                    {/if}
                    </i>
                    <p style="margin-bottom: 40px" />
                </td>
            </tr>
            <tr>
                <td><b>Project Description:</b></td>
            </tr>
            <tr>
                <td>
                    <hr/>
                </td>
            </tr>
            <tr>
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
        

{if isset($flash['success'])}
    <p class="alert alert-success">
        {$flash['success']}
    </p>
{/if}

{if isset($flash['error'])}
    <p class="alert alert-error">
        <b>Warning!</b> {$flash['error']}
    </p>
{/if}



<div style="margin-bottom: 40px"></div>        
<table width="100%">
    <thead>
    <th>Source Document Preview - {$filename}<hr/></th>
    </thead>
    <tbody>
        <tr>
            <td align="center"><iframe src="http://docs.google.com/viewer?url={urlencode($file_preview_path)}&embedded=true" width="800" height="780" style="border: none;"></iframe></td>
        </tr>
    </tbody>
</table>            


{include file="footer.tpl"}
