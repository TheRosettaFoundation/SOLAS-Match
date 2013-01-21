{include file='header.tpl'}

<div class='page-header'><h1>
    {if isset($user) && $user->getDisplayName() != ''}
        {$user->getDisplayName()}'s
    {else}
        Your
    {/if}
    Archived Tasks <small>A list of tasks you have worked on in the past</small>
</h1></div>

{if isset($archived_tasks) && count($archived_tasks) > 0}
    {for $count=$top to $bottom}
        {assign var="task" value=$archived_tasks[$count]}
        {include file="task.profile-display.tpl" task=$task}
    {/for}
    {include file="pagination.tpl" url_name="archived-tasks" current_page=$page_no last_page=$last}
{/if}

{include file='footer.tpl'}
