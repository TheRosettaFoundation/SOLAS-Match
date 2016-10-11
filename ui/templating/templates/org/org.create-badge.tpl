{include file="header.tpl"}

    <h1 class="page-header">
        {Localisation::getTranslation('org_create_badge_create_organisation_badge')}
        <small>{Localisation::getTranslation('org_create_badge_0')}</small>
    </h1>

    {if isset($flash['error'])}
        <div class="alert alert-error">
            <p><strong>{Localisation::getTranslation('common_warning')} </strong>{TemplateHelper::uiCleanseHTMLKeepMarkup($flash['error'])}</p>
        </div>
    {/if}

    <form method="post" action="{urlFor name="org-create-badge" options="org_id.$org_id"}" class="well" accept-charset="utf-8">
        <label for='title'><strong>{Localisation::getTranslation('org_create_badge_badge_title')}</strong></label>
        <input type='text' name='title' id='title' />
        <label for="description"><strong>{Localisation::getTranslation('common_description')}</strong></label>
        <textarea name='description' cols='40' rows='5'></textarea>
        <p>
            <button type='submit' class='btn btn-success' name='submit'>
                <i class="icon-star icon-white"></i> {Localisation::getTranslation('org_create_badge_create_badge')}
            </button>
        </p>
        <input type="hidden" name="sesskey" value="{$sesskey}" />
    </form>

{include file="footer.tpl"}
