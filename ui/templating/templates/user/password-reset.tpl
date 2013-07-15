{include file="header.tpl"}

    <h1 class="page-header"}>
        {Localisation::getTranslation(Strings::PASSWORD_RESET_PASSWORD_RESET)}
        <small>
            {Localisation::getTranslation(Strings::PASSWORD_RESET_RESET_YOUR_PASSWORD_HERE)}
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

    <form method="post" action="{urlFor name="password-reset" options="uid.$uid"}" class="well">
        <label for="nPassword">{Localisation::getTranslation(Strings::PASSWORD_RESET_NEW_PASSWORD)}</label>
        <input type="password" name="new_password" />

        <label for="cPassword">{Localisation::getTranslation(Strings::PASSWORD_RESET_CONFIRM_NEW_PASSWORD)}</label>
        <input type="password" name="confirmation_password" />

        <p>
            <input type="submit" class="btn btn-primary" value="    {Localisation::getTranslation(Strings::PASSWORD_RESET_CHANGE_PASSWORD)}"/>
            <i class="icon-check icon-white" style="position:relative; right:138px;top:2px;"></i>
        </p>
    </form>
    
{include file="footer.tpl"}