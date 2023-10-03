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
            <label for="role"> Choose your role : </>
            <select name ="role ">
                <option value= "NGO ADMIN"> NGO ADMIN </option>
                <option value= "NGO PO"> NGO PO </option>
                <option value= "NGO Linguist"> NGO Linguist </option>
                <option value= "TWB Linguist"> TWB Linguist </option>

            
             <select />
            <label for="email"><strong>{Localisation::getTranslation('common_email')}</strong></label>
            <input type="text" name="email" id="email"/>
            
            <div>
                <button type="submit" name="login" class="btn btn-primary">
  				    <i class="icon-share icon-white"></i> {Localisation::getTranslation('common_log_in')}
				</button>
				
				<button type="submit" class="btn btn-inverse" name="password_reset">
  				    <i class="icon-exclamation-sign icon-white"></i> {Localisation::getTranslation('login_reset_password')}
				</button>
            </div>
        </form>

   
        </div>
{include file="footer.tpl"}
