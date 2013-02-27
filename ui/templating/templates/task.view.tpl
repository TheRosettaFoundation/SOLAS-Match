{include file="header.tpl"}

    <h1 class="page-header" style="height: auto">
        <span style="height: auto; width: 750px; overflow-wrap: break-word; display: inline-block;">
            {if $task->getTitle() != ''}
                {$task->getTitle()}
            {else}
                Task {$task->getId()}
            {/if}

            <small>
                <strong>
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
                </strong>
            </small>  
        </span>
        {assign var="task_id" value=$task->getId()}

        <div class="pull-right">
            {if $task->getTaskStatus() == TaskStatusEnum::PENDING_CLAIM}
                <a href="{urlFor name="task-claim-page" options="task_id.$task_id"}" class="btn btn-primary">
                <i class="icon-download icon-white"></i> Claim Task</a>
            {/if}

            {if isset($isOrgMember)}
                <a href="{urlFor name="task-alter" options="task_id.$task_id"}" class='btn btn-primary'>
                    <i class="icon-wrench icon-white"></i> Edit Task Details
                </a>
            {/if}
        </div>
    </h1>
    
{if isset($flash['success'])}
    <p class="alert alert-success">
        <strong>Success:</strong> {$flash['success']}
    </p>
{/if}

{if isset($flash['error'])}
    <p class="alert alert-error">
        <strong>Error:</strong> {$flash['error']}
    </p>
{/if}

{include file="task.details.tpl"} 

    <p style="margin-bottom: 40px"/>        
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