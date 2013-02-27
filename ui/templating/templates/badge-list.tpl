{include file='header.tpl'}

<div class='page-header'>
    <h1>Badge List<small> All the badges availabe through SOLAS Match</small></h1>
</div>

{if isset($badgeList)}
    {foreach $badgeList as $badgeEntry}
        {if !is_null($badgeEntry->getOwnerId())}
            <h3>
                {assign var="org_id" value=$badgeEntry->getOwnerId()}
                <a href="{urlFor name="org-public-profile" options="org_id.$org_id"}">
                    {$org_list[$org_id]->getName()}
                </a>: {$badgeEntry->getTitle()}
            </h3>
        {else}
            <h3>SOLAS Badge: {$badgeEntry->getTitle()}</h3>
        {/if}
        <p>{$badgeEntry->getDescription()}</p>
        <p style="margin-bottom:20px;"></p>
    {/foreach}
{/if}

{include file='footer.tpl'}
