{include file="header.tpl"}

{assign var="taskId" value=$task->getId()}

{include file="handle-flash-messages.tpl"}


    <h1 class="page-header">
        {$task->getTitle()}
        <small>
            <strong>
                - <span style="color: {$taskTypeColours[TaskTypeEnum::DESEGMENTATION]}">{Localisation::getTranslation(Strings::COMMON_DESEGMENTATION_TASK)}
            </strong>
        </small>   
        {assign var="task_id" value=$task->getId()}
    </h1>

{include file="task/task.details.tpl"}        

    <div class="well">
        <div class="page-header">
            <h1>
                {$task->getTitle()} <small>{Localisation::getTranslation(Strings::TASK_DESEGMENTATION_DESEGMENTATION_TASK_DETAILS)}</small>
                <a href="{urlFor name="task-user-feedback" options="task_id.$task_id"}" style="float: right" class="btn btn-success">
                    <i class="icon-upload icon-white"></i> {Localisation::getTranslation(Strings::COMMON_PROVIDE_FEEDBACK)}
                </a>
            </h1>
        </div>
                
        <h2>{Localisation::getTranslation(Strings::TASK_DESEGMENTATION_DOWNLOAD)}</h2>
        <p>{Localisation::getTranslation(Strings::TASK_DESEGMENTATION_0)}</p>

        {foreach from=$preReqTasks item=pTask}
            {assign var="pTaskId" value=$pTask->getId()}
            <p>{Localisation::getTranslation(Strings::TASK_DESEGMENTATION_DOWNLOAD)} {$pTask->getTitle()} <a href="{urlFor name="download-task-latest-version" options="task_id.$pTaskId"}">{Localisation::getTranslation(Strings::COMMON_HERE)}</a></p>
        {/foreach}
        <p>
            You can download the <strong>original project file</strong> 
            <a href="{urlFor name="home"}api/v0/projects/{$task->getProjectId()}/file">here</a>.
        </p>
        <p style="margin-bottom: 40px"/>

        <h2>{Localisation::getTranslation(Strings::TASK_DESEGMENTATION_1)}</h2>
        <form class="well" method="post" enctype="multipart/form-data" action="{urlFor name="task-desegmentation" options="task_id.$taskId"}" accept-charset="utf-8">
            <p><input type="file" name="{$fieldName}" id="{$fieldName}" /></p>
            <p><button type="submit" class="btn btn-success"><i class="icon-upload icon-white"></i> {Localisation::getTranslation(Strings::TASK_DESEGMENTATION_UPLOAD)}</button>
        </form>

        {if isset($file_previously_uploaded) && $file_previously_uploaded}
            <br />
            <div class="alert">
                <p>{Localisation::getTranslation(Strings::COMMON_THANKS_FOR_PROVIDING_YOUR_TRANSLATION_FOR_THIS_TASK)}
                {if $org != null && $org->getName() != ''}
                    {$org->getName()}
                {else}
                    {Localisation::getTranslation(Strings::COMMON_THIS_ORGANISATION)}
                {/if}
                {Localisation::getTranslation(Strings::TASK_DESEGMENTATION_2)}</p>
                <p><strong>{Localisation::getTranslation(Strings::COMMON_WARNING)}! </strong>{Localisation::getTranslation(Strings::TASK_DESEGMENTATION_2)}</p>
            </div>
        {/if}
    </div>

{include file="footer.tpl"}
