{include file="header.tpl"}

<div class="page-header">
    <h1>{Localisation::getTranslation('task_review_provide_a_rating')} <small>{Localisation::getTranslation('task_review_0')}</small></h1>
</div>

{include file="handle-flash-messages.tpl"}

<p>
    {Localisation::getTranslation('task_review_1')} {Localisation::getTranslation('task_twitter_0')}
</p>

<a class="twitter-share-button"
  href="https://twitter.com/intent/tweet?text={Localisation::getTranslation('task_twitter_1')}"
  data-size="large" data-counturl="http://trommons.org/logout">
Tweet</a>

<p>
    {Localisation::getTranslation('task_review_2')}
    {$action} {Localisation::getTranslation('task_review_3')}
</p>

{if isset($formAction)}
    <form class="well" method="post" action="{$formAction}"  onsubmit="createHiddenFields()" id="TaskReviewForm" accept-charset="utf-8">
{else}
    <div class="well">
{/if}

{foreach $tasks as $task}
    {assign var="reviewedTask" value=$task}
    {if isset($reviews[$task->getId()])}
        {assign var="review" value=$reviews[$task->getId()]}
    {else}
        {assign var="review" value=null}
    {/if}
    {include file="task/task.review-form.tpl"}
{/foreach}

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
