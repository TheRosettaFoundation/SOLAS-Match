<p>
    {if $user->getDisplayName() != ''}
    	{$user->getDisplayName()},
    {else}
        Hello,
    {/if}
</p>

<p>
    Congratulations! The organisation "{$org->getName()}" has added you to their
    members list. You can now upload files on behalf of the organisation as well
    as alter existing tasks' details. You can also view any translations that
    have been uploaded for tasks related to this organisation or archive tasks
    that are no longer required.
</p>

<p>
    To get started with the Organisation please visit your client dashboard.
</p>

<p>
    The SOLAS Match Team
</p>
