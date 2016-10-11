{include file="header.tpl"}

    <h1 class="page-header">
        {TemplateHelper::uiCleanseHTML($badge->getTitle())}
        <small>{Localisation::getTranslation('org_edit_badge_edit_organisation_badge_details')}</small>
    </h1>

    {* {assign var="badge_id" value=$badge->getId()} *}
    <form method="post" action="{urlFor name="org-public-profile" options="org_id.$org_id"}" class="well" accept-charset="utf-8">
        <input type="hidden" name="badge_id" value="{$badge->getId()}" />    
        <label for='title'><strong>{Localisation::getTranslation('org_edit_badge_badge_title')}</strong></label>
        <input type='text' name='title' id='title'
        {if $badge->getTitle() != ''}
            value='{TemplateHelper::uiCleanseHTML($badge->getTitle())}'
        {else}
            placeholder='{Localisation::getTranslation('org_edit_badge_0')}'
        {/if} /> 

        <label for="description"><strong>{Localisation::getTranslation('common_description')}</strong></label>
        <textarea name='description' cols='40' rows='5' {if $badge->getDescription() == ''} placeholder="{Localisation::getTranslation('org_edit_badge_1')}" {/if}
        >{if $badge->getDescription() != ''}{TemplateHelper::uiCleanseHTML($badge->getDescription())}{/if}</textarea>

        <p>
            <button type='submit' class='btn btn-primary' name='submit'>
                <i class="icon-refresh icon-white"></i> {Localisation::getTranslation('org_edit_badge_update_badge')}
            </button>
        </p>
        <input type="hidden" name="sesskey" value="{$sesskey}" />
    </form>

{include file="footer.tpl"}
