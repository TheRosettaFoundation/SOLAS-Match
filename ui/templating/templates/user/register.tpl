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
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
     
     
     <style>
  #registerform label {
  color:#364f67 !important;
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
  color:#364f67 !important;
}
.register_header2 {
  text-align:left;
  color:#e8991c !important;
}
.span {
  text-align:center;
}
</style>
      
		
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
     

</head>

<body {if isset($body_class)}class="{$body_class}"{/if} {if isset($body_id)}id="{$body_id}"{/if} class="d-flex align-items-center min-vh-100 " >
     

  <div class="container-fluid px-4 flex-grow-1">
 

  <div class=" row py-2 d-flex justify-content-between  ">
  
      <div class=" col-12 col-md-6 py-4 d-flex flex-column align-items-center justify-content-center"> 

      <div class="w-75">
  
      <a class="navbar-brand" href={urlFor name='home'}> <img src="{urlFor name='home'}ui/img/TWB_Logo.svg" class="mb-4" /> </a>

      {include file="handle-flash-messages.tpl"}
      {if isset($error)}
          <div class="alert alert-danger">
              <strong>{Localisation::getTranslation('common_error')}:</strong> {$error}
          </div>
      {/if}
      
      {if isset($warning)}
          <div class="alert">
              <strong>{Localisation::getTranslation("common_warning")}:</strong> {$warning}
          </div>
      {/if}
  
  
      <div class="mb-4 mt-4">
              <h2 class="fw-bold">Create an account with TWB</h2>
      </div>

      
        <div id="gSignInWrapper"  class="mt-4 mb-4">
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
  <div class="d-flex align-items-center mt-4 mb-4"><div class="flex-fill border-top border-1 border-dark-subtle " ></div><div class=" text-center mx-4 text-muted">OR CONTINUE WITH </div><div class=" flex-fill border-top border-1 border-dark-subtle" ></div> </div>
      {if (empty($disabled))}
        <form method="post" id="registerform" action="{urlFor name="register"}" class="wel mt-3" accept-charset="utf-8">
             <div class="d-flex mb-2">
                <div class="me-5">
                <label for="first_name" class="required  mb-1 "><strong>First name</strong></label>
                <input type="text" name="first_name" id="first_name" placeholder="First name" class="form-control mb-2" {if isset($first_name)}value="{$first_name|escape:'html':'UTF-8'}"{/if} required/>
                </div>
                
                <div>

                <label for="last_name" class="required  mb-1"><strong>Last name</strong></label>
              <input type="text" name="last_name" id="last_name" placeholder="Last name" class="form-control mb-2" {if isset($last_name)}value="{$last_name|escape:'html':'UTF-8'}"{/if} required/>
                
                </div>
             
             </div>
             
              <div>
              <label for="email" class="required form-label mb-1"><strong>Email</strong></label>
            <input type="text" name="email" id="email" class="form-control mb-2" placeholder="Your email" {if isset($email)}value="{$email|escape:'html':'UTF-8'}"{/if} />
              </div>
             <div>
             <label for="password" class="required form-label mb-1"><strong>{Localisation::getTranslation('common_password')}</strong></label>
             <input type="password" name="password"  class="form-control mb-1" id="password" placeholder="{Localisation::getTranslation('register_your_password')}"/>
             </div>
             
             <div>
             <label for="password" class="required form-label  mb-1"><strong>Confirm Password</strong></label>
             <input type="password" name="confirm_password" id="confirm_password" class="form-control mb-1" placeholder="Please confirm your password"/>
             
             </div>
              <div class="mb-1">
              <label class="checkbox required check form-check-label mt-2">
              <input name="age_consent" id="age_consent" class="form-check-input " type="checkbox"> I confirm I am over the age of 18 <i class="fa-solid fa-circle-question" id="tool" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="If you are under 18 years of age, you can't volunteer with us. Our child protection policy prevents it"></i>

              </label>
              </div>
              <div class="mb-1">
               <label class="checkbox required check form-check-label  ">
              <input name="conduct_consent" id="conduct_consent" class="form-check-input " type="checkbox"/> <span class="ms-1">I agree to the <a href="https://translatorswithoutborders.org/wp-content/uploads/2022/03/Plain-language-Code-of-Conduct-for-Translators.pdf" class="custom-link" target="_blank">TWB Code of Conduct for Translators</a> and the <a href="https://translatorswithoutborders.org/privacy-policy/?__hstc=6552685.50947dd5d22eb95562a1c48227dc4cde.1624948951679.1624948951679.1624948951679.1&__hssc=6552685.1.1624948951679&__hsfp=1528584403" class="custom-link" target="_blank">TWB Privacy Policy</a> </span>
             
              </label>
              </div>
              <div class="mb-1">
              <label class="checkbox check form-check-label">
              <input name="newsletter_consent" id="newsletter_consent" class="form-check-input mb-3" type="checkbox"> Subscribe to the TWB email newsletter.
              <small>You can unsubscribe at any time</small>
              </label>
              </div>
              <input type="hidden" name="g-recaptcha-response" id="g_response">
              
              <div class="d-grid gap-2 reg_btn mt-3">
                  <button type="submit" class="btngray-lg w-full text-center cursor-pointer" name="submit">
                       {Localisation::getTranslation('common_register')}
                  </button>
              </div>
              <div class="fs-5 text-muted text-center mt-3 "> <a href='{urlFor name="login"}' class="link-grayish link-offset-2 link-offset-3-hover link-underline-grayish link-underline-opacity-0 link-underline-opacity-75-hover "> I already have an account</a> </div>
              
        </form>
  
        
      {/if}
  
          
           
  
            
  
  
      </div> 
  
      </div>
  
  
      <div class="col-12 col-md-6 py-4 flex-grow-1 "> 
  
  
      <img src="{urlFor name='home'}ui/img/login_register.svg" alt="login screen image"  class="img-fluid" />
           
          
  
      </div>
      
  
  
  
  </div>
  
  
  </div>
  
  
  
  </body>
  
  
</html>