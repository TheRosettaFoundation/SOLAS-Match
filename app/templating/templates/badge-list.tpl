{include file='header.tpl'}

<h1>Badge List</h1>

{if isset($badgeList)}
    {foreach $badgeList as $badgeEntry}
        <h3>{$badgeEntry['title']}</h3><p>{$badgeEntry['description']}</p>
    {/foreach}
{/if}


{include file='footer.tpl'}
