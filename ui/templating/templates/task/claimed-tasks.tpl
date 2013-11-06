{include file='header.tpl'}

<div class="page-header">
    <h1>
        {if isset($thisUser)}
            {if $thisUser->getDisplayName() != ''}
                {$thisUser->getDisplayName()}'s
            {else}
                {Localisation::getTranslation(Strings::COMMON_YOUR)}
            {/if}
        {else}
            {Localisation::getTranslation(Strings::COMMON_YOUR)}
        {/if}
        {Localisation::getTranslation(Strings::CLAIMED_TASKS_CLAIMED_TASKS)}
        <small>{Localisation::getTranslation(Strings::CLAIMED_TASKS_0)}</small>
    </h1>
</div>

<claimed-tasks-stream userid="{$thisUser->getId()}" tasksperpage="10"></claimed-tasks-stream>

{include file='footer.tpl'}
