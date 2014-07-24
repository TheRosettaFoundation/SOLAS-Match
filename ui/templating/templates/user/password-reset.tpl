{include file="header.tpl"}

    <h1 class="page-header"}>
        {Localisation::getTranslation('password_reset_password_reset')}
        <small>
            {Localisation::getTranslation('password_reset_reset_your_password_here')}
        </small>
    </h1>

{if isset($flash['error'])}
    <div class="alert alert-error">
        <a class="close" data-dismiss="alert" href="{urlFor name='login'}">×</a>
        <p><strong>Warning! </strong>{$flash['error']}</p>
    </div>
{/if}

{if isset($flash['info'])}
    <div class="alert alert-info">
        <a class="close" data-dismiss="alert" href="{urlFor name='login'}">×</a>
        <p><strong>NOTE: </strong>{$flash['info']}</p>
    </div>
{/if}

{if isset($flash['success'])}
    <div class="alert alert-success">
        <a class="close" data-dismiss="alert" href="{urlFor name='login'}">×</a>
        <p><strong>Success! </strong>{$flash['success']}</p>
    </div>
{/if}

    <form method="post" action="{urlFor name="password-reset" options="uid.$uid"}" class="well" accept-charset="utf-8">
        <label for="nPassword">{Localisation::getTranslation('password_reset_new_password')}</label>
        <input type="password" name="new_password" />

        <label for="cPassword">{Localisation::getTranslation('password_reset_confirm_new_password')}</label>
        <input type="password" name="confirmation_password" />

        <div>
            <button type="submit" class="btn btn-primary">
  			    <i class="icon-share icon-white"></i> {Localisation::getTranslation('password_reset_change_password')}
			</button>
        </div>
    </form>
    
{include file="footer.tpl"}