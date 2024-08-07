{include file="header.tpl"}
<!-- Editor Hint: ¿áéíóú -->

{assign var="task_id" value=$task->getId()}

{include file="handle-flash-messages.tpl"}

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
                {foreach from=TaskTypeEnum::$enum_to_UI key=task_type item=ui}
                    {if $type_id == $task_type}
                        <span style="color: {$ui['colour']}">{$ui['type_text']} Task</span>
                    {/if}
                {/foreach}
            </strong>
        </small>   
        {assign var="task_id" value=$task->getId()}
    </h1>

{include file="task/task.details_v0.tpl"}

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

        <form class="well" method="post" action="{urlFor name="task-chunk-complete" options="task_id.$task_id"}" enctype="application/x-www-form-urlencoded">
            <input type="hidden" name="task_id" value="{$task->getId()}" />
            <input type="hidden" name="copy_from_matecat" value="1" />
            {if $type_id == TaskTypeEnum::TRANSLATION}
            <button type="submit" value="submit" name="submit" class="btn btn-success"><i class="icon-upload icon-white"></i> Mark Chunk Complete so translation can later be copied from Kató TM to Kató</button>
            <p>
                <br />{sprintf(Localisation::getTranslation('task_simple_upload_view_on_kato'), {$matecat_url})}<br />
            </p>
            {elseif $type_id == TaskTypeEnum::PROOFREADING}
            <button type="submit" value="submit" name="submit" class="btn btn-success"><i class="icon-upload icon-white"></i> Mark Chunk Complete so revised version can later be copied from Kató TM to Kató</button>
            <p>
                <br />{sprintf(Localisation::getTranslation('task_simple_upload_view_on_kato_proofread'), {$matecat_url})}<br />
            </p>
            {/if}
            {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
        </form>
    </div>

{include file="footer.tpl"}
