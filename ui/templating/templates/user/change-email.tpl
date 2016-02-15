{include file="header.tpl"}

<div class="page-header">
    <h1>{Localisation::getTranslation('common_change_email')}</h1>
</div>

{include file="handle-flash-messages.tpl"}
{if isset($error)}
    <div class="alert alert-error">
        <strong>{Localisation::getTranslation('common_error')}:</strong> {$error}
    </div>
{/if}

{if isset($warning)}
    <div class="alert">
        <strong>{Localisation::getTranslation("common_warning")}:</strong> {$warning}
    </div>
{/if}

<form method="post" action="{urlFor name="change-email"}" class="well" accept-charset="utf-8">
    <label for="email"><strong>{Localisation::getTranslation('common_email')}</strong></label>
    <input type="text" name="email" id="email" placeholder="{Localisation::getTranslation('common_email')}"/>
    <p>
        <button type="submit" class="btn btn-success" name="submit">
            <i class="icon-star icon-white"></i> {Localisation::getTranslation('common_change_email')}
        </button>
    </p>
</form>

{include file="footer.tpl"}
