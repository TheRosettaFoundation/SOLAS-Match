{include file="header.tpl"}

<div class="page-header">
    <h1>{Localisation::getTranslation('task_review_provide_a_rating')} <small>{Localisation::getTranslation('task_review_0')}</small></h1>
</div>

{include file="handle-flash-messages.tpl"}

<p>
    {Localisation::getTranslation('task_review_1')} {Localisation::getTranslation('task_twitter_0')}
</p>

<a class="twitter-share-button"
  href="https://twitter.com/intent/tweet?text={Localisation::getTranslation('task_twitter_1')}&url=https%3A%2F%2Ftrommons.org"
  data-size="large" data-counturl="https://trommons.org">
Tweet</a>

{if $action === Localisation::getTranslation('task_review_translated')}
<p>
    Please provide a rating for the source file(s) you just translated based on the following criteria:
</p>
{else}
<p>
Please provide a general review of your colleague's work.
We trust that you will do your best to provide a fair evaluation and constructive comments.
The goal is to learn from one another and grow together.
</p>
<p>
If you collaborate with TWB regularly, you may review several translations.
Please use the same standards for all reviews.
You can follow <a href="https://community.translatorswb.org/t/what-is-translation-quality-for-twb/10295" target="_blank">this link to read more about our Quality Standards</a>.
</p>
Think about the readers of the translation as you review it.
Would they find the text clear and easy to read?
<p>
Does the translation communicate the message of the original text clearly, accurately and effectively?
Please remember: the translation does not have to be "perfect" to be good.
</p>
<hr />
<p>
How would you rate the translation in each of the following categories?
</p>
{/if}

{if isset($formAction)}
    <form class="well" method="post" action="{$formAction}"  onsubmit="return areRatingsSetThenCreateHiddenFields()" id="TaskReviewForm" name="TaskReviewForm" accept-charset="utf-8">
{else}
    <div class="well">
{/if}

{foreach $tasks as $task}
    {assign var="reviewedTask" value=$task}
    {if !empty($task->getId()) && !empty($reviews[$task->getId()])}
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
        {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
    </form>
{else}
    </div>
{/if}

{include file="footer.tpl"}
