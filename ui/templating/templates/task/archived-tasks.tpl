{include file='header.tpl'}

<div class='page-header'>
    <h1>
        {if isset($user) && $user->getDisplayName() != ''}{$user->getDisplayName()}'s {else} Your{/if}
        {Localisation::getTranslation('archived_tasks_archived_tasks')} <small>{Localisation::getTranslation('archived_tasks_0')}</small>
    </h1>
</div>

{if isset($archivedTasks) && $archivedTasksCount > 0}
    {for $count=$top to $bottom}
        {assign var="task" value=$archivedTasks[$count]}
        {include file="task/task.profile-display.tpl" task=$task}
    {/for}
    {include file="pagination.tpl" url_name="archived-tasks" current_page=$page_no last_page=$last}
{/if}

{include file='footer.tpl'}
