<p>
    {if $user->getDisplayName() != ''}
        {$user->getDisplayName()},
    {else}
        Hello,
    {/if}
</p>

<p>
    A task that you are following on the SOLAS Match translation platform has changed its status. 
    The task "{$task->getTitle()}" has been archived by {$org->getName()}. This means that the
    task is now complete and has been removed from the system.
</p>

<p>
    From
</p>
<p>
    SOLAS Match Team
</p>
