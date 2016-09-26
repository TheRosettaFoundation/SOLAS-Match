{include file='header.tpl'}

<div class='page-header'>
    <h1>
        {Localisation::getTranslation('badge_list_badge_list')}
        <small>
            {sprintf(Localisation::getTranslation('badge_list_all_available_badges_of'), $siteName)}
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
                </a>: {TemplateHelper::uiCleanseHTML($badgeEntry->getTitle())}
            </h3>
            <p>{TemplateHelper::uiCleanseHTML($badgeEntry->getDescription())}</p>
        {else}
            <h3>{sprintf(Localisation::getTranslation('badge_list_badge'), $siteName)} {TemplateHelper::uiCleanseHTML(Localisation::getTranslation($badgeEntry->getTitle()))}</h3>
            <p>{TemplateHelper::uiCleanseHTML(Localisation::getTranslation($badgeEntry->getDescription()))}</p>
        {/if}
        <p style="margin-bottom:20px;"></p>
    {/foreach}
{/if}

{include file='footer.tpl'}
