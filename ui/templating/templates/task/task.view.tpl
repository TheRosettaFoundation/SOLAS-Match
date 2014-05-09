{include file="header.tpl"}

    <h1 class="page-header" style="height: auto">
        <span style="height: auto; width: 750px; overflow-wrap: break-word; display: inline-block; word-break:break-all;">
            {if $task->getTitle() != ''}
                {$task->getTitle()}
            {else}
                {Localisation::getTranslation('common_task')} {$task->getId()}
            {/if}

            <small>
                <strong>
                     -
                    {assign var="type_id" value=$task->getTaskType()}
                    {if $type_id == TaskTypeEnum::SEGMENTATION}
                        <span style="color: {$taskTypeColours[TaskTypeEnum::SEGMENTATION]}">{Localisation::getTranslation('common_segmentation_task')}</span>
                    {elseif $type_id == TaskTypeEnum::TRANSLATION}
                        <span style="color: {$taskTypeColours[TaskTypeEnum::TRANSLATION]}">{Localisation::getTranslation('common_translation_task')}</span>
                    {elseif $type_id == TaskTypeEnum::PROOFREADING}
                        <span style="color: {$taskTypeColours[TaskTypeEnum::PROOFREADING]}">{Localisation::getTranslation('common_proofreading_task')}</span>
                    {elseif $type_id == TaskTypeEnum::DESEGMENTATION}
                        <span style="color: {$taskTypeColours[TaskTypeEnum::DESEGMENTATION]}">{Localisation::getTranslation('common_desegmentation_task')}</span>
                    {/if}
                </strong>
            </small>  
        </span>
        {assign var="task_id" value=$task->getId()}

        <div class="pull-right">
            {if $task->getTaskStatus() == TaskStatusEnum::PENDING_CLAIM}
                <a href="{urlFor name="task-claim-page" options="task_id.$task_id"}" class=" pull-right btn btn-primary">
                <i class="icon-download icon-white"></i> {Localisation::getTranslation('task_view_download_task')}</a>
            {/if}
            {if $isMember}
                <a href="{urlFor name="task-alter" options="task_id.$task_id"}" class='pull-right fixMargin btn btn-primary'>
                    <i class="icon-wrench icon-white"></i> {Localisation::getTranslation('task_view_edit_task_details')}
                </a>
            {else}
                {if isset($registered)}
                    <form id="trackedOrganisationForm" class="fixMargin" method="post" action="{urlFor name="task-view" options="task_id.$task_id"}">
                {else}
                    <form id="trackedOrganisationForm" class="fixMargin" method="post" action="{urlFor name="task" options="task_id.$task_id"}">
                {/if}
                {if $userSubscribedToOrganisation}
                    <input type="hidden" name="trackOrganisation" value="0" />
                    <a class="btn btn-small btn-inverse" onclick="$('#trackedOrganisationForm').submit();" >
                        <i class="icon-remove-circle icon-white"></i>{Localisation::getTranslation('org_public_profile_untrack_organisation')}
                    </a>
                {else}
                    <input type="hidden" name="trackOrganisation" value="1" />
                    <a class="btn btn-small" onclick="$('#trackedOrganisationForm').submit();" >
                        <i class="icon-envelope icon-black"></i>{Localisation::getTranslation('org_public_profile_track_organisation')}
                    </a>
                {/if}
                </form>
            {/if}
            
        </div>
    </h1>

    {if $task->getTaskStatus() > TaskStatusEnum::PENDING_CLAIM}
        <p class="alert alert-info">
            {Localisation::getTranslation('task_view_0')}
        </p>
    {/if}
    
    {if isset($flash['success'])}
        <p class="alert alert-success">
            <strong>{Localisation::getTranslation('common_success')}:</strong> {$flash['success']}
        </p>
    {/if}

    {if isset($flash['error'])}
        <p class="alert alert-error">
            <strong>{Localisation::getTranslation('common_warning')}:</strong> {$flash['error']}
        </p>
    {/if}

    {include file="task/task.details.tpl"} 

    <p style="margin-bottom: 40px"/>        
    <table width="100%">
        <thead>
            <th>{Localisation::getTranslation('task_view_source_document_preview')} - {$filename}<hr/></th>
        </thead>
        <tbody>
            <tr>
                <td align="center"><iframe src="http://docs.google.com/viewer?url={urlencode($file_preview_path)}&embedded=true" width="800" height="780" style="border: none;"></iframe></td>
            </tr>
        </tbody>
    </table>

{include file="footer.tpl"}
