{include file="header.tpl"}

<div class="page-header">
    <h1>{Localisation::getTranslation(Strings::REGISTER_REGISTER_ON)} {Settings::get('site.name')}</h1>
</div>

{include file="handle-flash-messages.tpl"}
{if isset($error)}
    <div class="alert alert-error">
            <strong>Error</strong> {$error}
    </div>
{/if}

{if isset($warning)}
    <div class="alert">
            <strong>Warning</strong> {$warning}
    </div>
{/if}

{if isset($openid)&& ($openid==='n'||$openid==='h' )}
    <form method="post" action="{urlFor name="register"}" class="well" accept-charset="utf-8">
            <label for="email"><strong>{Localisation::getTranslation(Strings::COMMON_EMAIL)}</strong></label>
            <input type="text" name="email" id="email" placeholder="{Localisation::getTranslation(Strings::REGISTER_YOUR_EMAIL)}"/>
            <label for="password"><strong>{Localisation::getTranslation(Strings::COMMON_PASSWORD)}</strong></label>
            <input type="password" name="password" id="password" placeholder="{Localisation::getTranslation(Strings::REGISTER_YOUR_PASSWORD)}"/>
            <p>
                <button type="submit" class="btn btn-success" name="submit">
                    <i class="icon-star icon-white"></i> {Localisation::getTranslation(Strings::COMMON_REGISTER)}
                </button>
            </p>
    </form>
{/if}      

{if isset($openid)&& ($openid==='y'||$openid==='h' )}
    <!-- Simple OpenID Selector -->
    <form action="{urlFor name='login'}" method="post" id="openid_form">
        <input type="hidden" name="action" value="verify" />
        <fieldset>
            <legend>{Localisation::getTranslation(Strings::COMMON_SIGNIN_OR_CREATE_NEW_ACCOUNT)}</legend>
            <div id="openid_choice">
                    <p>{Localisation::getTranslation(Strings::COMMON_PLEASE_CLICK_YOUR_ACCOUNT_PROVIDER)}</p>
                    <div id="openid_btns"></div>
            </div>
            <div id="openid_input_area">
                    <input id="openid_identifier" name="openid_identifier" type="text" />
                    <input id="openid_submit" type="submit" class="btn btn-primary" value="{Localisation::getTranslation(Strings::LOGIN_SIGNIN)}"/>
            </div>
            <noscript>
                 <p>
                    {Localisation::getTranslation(Strings::COMMON_OPENID_IS_SERVICE_THAT_ALLOWS_YOU_TO_LOGON_TO_MANY_DIFFERENT_WEBSITES_USING_A_SINGLE_INDENTITY)}
                    {sprintf(Localisation::getTranslation(Strings::LOGIN_0), "http://openid.net/what/", "http://openid.net/get/")} 
                </p>
            </noscript>
        </fieldset>
    </form>
    <!-- /Simple OpenID Selector -->
{/if}

{include file="footer.tpl"}
