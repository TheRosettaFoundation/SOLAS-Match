{include file='header.tpl'}
<div class='page-header'>
    <h1>Badge List<small> All the badges availabe through SOLAS Match</small></h1>
</div>

{if isset($badgeList)}
    {foreach $badgeList as $badgeEntry}
        <h3>{$badgeEntry['title']}</h3>
        <p>{$badgeEntry['description']}</p>
    {/foreach}
{/if}


{include file='footer.tpl'}
