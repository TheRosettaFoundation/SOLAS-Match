{include file="header.tpl"}

{include file="handle-flash-messages.tpl"}

    <div class="page-header">
        <h1>{Localisation::getTranslation(Strings::USER_RESET_PASSWORD_RESET_USER_PASSWORD)}</h1>
    </div>

    <form class="well" action="{urlFor name="password-reset-request"}" method="post" accept-charset="utf-8">
        <p>
            {Localisation::getTranslation(Strings::USER_RESET_PASSWORD_0)}
        </p>
        <p>
            {Localisation::getTranslation(Strings::USER_RESET_PASSWORD_1)}
        </p>
        <label for="email">
            <h2>
                {Localisation::getTranslation(Strings::COMMON_EMAIL)}:
            </h2>
        </label>
        <p><input type="text" name="email_address" id="email_address" /></p>
        <input type="submit" name="password_reset" value="    {Localisation::getTranslation(Strings::USER_RESET_PASSWORD_SEND_REQUEST)}" class="btn btn-primary"/>
        <i class="icon-share-alt icon-white" style="position:relative; right:116px; top:2px;"></i>    
    </form>

{include file="footer.tpl"}
