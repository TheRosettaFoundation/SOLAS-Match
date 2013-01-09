<html>
<p>
    {if $user->getDisplayName() != ''}
        {$user->getDisplayName()},
    {else}
        Hello,
    {/if}
</p>

<p>
    A task that you are following on SOLAS Match has changed its status.
    The translator
    {if $translator->getDisplayName() != ''}
	{$translator->getDisplayName()}
    {/if}
    has uploaded a translation for the file "{$task->getTitle()}". To download
    the the latest version of the file please visit your <a href="{$site_url}client/dashboard">
    client dashboard</a>. You will find the download button under the {$org->getName()}
    title.
</p>
<p>
The SOLAS Match Team
</p>
</html>