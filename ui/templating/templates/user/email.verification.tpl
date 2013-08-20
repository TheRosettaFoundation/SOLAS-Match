{include file="header.tpl"}

<h1 class="page-header">
    {Localisation::getTranslation(Strings::EMAIL_VERIFICATION_EMAIL_VERIFICATION)} <small>{Localisation::getTranslation(Strings::EMAIL_VERIFICATION_0)}</small>
</h1>

<p>
    {Localisation::getTranslation(Strings::EMAIL_VERIFICATION_WELCOME_TO)} {Settings::get('site.title')}. {Localisation::getTranslation(Strings::EMAIL_VERIFICATION_1)}
    {Localisation::getTranslation(Strings::EMAIL_VERIFICATION_2)} {Localisation::getTranslation(Strings::EMAIL_VERIFICATION_3)} {Localisation::getTranslation(Strings::EMAIL_VERIFICATION_4)}
</p>

<p>
    {Localisation::getTranslation(Strings::EMAIL_VERIFICATION_5)} {Localisation::getTranslation(Strings::EMAIL_VERIFICATION_6)}
</p>

<form method='post' action="{urlFor name="email-verification" options="uuid.$uuid"}">
    <input type="submit" name="verify" class="btn btn-success" value="{Localisation::getTranslation(Strings::EMAIL_VERIFICATION_FINISH_REGISTRATION)}" />
</form>

{include file="footer.tpl"}
