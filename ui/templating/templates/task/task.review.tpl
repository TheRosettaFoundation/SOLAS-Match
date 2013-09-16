{include file="header.tpl"}

<div class="page-header">
    <h1>{Localisation::getTranslation(Strings::TASK_REVIEW_PROVIDE_A_RATING)} <small>{Localisation::getTranslation(Strings::TASK_REVIEW_0)}</small></h1>
</div>

{include file="handle-flash-messages.tpl"}

<p>
    {Localisation::getTranslation(Strings::TASK_REVIEW_1)} {Localisation::getTranslation(Strings::TASK_REVIEW_2)}
    {$action} {Localisation::getTranslation(Strings::TASK_REVIEW_3)}
</p>

{foreach $tasks as $task}
    {assign var="reviewedTask" value=$task}
    {if isset($reviews[$task->getId()])}
        {assign var="review" value=$reviews[$task->getId()]}
    {else}
        {assign var="review" value=null}
    {/if}
    {include file="task/task.review-form.tpl"}
{/foreach}

{include file="footer.tpl"}
