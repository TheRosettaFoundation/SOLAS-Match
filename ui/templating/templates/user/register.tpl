{include file="header.tpl"}
<style>
  #registerform label {
  color:#143878 !important;
}
  #registerform .check {
  color:#333 !important;
}
#registerform label.error {
  width: auto;
}
#registerform .error{
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
    <h3><span class="register_header1">Register with Translators without Borders</span><br />
<span class="register_header2">using your email or Google account</span></h3>
    </div>
  </div>
</div>

{include file="handle-flash-messages.tpl"}
{if isset($error)}
    <div class="alert alert-error">
        <strong>{Localisation::getTranslation('common_error')}:</strong> {$error}
    </div>
{/if}

{if isset($warning)}
    <div class="alert">
        <strong>{Localisation::getTranslation("common_warning")}:</strong> {$warning}
    </div>
{/if}

<div class="container-fluid">
  <div class="row-fluid">
    <div class="span4">
    </div>
    <div class="span4">
      <form method="post" id="registerform" action="{urlFor name="register"}" class="well" accept-charset="utf-8">
            <label for="first_name" class="required "><strong>First name</strong></label>
            <input type="text" name="first_name" id="first_name" placeholder="First name" required/>
            <label for="last_name" class="required"><strong>Last name</strong></label>
            <input type="text" name="last_name" id="last_name" placeholder="Last name" required/>
            <label for="email" class="required"><strong>Email</strong></label>
            <input type="text" name="email" id="email" placeholder="Your email"/>
            <label for="email2" class="required"><strong>Email Confirmation</strong></label>
            <input type="text" name="email2"  id="email2" placeholder="Please confirm your email address"/>
            <label for="password" class="required"><strong>{Localisation::getTranslation('common_password')}</strong></label>
            <input type="password" name="password" id="password" placeholder="{Localisation::getTranslation('register_your_password')}"/>
            <label for="password" class="required"><strong>Confirm Password</strong></label>
            <input type="password" name="confirm_password" id="confirm_password" placeholder="Please confirm your password"/>
            <label class="checkbox required check">
            <input name="age_consent" id="age_consent" type="checkbox"> I confirm I am over the age of 18 <i class="icon-question-sign" id="tool" data-toggle="tooltip" title="If you are under 18 years of age, you can't volunteer with us. Our child protection policy prevents it"></i>
            </label>
            <label class="checkbox required check">
            <input name="conduct_consent" id="conduct_consent" type="checkbox"> I agree to the <a href="https://www.translatorswithoutborders.org/volunteer/volunteer-translators/translators-code-of-conduct/" target="_blank">TWB Code of Conduct for Translators</a> and the <a href="https://translatorswithoutborders.org/privacy-policy/?__hstc=6552685.50947dd5d22eb95562a1c48227dc4cde.1624948951679.1624948951679.1624948951679.1&__hssc=6552685.1.1624948951679&__hsfp=1528584403" target="_blank">TWB Privacy Policy</a>
            </label>
            <label class="checkbox check">
            <input name="newsletter_consent" id="newsletter_consent"  type="checkbox"> Subscribe to the TWB email newsletter.
            <small>You can unsubscribe at any time</small>
            </label>
            <input type="hidden" name="g-recaptcha-response" id="g_response">
            
            <p class="reg_btn ">
                <button type="submit"  class="btn btn-primary" name="submit" 
                data-sitekey="{Settings::get('google.captcha_site_key')}" 
                data-callback='onSubmit' 
                data-action='submit' >
                     {Localisation::getTranslation('common_register')}
                </button>
            </p>
            
      </form>

      <div style="width: 100%; height: 10px; border-bottom: 1px solid #F3F5F6; text-align: center">
      <span style="font-size: 14px; background-color: #F3F5F6; padding: 0 10px;">
        OR <!--Padding is optional-->
      </span>
      </div><br/><br/>
                    <div id="gSignInWrapper" style="margin-bottom: 10px;">
                          <div id="g_id_onload"
                              data-client_id="{Settings::get('googlePlus.client_id')}"
                              data-context="signin"
                              data-ux_mode="popup"
                              data-login_uri="{urlFor name='login'}"
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
    </div>
  </div>
</div>
{include file="footer.tpl"}
