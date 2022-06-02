{include file="header.tpl"}

<h1 class="page-header">
    {Localisation::getTranslation('email_verification_email_verification')} <small>{Localisation::getTranslation('email_verification_0')}</small>
</h1>

<p>
    Welcome to the TWB Community. Once you click "Finish registration" below you will have access to the TWB Platform.
    {Localisation::getTranslation('email_verification_2')}
</p>

<form method='post' action="{urlFor name="email-verification" options="uuid.$uuid"}">
    <input type="submit" name="verify" class="btn btn-success" value="{Localisation::getTranslation('email_verification_finish_registration')}" />
</form>

{include file="footer.tpl"}
