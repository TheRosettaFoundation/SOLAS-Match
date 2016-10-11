{include file="header.tpl"}

{assign var="taskId" value=$task->getId()}

{include file="handle-flash-messages.tpl"}


    <h1 class="page-header">
        {TemplateHelper::uiCleanseHTML($task->getTitle())}
        <small>
            <strong>
                - <span style="color: {$taskTypeColours[TaskTypeEnum::DESEGMENTATION]}">{Localisation::getTranslation('common_desegmentation_task')}
            </strong>
        </small>   
        {assign var="task_id" value=$task->getId()}
    </h1>

{include file="task/task.details.tpl"}        

    <div class="well">
        <div class="page-header">
            <h1>
                {TemplateHelper::uiCleanseHTML($task->getTitle())} <small>{Localisation::getTranslation('task_desegmentation_desegmentation_task_details')}</small>
                <a href="{urlFor name="task-user-feedback" options="task_id.$task_id"}" style="float: right" class="btn btn-success">
                    <i class="icon-upload icon-white"></i> {Localisation::getTranslation('common_provide_feedback')}
                </a>
            </h1>
        </div>
                
        <h2>{Localisation::getTranslation('task_desegmentation_download')}</h2>
        <p>{Localisation::getTranslation('task_desegmentation_0')}</p>

        {foreach from=$preReqTasks item=pTask}
            {assign var="pTaskId" value=$pTask->getId()}
            <p>{sprintf(Localisation::getTranslation('task_desegmentation_4'), {TemplateHelper::uiCleanseHTML($pTask->getTitle())}, {urlFor name="download-task-latest-version" options="task_id.$pTaskId"})}</p>
        {/foreach}
        <p>
            You can download the <strong>original project file</strong> 
            <a href="{urlFor name="home"}project/{$task->getProjectId()}/file">here</a>.
        </p>
        <p style="margin-bottom: 40px"/>

        <h2>{Localisation::getTranslation('task_desegmentation_1')}</h2>
        <form class="well" method="post" enctype="multipart/form-data" action="{urlFor name="task-desegmentation" options="task_id.$taskId"}" accept-charset="utf-8">
            <p><input type="file" name="{$fieldName}" id="{$fieldName}" /></p>
            <p><button type="submit" class="btn btn-success"><i class="icon-upload icon-white"></i> {Localisation::getTranslation('task_desegmentation_upload')}</button>
            <input type="hidden" name="sesskey" value="{$sesskey}" />
        </form>

        {if isset($file_previously_uploaded) && $file_previously_uploaded}
            <br />
            <div class="alert">
                <p>{Localisation::getTranslation('common_thanks_for_providing_your_translation_for_this_task')}
                {if $org != null && $org->getName() != ''}
                    {sprintf(Localisation::getTranslation('task_desegmentation_2'), {$org->getName()})}
                {else}
                    {Localisation::getTranslation('task_desegmentation_5')}
                {/if}
                </p>                
            </div>
        {/if}
    </div>

{include file="footer.tpl"}
