<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en" data-bs-theme="light">
    <head>
        <meta charset="utf-8" content="application/xhtml+xml" />

<!--        <meta name="google-translate-customization" content="d0b5975e5905d60f-4e4c167261d2937a-g4574d0ff41a34d5b-10" />-->

        <!-- css -->
        <title>{Settings::get('site.title')}</title>
        <meta name="description" content="{Settings::get('site.meta_desc')}" />
        <meta name="keywords" content="{Settings::get('site.meta_key')}" />
        
        <!-- Open Graph data (Facebook and Google+) -->
        <meta property="og:title" content="{Settings::get('openGraph.title')}"/>
        <meta property="og:type" content="{Settings::get('openGraph.type')}" />
        <meta property="og:image" content="{Settings::get('openGraph.image')}"/>
        <meta property="og:site_name" content="{Settings::get('openGraph.site_name')}"/>
        <meta property="og:description" content="{Settings::get('openGraph.description')}"/>

        <!-- Twitter Card data -->
        <meta name="twitter:card" content="{Settings::get('twitter.card')}"/>
        <meta name="twitter:site" content="{Settings::get('twitter.site')}"/>
        <meta name="twitter:title" content="{Settings::get('twitter.title')}"/>
        <meta name="twitter:description" content="{Settings::get('twitter.description')}"/>
        <meta name="twitter:image" content="{Settings::get('twitter.image')}"/>
       
        <link href="{urlFor name="home"}ui/css/custom.css" rel="stylesheet" type="text/css">
      <link rel="shortcut icon" type="image/x-icon" href="{urlFor name="home"}ui/img/favicon/faviconM.png"> 
        <!-- 
        <link rel="shortcut icon" href="{urlFor name="home"}ui/img/favicon/favicon.ico" type="image/x-icon">
        <link rel="icon" href="{urlFor name="home"}ui/img/favicon/favicon.ico" type="image/x-icon">
        <link rel="apple-touch-icon" sizes="180x180" href="{urlFor name="home"}ui/img/favicon/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="{urlFor name="home"}ui/img/favicon/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="{urlFor name="home"}ui/img/favicon/favicon-16x16.png">
        <link rel="manifest" href="{urlFor name="home"}ui/img/favicon/site.webmanifest"> 
        -->
		
        <!-- extra styles-->
        {if isset($extra_styles)}
            {$extra_styles}
        {/if}

        <!-- style overrides-->
        

        {if isset($platformJS)}
            {$platformJS}
        {/if}
        <!-- javascript -->
        <script type="text/javascript" src="{urlFor name="home"}ui/js/lib/jquery-1.9.0.js"></script>
        <script type="text/javascript" src="{urlFor name="home"}ui/js/lib/jquery-ui.js"></script>

<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-3Z3VNH71D6"></script>



<script>
  window.dataLayer = window.dataLayer || [];
  function gtag() { dataLayer.push(arguments); }
  gtag('js', new Date());
  gtag('config', 'G-3Z3VNH71D6');

         
</script>







        <!-- extra Scripts -->
        {if isset($extra_scripts)}
            {$extra_scripts}
        {/if}
       

     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>  

     
    
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
</head>

<body {if isset($body_class)}class="{$body_class}"{/if} {if isset($body_id)}id="{$body_id}"{/if} class="d-flex align-items-center min-vh-100 " >
     

  <div class="container  flex-grow-1">
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
  

  
  <div class=" row py-2 d-flex justify-content-between  ">
  
      <div class=" col-12 col-md-6 py-4"> 
  
      <img src="{urlFor name='home'}ui/img/TWB_Logo.svg" class="mb-4" />
  
  
      <div class="mb-4 mt-2">
              <h3 class="fw-bold">Create an account with TWB</h3>
      </div>

      {if (empty($disabled))}
        <form method="post" id="registerform" action="{urlFor name="register"}" class="well" accept-charset="utf-8">
             <div class="d-flex justify-content-between">
                <div>
                <label for="first_name" class="required "><strong>First name</strong></label>
                <input type="text" name="first_name" id="first_name" placeholder="First name" class="form-control" {if isset($first_name)}value="{$first_name|escape:'html':'UTF-8'}"{/if} required/>
                </div>
                <div >

                <label for="last_name" class="required"><strong>Last name</strong></label>
              <input type="text" name="last_name" id="last_name" placeholder="Last name" class="form-control" {if isset($last_name)}value="{$last_name|escape:'html':'UTF-8'}"{/if} required/>
                
                </div>
             
             </div>
             
             
              <label for="email" class="required"><strong>Email</strong></label>
              <input type="text" name="email" id="email" class="form-control" placeholder="Your email" {if isset($email)}value="{$email|escape:'html':'UTF-8'}"{/if} />
              <label for="password" class="required"><strong>{Localisation::getTranslation('common_password')}</strong></label>
              <input type="password" name="password"  class="form-control" id="password" placeholder="{Localisation::getTranslation('register_your_password')}"/>
              <label for="password" class="required"><strong>Confirm Password</strong></label>
              <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Please confirm your password"/>
              <label class="checkbox required check">
              <input name="age_consent" id="age_consent" class="form-control" type="checkbox"> I confirm I am over the age of 18 <i class="icon-question-sign" id="tool" data-toggle="tooltip" title="If you are under 18 years of age, you can't volunteer with us. Our child protection policy prevents it"></i>
              </label>
              <label class="checkbox required check">
              <input name="conduct_consent" id="conduct_consent" class="form-control" type="checkbox"> I agree to the <a href="https://translatorswithoutborders.org/wp-content/uploads/2022/03/Plain-language-Code-of-Conduct-for-Translators.pdf" target="_blank">TWB Code of Conduct for Translators</a> and the <a href="https://translatorswithoutborders.org/privacy-policy/?__hstc=6552685.50947dd5d22eb95562a1c48227dc4cde.1624948951679.1624948951679.1624948951679.1&__hssc=6552685.1.1624948951679&__hsfp=1528584403" target="_blank">TWB Privacy Policy</a>
              </label>
              <label class="checkbox check">
              <input name="newsletter_consent" id="newsletter_consent" class="form-control" type="checkbox"> Subscribe to the TWB email newsletter.
              <small>You can unsubscribe at any time</small>
              </label>
              <input type="hidden" name="g-recaptcha-response" id="g_response">
              <p class="reg_btn ">
                  <button type="submit" class="btn btn-primary" name="submit">
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
                                data-context="signin"div>
                                </div>
                                {include file="footer.tpl"} *} *}
                                
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
      {/if}
  
          
           
  
            
  
  
          
  
      </div>
  
  
      <div class="col-12 col-md-6 py-4 flex-grow-1 "> 
  
  
      <img src="{urlFor name='home'}ui/img/login_register.svg" alt="login screen image"  class="img-fluid" />
           
          
  
      </div>
      
  
  
  
  </div>
  
  
  </div>
  
  
  
  </body>
  
  

{* <div class="container-fluid">
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
    {if (empty($disabled))}
      <form method="post" id="registerform" action="{urlFor name="register"}" class="well" accept-charset="utf-8">
            <label for="first_name" class="required "><strong>First name</strong></label>
            <input type="text" name="first_name" id="first_name" placeholder="First name" {if isset($first_name)}value="{$first_name|escape:'html':'UTF-8'}"{/if} required/>
            <label for="last_name" class="required"><strong>Last name</strong></label>
            <input type="text" name="last_name" id="last_name" placeholder="Last name" {if isset($last_name)}value="{$last_name|escape:'html':'UTF-8'}"{/if} required/>
            <label for="email" class="required"><strong>Email</strong></label>
            <input type="text" name="email" id="email" placeholder="Your email" {if isset($email)}value="{$email|escape:'html':'UTF-8'}"{/if} />
            <label for="password" class="required"><strong>{Localisation::getTranslation('common_password')}</strong></label>
            <input type="password" name="password" id="password" placeholder="{Localisation::getTranslation('register_your_password')}"/>
            <label for="password" class="required"><strong>Confirm Password</strong></label>
            <input type="password" name="confirm_password" id="confirm_password" placeholder="Please confirm your password"/>
            <label class="checkbox required check">
            <input name="age_consent" id="age_consent" type="checkbox"> I confirm I am over the age of 18 <i class="icon-question-sign" id="tool" data-toggle="tooltip" title="If you are under 18 years of age, you can't volunteer with us. Our child protection policy prevents it"></i>
            </label>
            <label class="checkbox required check">
            <input name="conduct_consent" id="conduct_consent" type="checkbox"> I agree to the <a href="https://translatorswithoutborders.org/wp-content/uploads/2022/03/Plain-language-Code-of-Conduct-for-Translators.pdf" target="_blank">TWB Code of Conduct for Translators</a> and the <a href="https://translatorswithoutborders.org/privacy-policy/?__hstc=6552685.50947dd5d22eb95562a1c48227dc4cde.1624948951679.1624948951679.1624948951679.1&__hssc=6552685.1.1624948951679&__hsfp=1528584403" target="_blank">TWB Privacy Policy</a>
            </label>
            <label class="checkbox check">
            <input name="newsletter_consent" id="newsletter_consent"  type="checkbox"> Subscribe to the TWB email newsletter.
            <small>You can unsubscribe at any time</small>
            </label>
            <input type="hidden" name="g-recaptcha-response" id="g_response">
            <p class="reg_btn ">
                <button type="submit" class="btn btn-primary" name="submit">
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
                              data-login_uri="{Settings::get('site.location')}login/"
                              data-auto_prompt="false">
                          </div>
                          <div class="g_id_signin"
                              data-type="standard"
                              data-shape="rectangular"
                              data-theme="outline"
                              data-text="signin_with"
                              data-size="large"
                              data-width=219<div class="container-fluid">
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
    {if (empty($disabled))}
      <form method="post" id="registerform" action="{urlFor name="register"}" class="well" accept-charset="utf-8">
            <label for="first_name" class="required "><strong>First name</strong></label>
            <input type="text" name="first_name" id="first_name" placeholder="First name" {if isset($first_name)}value="{$first_name|escape:'html':'UTF-8'}"{/if} required/>
            <label for="last_name" class="required"><strong>Last name</strong></label>
            <input type="text" name="last_name" id="last_name" placeholder="Last name" {if isset($last_name)}value="{$last_name|escape:'html':'UTF-8'}"{/if} required/>
            <label for="email" class="required"><strong>Email</strong></label>
            <input type="text" name="email" id="email" placeholder="Your email" {if isset($email)}value="{$email|escape:'html':'UTF-8'}"{/if} />
            <label for="password" class="required"><strong>{Localisation::getTranslation('common_password')}</strong></label>
            <input type="password" name="password" id="password" placeholder="{Localisation::getTranslation('register_your_password')}"/>
            <label for="password" class="required"><strong>Confirm Password</strong></label>
            <input type="password" name="confirm_password" id="confirm_password" placeholder="Please confirm your password"/>
            <label class="checkbox required check">
            <input name="age_consent" id="age_consent" type="checkbox"> I confirm I am over the age of 18 <i class="icon-question-sign" id="tool" data-toggle="tooltip" title="If you are under 18 years of age, you can't volunteer with us. Our child protection policy prevents it"></i>
            </label>
            <label class="checkbox required check">
            <input name="conduct_consent" id="conduct_consent" type="checkbox"> I agree to the <a href="https://translatorswithoutborders.org/wp-content/uploads/2022/03/Plain-language-Code-of-Conduct-for-Translators.pdf" target="_blank">TWB Code of Conduct for Translators</a> and the <a href="https://translatorswithoutborders.org/privacy-policy/?__hstc=6552685.50947dd5d22eb95562a1c48227dc4cde.1624948951679.1624948951679.1624948951679.1&__hssc=6552685.1.1624948951679&__hsfp=1528584403" target="_blank">TWB Privacy Policy</a>
            </label>
            <label class="checkbox check">
            <input name="newsletter_consent" id="newsletter_consent"  type="checkbox"> Subscribe to the TWB email newsletter.
            <small>You can unsubscribe at any time</small>
            </label>
            <input type="hidden" name="g-recaptcha-response" id="g_response">
            <p class="reg_btn ">
                <button type="submit" class="btn btn-primary" name="submit">
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
    {/if}
    </div>
  </div>
</div>
{include file="footer.tpl"} *}

                              {* data-logo_alignment="left">
                          </div>
                    </div>
    {/if}
    </div>
  </div>
</div>
{include file="footer.tpl"} *} 
