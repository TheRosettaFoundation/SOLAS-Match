{if isset($flash['error'])}
    <div class="alert alert-error">
        <p><strong>{Localisation::getTranslation(Strings::COMMON_ERROR)}! </strong>{$flash['error']}</p>
    </div>
{/if}

{if isset($flash['info'])}
    <div class="alert alert-info">
        <p><strong>{Localisation::getTranslation(Strings::COMMON_NOTE)}! </strong>{$flash['info']}</p>
    </div>
{/if}

{if isset($flash['success'])}
    <div class="alert alert-success">
        <p><strong>{Localisation::getTranslation(Strings::COMMON_SUCCESS)}! </strong>{$flash['success']}</p>
    </div>
{/if}