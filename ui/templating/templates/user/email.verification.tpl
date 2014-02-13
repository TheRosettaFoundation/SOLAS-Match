{include file="header.tpl"}

<h1 class="page-header">
    {Localisation::getTranslation('email_verification_email_verification')} <small>{Localisation::getTranslation('email_verification_0')}</small>
</h1>

<p>
    {Localisation::getTranslation('email_verification_welcome_to')} {Settings::get('site.name')} {Localisation::getTranslation('email_verification_1')}
    {Localisation::getTranslation('email_verification_2')} {Localisation::getTranslation('email_verification_3')} {Localisation::getTranslation('email_verification_4')}
</p>

<p>
    {Localisation::getTranslation('email_verification_5')} {Localisation::getTranslation('email_verification_6')}
</p>

<form method='post' action="{urlFor name="email-verification" options="uuid.$uuid"}">
    <input type="submit" name="verify" class="btn btn-success" value="{Localisation::getTranslation('email_verification_finish_registration')}" />
</form>

{include file="footer.tpl"}
