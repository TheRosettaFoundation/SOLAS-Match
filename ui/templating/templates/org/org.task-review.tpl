{include file="header.tpl"}

{assign var="taskId" value=$task->getId()}
<div class="page-header">
    <h1>{TemplateHelper::uiCleanseHTML($task->getTitle())} <small>{Localisation::getTranslation('org_task_review_review_this_completed_task')}</small></h1>
</div>

{assign var="type_id" value=$task->getTaskType()}
<h2 class="page-header">{foreach from=TaskTypeEnum::$enum_to_UI key=task_type item=ui}{if $type_id == $task_type}Review this {$ui['type_text']} task{/if}{/foreach} <small>{Localisation::getTranslation('org_task_review_1')}</small></h2>
{include file="handle-flash-messages.tpl"}

<p>
    {sprintf(Localisation::getTranslation('org_task_review_the_volunteer'), {urlFor name="user-public-profile" options="user_id.{$translator->getId()}"}, TemplateHelper::uiCleanseHTML($translator->getDisplayName()))}
</p>

{if isset($formAction)}
    <form class="well" method="post" action="{$formAction}"  onsubmit="return areRatingsSetThenCreateHiddenFields()" id="TaskReviewForm" accept-charset="utf-8">
        <div id="placeholder_for_errors_1"></div>
{else}
    <div class="well">
{/if}

{assign var="reviewedTask" value=$task}
{include file="task/task.review-form.tpl"}

 {if isset($formAction)}
        <div id="placeholder_for_errors_2"></div>
      
        {if !isset($review)}
            <button class="btn btn-primary" type="submit" name="submitReview">
                <i class="icon-upload icon-white"></i> {Localisation::getTranslation('task_review_form_submit_review')}
            </button>
        {/if}
        <button class="btn btn-inverse" type="submit" name="skip" onclick="noteSkipClicked();">
            <i class="icon-circle-arrow-right icon-white"></i> {Localisation::getTranslation('task_review_form_skip')}
        </button>
    {/if}
{if isset($formAction)}
        {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
    </form>
{else}
    </div>
{/if}

{include file="footer.tpl"}