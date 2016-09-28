{include file="header.tpl"}

{assign var="taskId" value=$task->getId()}
<div class="page-header">
    <h1>{TemplateHelper::uiCleanseHTML($task->getTitle())} <small>{Localisation::getTranslation('org_task_review_review_this_completed_task')}</small></h1>
</div>

<h2 class="page-header">{Localisation::getTranslation('org_task_review_review_this_file')} <small>{Localisation::getTranslation('org_task_review_1')}</small></h2>
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
    <form class="well" method="post" action="{$formAction}"  onsubmit="createHiddenFields()" id="TaskReviewForm" accept-charset="utf-8">
{else}
    <div class="well">
{/if}

{assign var="reviewedTask" value=$task}
{include file="task/task.review-form.tpl"}

 {if isset($formAction)}
      
        {if !isset($review)}
            <button class="btn btn-primary" type="submit" name="submitReview">
                <i class="icon-upload icon-white"></i> {Localisation::getTranslation('task_review_form_submit_review')}
            </button>
        {/if}
        <button class="btn btn-inverse" type="submit" name="skip">
            <i class="icon-circle-arrow-right icon-white"></i> {Localisation::getTranslation('task_review_form_skip')}
        </button>
    {/if}
{if isset($formAction)}
    </form>
{else}
    </div>
{/if}

{include file="footer.tpl"}