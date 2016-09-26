{if isset($flash['error'])}
    <div class="alert alert-error">
        <p><strong>{Localisation::getTranslation('common_error')}! </strong>{TemplateHelper::uiCleanseHTML($flash['error'])}</p>
    </div>
{/if}

{if isset($flash['info'])}
    <div class="alert alert-info">
        <p><strong>{Localisation::getTranslation('common_note')}! </strong>{TemplateHelper::uiCleanseHTML($flash['info'])}</p>
    </div>
{/if}

{if isset($flash['success'])}
    <div class="alert alert-success">
        <p><strong>{Localisation::getTranslation('common_success')}! </strong>{TemplateHelper::uiCleanseHTML($flash['success'])}</p>
    </div>
{/if}