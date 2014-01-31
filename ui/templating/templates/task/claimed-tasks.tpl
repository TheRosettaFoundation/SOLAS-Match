{include file='header.tpl'}

<div class="page-header">
    <h1>
        {if isset($thisUser)}
            {if $thisUser->getDisplayName() != ''}
                {sprintf(Localisation::getTranslation(Strings::CLAIMED_TASKS_CLAIMED_TASKS_2), {$thisUser->getDisplayName()})}
            {else}
                {Localisation::getTranslation(Strings::CLAIMED_TASKS_CLAIMED_TASKS)}
            {/if}
        {else}
            {Localisation::getTranslation(Strings::CLAIMED_TASKS_CLAIMED_TASKS)}
        {/if}
        <small>{Localisation::getTranslation(Strings::CLAIMED_TASKS_0)}</small>
    </h1>
</div>

<claimed-tasks-stream userid="{$thisUser->getId()}" tasksperpage="10"></claimed-tasks-stream>

{include file='footer.tpl'}
