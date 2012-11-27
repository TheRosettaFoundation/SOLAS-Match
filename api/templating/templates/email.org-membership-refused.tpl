<p>
    {if $user->getDisplayName() != ''}
        {$user->getDisplayName()},
    {else}
        Hello,
    {/if}
</p>
    
<p>
    I regret to inform you that your request to become a member of the organisation,
    {$org->getName()}, has been rejected. Feel free to apply again another time or 
    start your own organisation so that you can upload files.
</p>

<p>
    The SOLAS Match Team
</p>
