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
        {if $task->getTaskStatus() == TaskStatusEnum::PENDING_CLAIM}
            <a href="{urlFor name="download-task-preview" options="task_id.$task_id"}" class="btn btn-primary">
            <i class="icon-download icon-white"></i> Claim Task</a>
        {/if}

        {if isset($isOrgMember)}
            <a href="{urlFor name="task-alter" options="task_id.$task_id"}" class='btn btn-primary'>
                <i class="icon-wrench icon-white"></i> Edit Task Details
            </a>
        {else}
                {if Settings::get('converter.converter_enabled') == "y"}
                    <a href="{urlFor name="download-task-preview" options="task_id.$task_id"}?convertToXliff=true" class="btn btn-primary">
                    <i class="icon-download icon-white"></i> Download as XLIFF</a>   
                {/if}
        {/if}
    </div>
</h1>
        
{include file="task.details.tpl"}        

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
