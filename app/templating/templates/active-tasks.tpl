{include file='header.tpl'}

<div class="page-header"><h1>
{if isset($user)}
    {assign var="user_id" value=$user->getUserId()}
    {if $user->getDisplayName() != ''}
        {$user->getDisplayName()}'s
    {else}
        Your
    {/if}
    active tasks
    <small>A list of tasks you are currently working on</small>

{/if}
</h1></div>

{if isset($active_tasks)}
    {if count($active_tasks) > 0}
        {foreach $active_tasks as $job}
            {include file="task.summary-link.tpl" task=$job}
        {/foreach}
    {/if}
{/if}


{include file='footer.tpl'}
