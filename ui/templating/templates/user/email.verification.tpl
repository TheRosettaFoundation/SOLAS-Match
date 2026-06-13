{include file="header.tpl"}

<div class="container">
{if isset($flash['error'])}
    <div class="alert alert-danger alert-dismissible fade show mt-4">
        <p><strong>{Localisation::getTranslation('common_warning')}! </strong>{TemplateHelper::uiCleanseHTMLKeepMarkup($flash['error'])}</p>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
{/if}
</div>

<h1 class="page-header">
    {Localisation::getTranslation('email_verification_email_verification')} <small>{Localisation::getTranslation('email_verification_0')}</small>
</h1>

<p>
    Welcome to the TWB Community. Once you click "Finish Registration" below you will have access to the TWB Platform.
    {Localisation::getTranslation('email_verification_2')}
</p>

<form method='post' action="{urlFor name="email-verification" options="uuid.$uuid"}">
    <input type="submit" name="verify" class="btn btn-success" value="{Localisation::getTranslation('email_verification_finish_registration')}" />
</form>

{include file="footer.tpl"}
