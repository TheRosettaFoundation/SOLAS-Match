{include file='header.tpl'}

<div class="page-header">
    <h1>
        {if isset($thisUser)}
            {if $thisUser->getDisplayName() != ''}
                {sprintf(Localisation::getTranslation('claimed_tasks_users_claimed_tasks'), {$thisUser->getDisplayName()})}
            {else}
                {Localisation::getTranslation('claimed_tasks_claimed_tasks')}
            {/if}
        {else}
            {Localisation::getTranslation('claimed_tasks_claimed_tasks')}
        {/if}
        <small>{Localisation::getTranslation('claimed_tasks_a_list_of_tasks')}</small>
    </h1>
</div>

<div style="max-width: 70%; overflow-wrap: break-word; word-break:break-all;">
<claimed-tasks-stream userid="{$thisUser->getId()}" tasksperpage="10"></claimed-tasks-stream>
</div>
<br/>
<div style="float:left">
{include file='footer.tpl'}
</div>
