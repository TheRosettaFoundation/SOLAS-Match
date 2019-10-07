{include file="header.tpl"}

{assign var="taskId" value=$task->getId()}
<div class="page-header">
    <h1>{TemplateHelper::uiCleanseHTML($task->getTitle())} <small>{Localisation::getTranslation('org_task_review_review_this_completed_task')}</small></h1>
</div>

<h2 class="page-header">{if $task->getTaskType() == TaskTypeEnum::TRANSLATION}Review this translation task{else}Review this revising task{/if} <small>{Localisation::getTranslation('org_task_review_1')}</small></h2>
{include file="handle-flash-messages.tpl"}

<p>
    {sprintf(Localisation::getTranslation('org_task_review_the_volunteer'), {urlFor name="user-public-profile" options="user_id.{$translator->getId()}"}, TemplateHelper::uiCleanseHTML($translator->getDisplayName()))}
</p>

<p>
{Localisation::getTranslation('task_twitter_0_org_task_review')} <a class="twitter-share-button"
  href="https://twitter.com/intent/tweet?text={Localisation::getTranslation('task_twitter_4')}&url=https%3A%2F%2Ftrommons.org"
  data-size="large" data-counturl="https://trommons.org">
Tweet</a>
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