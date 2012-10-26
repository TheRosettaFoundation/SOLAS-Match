{include file='header.tpl'}
<div class='page-header'>
    <h1>Badge List<small> All the badges availabe through SOLAS Match</small></h1>
</div>

{if isset($badgeList)}
    {foreach $badgeList as $badgeEntry}
        <h3>{$badgeEntry->getTitle()}</h3>
        <p>{$badgeEntry->getDescription()}</p>
        <p style="margin-bottom:20px;"></p>
    {/foreach}
{/if}


{include file='footer.tpl'}
