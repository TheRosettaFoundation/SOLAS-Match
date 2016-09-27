{if isset($flash['error'])}
    <div class="alert alert-error">
        <p><strong>{Localisation::getTranslation('common_error')}! </strong>{TemplateHelper::uiCleanseHTMLKeepMarkup($flash['error'])}</p>
    </div>
{/if}

{if isset($flash['info'])}
    <div class="alert alert-info">
        <p><strong>{Localisation::getTranslation('common_note')}! </strong>{TemplateHelper::uiCleanseHTMLKeepMarkup($flash['info'])}</p>
    </div>
{/if}

{if isset($flash['success'])}
    <div class="alert alert-success">
        <p><strong>{Localisation::getTranslation('common_success')}! </strong>{TemplateHelper::uiCleanseHTMLKeepMarkup($flash['success'])}</p>
    </div>
{/if}