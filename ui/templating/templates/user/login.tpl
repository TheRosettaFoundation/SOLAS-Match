{include file="header.tpl"}

    <div class="page-header">
            <h1>{Localisation::getTranslation(Strings::LOGIN_LOG_IN_TO)} {Settings::get('site.name')}</h1>
    </div>

    {if isset($flash['error'])}
        <div class="alert alert-error">
            <a class="close" data-dismiss="alert" href="{urlFor name='login'}">×</a>
            <p><strong>{Localisation::getTranslation(Strings::COMMON_WARNING)}! </strong>{$flash['error']}</p>
        </div>
    {/if}

    {if isset($flash['info'])}
        <div class="alert alert-info">
            <a class="close" data-dismiss="alert" href="{urlFor name='login'}">×</a>
            <p><strong>{Localisation::getTranslation(Strings::COMMON_NOTE)}: </strong>{$flash['info']}</p>
        </div>
    {/if}

    {if isset($flash['success'])}
        <div class="alert alert-success">
            <a class="close" data-dismiss="alert" href="{urlFor name='login'}">×</a>
            <p><strong>{Localisation::getTranslation(Strings::COMMON_SUCCESS)}! </strong>{$flash['success']}</p>
        </div>
    {/if}

    {if isset($openid)&& ($openid==='n'||$openid==='h' )}
        <form method="post" action="{urlFor name='login'}" accept-charset="utf-8">
            <label for="email"><strong>{Localisation::getTranslation(Strings::COMMON_EMAIL)}</strong></label>
            <input type="text" name="email" id="email"/>
            <label for="password"><strong>{Localisation::getTranslation(Strings::COMMON_PASSWORD)}</strong></label>
            <input type="password" name="password" id="password"/>
            <p>
                <input type="submit" class="btn btn-primary" name="login" value="   {Localisation::getTranslation(Strings::COMMON_LOG_IN)}" />
                <input type="submit" class="btn btn-inverse" name="password_reset" value="   {Localisation::getTranslation(Strings::LOGIN_RESET_PASSWORD)}" />
                <i class="icon-share icon-white" style="position:relative; right:200px;top:2px;"></i>
                <i class="icon-exclamation-sign icon-white" style="position:relative; right:145px; top:2px;"></i>        
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
