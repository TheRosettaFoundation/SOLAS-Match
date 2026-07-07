{include file="header.tpl"}
<style>
 #gregisterform label {
  color:#143878 !important;
}
 #gregisterform .check {
  color:#333 !important;
}
#gregisterform label.error {
  width: auto;
}
#gregisterform .error {
  color:#F00 !important;
}
.required:after {
  content:" *";
  color: red;
}
.center {
  margin: 0;
  position: absolute;
  top: 50%;
  left: 50%;
  -ms-transform: translate(-50%, -50%);
  transform: translate(-50%, -50%);
}
.register_header1 {
  text-align:right;
  color:#143878 !important;
}
.register_header2 {
  text-align:left;
  color:#e8991c !important;
}
.span {
  text-align:center;
}
</style>
<div class="container-fluid">
  <div class="row-fluid">
  <br/>
    <div class="span4">
    </div>
    <div class="span">
<h1><span class="register_header2">{Localisation::getTranslation('ff_welcome_word')} {$firstname|escape:'html':'UTF-8'}</span></h1>
    </div>
  </div>
</div>

{include file="handle-flash-messages.tpl"}
{if isset($error)}
    <div class="alert alert-error">
        <strong>{Localisation::getTranslation('ff_error')}:</strong> {$error}
    </div>
{/if}

{if isset($warning)}
    <div class="alert">
        <strong>{Localisation::getTranslation("ff_warning")}:</strong> {$warning}
    </div>
{/if}

<div class="container-fluid">
  <div class="row-fluid">
    <div class="span4">
    </div>
    <div class="span4">
    
    <form method="post" id="gregisterform" action="{urlFor name="googleregister" options="user_id.$user_id"}" class="well" accept-charset="utf-8">
            <label for="first_name" class="required "><strong>{Localisation::getTranslation('ff_first')}</strong></label>
            <input type="text" name="first_name" id="first_name" placeholder="{Localisation::getTranslation('ff_first')}" value="{$firstname|escape:'html':'UTF-8'}" required/>

            <label for="last_name" class="required"><strong>{Localisation::getTranslation('ff_last')}</strong></label>
            <input type="text" name="last_name" id="last_name" placeholder="{Localisation::getTranslation('ff_last')}" value="{$lastname|escape:'html':'UTF-8'}" required/>

            <label class="checkbox required check">
            <input name="age_consent" id="age_consent" type="checkbox"> {Localisation::getTranslation('ff_i_confirm')} <i class="icon-question-sign" id="tool" data-toggle="tooltip" title="{Localisation::getTranslation('ff_if_18')}"></i>
            </label>
           
            <label class="checkbox required check">
            <input name="conduct_consent" id="conduct_consent" type="checkbox"> {Localisation::getTranslation('ff_i_agree')}
            </label>

            <label class="checkbox check">
            <input name="newsletter_consent" id="newsletter_consent" type="checkbox"> {Localisation::getTranslation('ff_subscribe')}
            <small>{Localisation::getTranslation('ff_you_can_un')}</small>
            </label>

            <p class="reg_btn ">
                <button type="submit" class="btn btn-primary " name="submit">
                     {Localisation::getTranslation('ff_create_acc')}
                </button>
            </p>
            <input type="hidden" name="sesskey" value="{$sesskey}" />
    </form>
    </div>
  </div>
</div>
{include file="footer.tpl"}
