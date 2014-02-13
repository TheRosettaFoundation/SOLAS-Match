{include file="header.tpl"}

{include file="handle-flash-messages.tpl"}

    <div class="page-header">
        <h1>{Localisation::getTranslation('user_reset_password_reset_user_password')}</h1>
    </div>

    <form class="well" action="{urlFor name="password-reset-request"}" method="post" accept-charset="utf-8">
        <p>
            {Localisation::getTranslation('user_reset_password_0')}
        </p>
        <p>
            {Localisation::getTranslation('user_reset_password_1')}
        </p>
        <label for="email">
            <h2>
                {Localisation::getTranslation('common_email')}
            </h2>
        </label>
        <p><input type="text" name="email_address" id="email_address" /></p>
        <input type="submit" name="password_reset" value="    {Localisation::getTranslation('user_reset_password_send_request')}" class="btn btn-primary"/>
        <i class="icon-share-alt icon-white" style="position:relative; right:116px; top:2px;"></i>    
    </form>

{include file="footer.tpl"}
