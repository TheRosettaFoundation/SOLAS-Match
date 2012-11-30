<p>
    {if $user->getDisplayName() != ''}
        {$user->getDisplayName()}
    {else}
        Hello
    {/if}
    ,
</p>

<p>
    A task that you are following on SOLAS Match has changed its state.
    The task "{$task->getTitle()}{$task->getId()}" has been claimed by
    {if $translator && $translator->getDisplayName() != ''}
        {$translator->getDisplayName()}
    {else}
        a translator
    {/if}
    . You can visit the translator's public profile
    {assign var="user_id" value=$translator->getUserId()}
    <a href="{$site_url}{"/profile/$user_id"}">
        here
    </a>
    . You will be informed when a translation becomes available.
</p>
<p>
    From,
    <br />
    The SOLAS Match Team
</p>
