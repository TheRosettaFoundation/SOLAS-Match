{include file="header.tpl"}

<div class="page-header">
    <h1>{Localisation::getTranslation(Strings::TASK_REVIEW_PROVIDE_A_RATING)} <small>{Localisation::getTranslation(Strings::TASK_REVIEW_0)}</small></h1>
</div>

{include file="handle-flash-messages.tpl"}

<p>
    {Localisation::getTranslation(Strings::TASK_REVIEW_1)} {Localisation::getTranslation(Strings::TASK_REVIEW_2)}
    {$action} {Localisation::getTranslation(Strings::TASK_REVIEW_3)}
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
                <i class="icon-upload icon-white"></i> {Localisation::getTranslation(Strings::TASK_REVIEW_FORM_SUBMIT_REVIEW)}
            </button>
        {/if}
        <button class="btn btn-inverse" type="submit" name="skip">
            <i class="icon-circle-arrow-right icon-white"></i> {Localisation::getTranslation(Strings::TASK_REVIEW_FORM_SKIP)}
        </button>
    {/if}
{if isset($formAction)}
    </form>
{else}
    </div>
{/if}

{include file="footer.tpl"}
