{include file='header.tpl'}

<div class="page-header">
    <h1>
        {if isset($user)}
            {if $user->getDisplayName() != ''}
                {sprintf(Localisation::getTranslation(Strings::CLAIMED_TASKS_CLAIMED_TASKS_2), {$user->getDisplayName()})}
            {else}
                {Localisation::getTranslation(Strings::CLAIMED_TASKS_CLAIMED_TASKS)}
            {/if}
        {else}
            {Localisation::getTranslation(Strings::CLAIMED_TASKS_CLAIMED_TASKS)}
        {/if}
        <small>{Localisation::getTranslation(Strings::CLAIMED_TASKS_0)}</small>
    </h1>
</div>

{if isset($active_tasks)}
    {if count($active_tasks) > 0}
        {for $count=$top to $bottom}
            {assign var="task" value=$active_tasks[$count]}
            {include file="task/task.claimed-tasks.tpl" task=$task}
        {/for}
        {include file="pagination.tpl" url_name="claimed-tasks" current_page=$page_no last_page=$last}
    {else}
        <div class="alert alert-warning">
            <strong>{Localisation::getTranslation(Strings::CLAIMED_TASKS_NO_ACTIVE_TASKS_AVAILABLE)}</strong> {Localisation::getTranslation(Strings::CLAIMED_TASKS_1)}
        </div>        
    {/if}
{/if}

{include file='footer.tpl'}
