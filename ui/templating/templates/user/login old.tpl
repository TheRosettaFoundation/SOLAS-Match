{include file="header.tpl"}

    <div class="page-header">
            <h1>{Localisation::getTranslation('login_log_in_to')} {Settings::get('site.name')}</h1>
    </div>

    {if isset($flash['error'])}
        <div class="alert alert-error">
            <a class="close" data-dismiss="alert" href="{urlFor name='login'}">×</a>
            <p>{TemplateHelper::uiCleanseHTMLKeepMarkup($flash['error'])}</p>
        </div>
    {/if}

    {if isset($flash['info'])}
        <div class="alert alert-info">
            <a class="close" data-dismiss="alert" href="{urlFor name='login'}">×</a>
            <p><strong>{Localisation::getTranslation('common_note')}: </strong>{TemplateHelper::uiCleanseHTMLKeepMarkup($flash['info'])}</p>
        </div>
    {/if}

    {if isset($flash['success'])}
        <div class="alert alert-success">
            <a class="close" data-dismiss="alert" href="{urlFor name='login'}">×</a>
            <p><strong>{Localisation::getTranslation('common_success')}! </strong>{TemplateHelper::uiCleanseHTMLKeepMarkup($flash['success'])}</p>
        </div>
    {/if}
<div class="row-fluid">
        <form method="post" action="{urlFor name='login'}" accept-charset="utf-8">
            <label for="email"><strong>{Localisation::getTranslation('common_email')}</strong></label>
            <input type="text" name="email" id="email"/>
            <label for="password"><strong>{Localisation::getTranslation('common_password')}</strong></label>
            <input type="password" name="password" id="password"/>
            <div>
                <button type="submit" name="login" class="btn btn-primary">
  				    <i class="icon-share icon-white"></i> {Localisation::getTranslation('common_log_in')}
				</button>
				
				<button type="submit" class="btn btn-inverse" name="password_reset">
  				    <i class="icon-exclamation-sign icon-white"></i> {Localisation::getTranslation('login_reset_password')}
				</button>
            </div>
        </form>

        <form action="{urlFor name='login'}" method="post">
            <input type="hidden" name="action" value="verify" />
            <fieldset>
                <legend><strong>or sign-in via Google</strong></legend>
                        <div id="gSignInWrapper" style="margin-bottom: 10px;">
                          <div id="g_id_onload"
                              data-client_id="{Settings::get('googlePlus.client_id')}"
                              data-context="signin"
                              data-ux_mode="popup"
                              data-login_uri="{Settings::get('site.location')}login/"
                              data-auto_prompt="false">
                          </div>
                          <div class="g_id_signin"
                              data-type="standard"
                              data-shape="rectangular"
                              data-theme="outline"
                              data-text="signin_with"
                              data-size="large"
                              data-width=219
                              data-logo_alignment="left">
                          </div>
                        </div>
            </fieldset>
        </form>
        </div>
{include file="footer.tpl"}
