{include file='header.tpl'}

<div class="page-header">
    <h1>
        {if isset($user)}
            {if $user->getDisplayName() != ''}
                {$user->getDisplayName()}'s
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

<div class="task" is="x-claimed-tasks-stream" user-id="{$user->getId()}" tasks-per-page="2" id="claimedTasksStream"></div>

<script type="text/javascript" src="{urlFor name="home"}ui/dart/deploy/web/packages/browser/interop.js"></script>
<script type="text/javascript" src="{urlFor name="home"}ui/dart/deploy/web/packages/browser/dart.js"></script>
<script type="application/dart" src="{urlFor name="home"}ui/dart/deploy/web/Routes/Tasks/ClaimedTasks.dart"></script>

{include file='footer.tpl'}
