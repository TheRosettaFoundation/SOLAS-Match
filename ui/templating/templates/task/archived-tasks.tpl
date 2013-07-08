{include file='header.tpl'}

<div class='page-header'>
    <h1>
        {if isset($user) && $user->getDisplayName() != ''}{$user->getDisplayName()}'s {else} Your{/if}
        {Localisation::getTranslation(Strings::ARCHIVED_TASKS_ARCHIVED_TASKS)} <small>{Localisation::getTranslation(Strings::ARCHIVED_TASKS_0)}</small>
    </h1>
</div>

{if isset($archived_tasks) && count($archived_tasks) > 0}
    {for $count=$top to $bottom}
        {assign var="task" value=$archived_tasks[$count]}
        {include file="task/task.profile-display.tpl" task=$task}
    {/for}
    {include file="pagination.tpl" url_name="archived-tasks" current_page=$page_no last_page=$last}
{/if}

{include file='footer.tpl'}
