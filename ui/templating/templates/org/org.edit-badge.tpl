{include file="header.tpl"}

    <h1 class="page-header">
        {$badge->getTitle()}
        <small>{Localisation::getTranslation(Strings::ORG_EDIT_BADGE_EDIT_ORGANISATION_BADGE_DETAILS)}</small>
    </h1>

    {* {assign var="badge_id" value=$badge->getId()} *}
    <form method="post" action="{urlFor name="org-public-profile" options="org_id.$org_id"}" class="well">
        <input type="hidden" name="badge_id" value="{$badge->getId()}" />    
        <label for='title'><strong>{Localisation::getTranslation(Strings::ORG_EDIT_BADGE_BADGE_TITLE)}:</strong></label>
        <input type='text' name='title' id='title'
        {if $badge->getTitle() != ''}
            value='{$badge->getTitle()}'
        {else}
            placeholder='{Localisation::getTranslation(Strings::ORG_EDIT_BADGE_0)}'
        {/if} /> 

        <label for="description"><strong>{Localisation::getTranslation(Strings::COMMON_DESCRIPTION)}:</strong></label>
        <textarea name='description' cols='40' rows='5' {if $badge->getDescription() == ''} placeholder="{Localisation::getTranslation(Strings::ORG_EDIT_BADGE_1)}" {/if}
        >{if $badge->getDescription() != ''}{$badge->getDescription()}{/if}</textarea>

        <p>
            <button type='submit' class='btn btn-primary' name='submit'>
                <i class="icon-refresh icon-white"></i> {Localisation::getTranslation(Strings::ORG_EDIT_BADGE_UPDATE_BADGE)}
            </button>
        </p>
    </form>

{include file="footer.tpl"}
