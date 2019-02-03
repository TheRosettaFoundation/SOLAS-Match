{include file="header.tpl"}

{assign var="task_id" value=$task->getId()}

{include file="handle-flash-messages.tpl"}

{include file="header.tpl"}
<!-- Editor Hint: ¿áéíóú -->

    <h1 class="page-header">
        {if $task->getTitle() != ''}
            {TemplateHelper::uiCleanseHTMLNewlineAndTabs($task->getTitle())}
        {else}
            {Localisation::getTranslation('common_task')} {$task->getId()}
        {/if}
        <small>
            <strong>
                -
                {assign var="type_id" value=$task->getTaskType()}
                {if $type_id == TaskTypeEnum::TRANSLATION}
                    <span style="color: {$taskTypeColours[TaskTypeEnum::TRANSLATION]}">{Localisation::getTranslation('common_translation_task')}
                {elseif $type_id == TaskTypeEnum::PROOFREADING}
                    <span style="color: {$taskTypeColours[TaskTypeEnum::PROOFREADING]}">{Localisation::getTranslation('common_proofreading_task')}
                {/if}
            </strong>
        </small>   
        {assign var="task_id" value=$task->getId()}
    </h1>

{include file="task/task.details.tpl"}        

    <div class="well">
        <div class="page-header">
            <h1>{Localisation::getTranslation('task_simple_upload_finished_processing')}
                <form method="post" action="{urlFor name="task-user-feedback" options="task_id.$task_id"}" enctype="application/x-www-form-urlencoded">
                    <button style="float: right" class="btn btn-success" type="submit" value="Submit Feedback"><i class="icon-upload icon-white"></i> {Localisation::getTranslation('task_simple_upload_provide_feedback')}</button>   
                    {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
                </form>
            </h1>
            <div class="pull-right" >

            </div>
        </div>

        {if $matecat_url != ''}
        <form class="well" method="post" action="{urlFor name="task-simple-upload" options="task_id.$task_id"}" enctype="application/x-www-form-urlencoded">
            <input type="hidden" name="task_id" value="{$task->getId()}" />
            <input type="hidden" name="copy_from_matecat" value="1" />
            {if $type_id == TaskTypeEnum::TRANSLATION}
            {Localisation::getTranslation('task_claimed_alternative_option')} <button type="submit" value="submit" name="submit" class="btn btn-success"><i class="icon-upload icon-white"></i> {Localisation::getTranslation('task_simple_upload_copy_from_kato')}</button>
            <p>
                {sprintf(Localisation::getTranslation('task_simple_upload_view_on_kato'), {$matecat_url})}<br />
            </p>
            {elseif $type_id == TaskTypeEnum::PROOFREADING}
            {Localisation::getTranslation('task_claimed_alternative_option')} <button type="submit" value="submit" name="submit" class="btn btn-success"><i class="icon-upload icon-white"></i> {Localisation::getTranslation('task_simple_upload_copy_from_kato_proofread')}</button>
            <p>
                {sprintf(Localisation::getTranslation('task_simple_upload_view_on_kato_proofread'), {$matecat_url})}<br />
            </p>
            {/if}
            {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
        </form>
        {/if}
    </div>

{include file="footer.tpl"}
