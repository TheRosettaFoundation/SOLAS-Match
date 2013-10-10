{include file="header.tpl"}

    <h1 class="page-header" style="height: auto">
        <span style="height: auto; width: 750px; overflow-wrap: break-word; display: inline-block;">
            {if $task->getTitle() != ''}
                {$task->getTitle()}
            {else}
                {Localisation::getTranslation(Strings::COMMON_TASK)} {$task->getId()}
            {/if}

            <small>
                <strong>
                     -
                    {assign var="type_id" value=$task->getTaskType()}
                    {if $type_id == TaskTypeEnum::SEGMENTATION}
                        <span style="color: {$taskTypeColours[TaskTypeEnum::SEGMENTATION]}">{Localisation::getTranslation(Strings::COMMON_SEGMENTATION_TASK)}</span>
                    {elseif $type_id == TaskTypeEnum::TRANSLATION}
                        <span style="color: {$taskTypeColours[TaskTypeEnum::TRANSLATION]}">{Localisation::getTranslation(Strings::COMMON_TRANSLATION_TASK)}</span>
                    {elseif $type_id == TaskTypeEnum::PROOFREADING}
                        <span style="color: {$taskTypeColours[TaskTypeEnum::PROOFREADING]}">{Localisation::getTranslation(Strings::COMMON_PROOFREADING_TASK)}</span>
                    {elseif $type_id == TaskTypeEnum::DESEGMENTATION}
                        <span style="color: {$taskTypeColours[TaskTypeEnum::DESEGMENTATION]}">{Localisation::getTranslation(Strings::COMMON_DESEGMENTATION_TASK)}</span>
                    {/if}
                </strong>
            </small>  
        </span>
        {assign var="task_id" value=$task->getId()}

        <div class="pull-right">
            {if $task->getTaskStatus() == TaskStatusEnum::PENDING_CLAIM}
                <a href="{urlFor name="task-claim-page" options="task_id.$task_id"}" class="btn btn-primary">
                <i class="icon-download icon-white"></i> {Localisation::getTranslation(Strings::TASK_VIEW_DOWNLOAD_TASK)}</a>
            {/if}

            {if isset($isOrgMember)}
                <a href="{urlFor name="task-alter" options="task_id.$task_id"}" class='btn btn-primary'>
                    <i class="icon-wrench icon-white"></i> {Localisation::getTranslation(Strings::TASK_VIEW_EDIT_TASK_DETAILS)}
                </a>
            {/if}
        </div>
    </h1>

    {if $task->getTaskStatus() > TaskStatusEnum::PENDING_CLAIM}
        <p class="alert alert-info">
            {Localisation::getTranslation(Strings::TASK_VIEW_0)}
        </p>
    {/if}
    
    {if isset($flash['success'])}
        <p class="alert alert-success">
            <strong>{Localisation::getTranslation(Strings::COMMON_SUCCESS)}:</strong> {$flash['success']}
        </p>
    {/if}

    {if isset($flash['error'])}
        <p class="alert alert-error">
            <strong>{Localisation::getTranslation(Strings::COMMON_WARNING)}:</strong> {$flash['error']}
        </p>
    {/if}

    {include file="task/task.details.tpl"} 

    <p style="margin-bottom: 40px"/>        
    <table width="100%">
        <thead>
            <th>{Localisation::getTranslation(Strings::TASK_VIEW_SOURCE_DOCUMENT_PREVIEW)} - {$filename}<hr/></th>
        </thead>
        <tbody>
            <tr>
                <td align="center"><iframe src="http://docs.google.com/viewer?url={urlencode($file_preview_path)}&embedded=true" width="800" height="780" style="border: none;"></iframe></td>
            </tr>
        </tbody>
    </table>

{include file="footer.tpl"}
