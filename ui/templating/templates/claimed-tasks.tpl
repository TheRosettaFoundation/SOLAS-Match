{include file='header.tpl'}

<div class="page-header">
    <h1>
        {if isset($user)}
            {if $user->getDisplayName() != ''}
                {$user->getDisplayName()}'s
            {else}
                Your
            {/if}
        {else}
            Your
        {/if}
        Claimed Tasks
        <small>A list of tasks you are currently working on.</small>
    </h1>
</div>

{if isset($active_tasks)}
    {if count($active_tasks) > 0}
        {for $count=$top to $bottom}
            {assign var="task" value=$active_tasks[$count]}
            {include file="task.claimed-tasks.tpl" task=$task}
        {/for}
        {include file="pagination.tpl" url_name="claimed-tasks" current_page=$page_no last_page=$last}
    {else}
        <div class="alert alert-warning">
            <strong>No Active Tasks available!</strong> You can claim tasks from the 'Task Stream' on the home page.
        </div>        
    {/if}
{/if}

{include file='footer.tpl'}
