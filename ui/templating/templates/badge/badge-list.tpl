{include file='header.tpl'}

<div class='page-header'>
    <h1>
        {Localisation::getTranslation(Strings::BADGE_LIST_BADGE_LIST)}
        <small>
            {sprintf(Localisation::getTranslation(Strings::BADGE_LIST_ALL_AVAILABLE_BADGES_OF), $siteName)}
        </small>
    </h1>
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
            <h3>{sprintf(Localisation::getTranslation(Strings::BADGE_LIST_BADGE), $siteName)} {$badgeEntry->getTitle()}</h3>
        {/if}
        <p>{$badgeEntry->getDescription()}</p>
        <p style="margin-bottom:20px;"></p>
    {/foreach}
{/if}

{include file='footer.tpl'}
