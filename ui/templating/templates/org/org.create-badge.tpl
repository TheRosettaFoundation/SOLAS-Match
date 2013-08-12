{include file="header.tpl"}

    <h1 class="page-header">
        {Localisation::getTranslation(Strings::ORG_CREATE_BADGE_CREATE_ORGANISATION_BADGE)}
        <small>{Localisation::getTranslation(Strings::ORG_CREATE_BADGE_0)}</small>
    </h1>

    {if isset($flash['error'])}
        <div class="alert alert-error">
            <p><strong>{Localisation::getTranslation(Strings::COMMON_WARNING)}! </strong>{$flash['error']}</p>
        </div>
    {/if}

    <form method="post" action="{urlFor name="org-create-badge" options="org_id.$org_id"}" class="well" accept-charset="utf-8">
        <label for='title'><strong>{Localisation::getTranslation(Strings::ORG_CREATE_BADGE_BADGE_TITLE)}:</strong></label>
        <input type='text' name='title' id='title' />
        <label for="description"><strong>{Localisation::getTranslation(Strings::COMMON_DESCRIPTION)}:</strong></label>
        <textarea name='description' cols='40' rows='5'></textarea>
        <p>
            <button type='submit' class='btn btn-success' name='submit'>
                <i class="icon-star icon-white"></i> {Localisation::getTranslation(Strings::ORG_CREATE_BADGE_0)}
            </button>
        </p>
    </form>

{include file="footer.tpl"}
