<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en" data-bs-theme="light">
    <head>
        <meta charset="utf-8" content="application/xhtml+xml" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

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

  <!-- Import Roboto font required by Google branding guidelines -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://gstatic.com" crossorigin>
  <link href="https://googleapis.com" rel="stylesheet">

  <style>
    /* The main link container styled as a button */
    .google-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      background-color: #ffffff;
      color: #1f1f1f;
      font-family: 'Roboto', sans-serif;
      font-weight: 500;
      font-size: 14px;
      line-height: 20px;
      letter-spacing: 0.25px;
      text-decoration: none;
      padding: 0 12px 0 12px;
      height: 40px;
      border: 1px solid #747775;
      border-radius: 4px;
      transition: background-color 0.218s, border-color 0.218s, box-shadow 0.218s;
      cursor: pointer;
      box-sizing: border-box;
    }

    /* Target the SVG icon wrapper */
    .google-btn .google-icon-wrapper {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 20px;
      height: 20px;
      margin-right: 12px;
    }

    /* Hover effect */
    .google-btn:hover {
      background-color: #f8fafc;
      border-color: #747775;
      box-shadow: 0 1px 2px 0 rgba(60,64,67,0.30), 0 1px 3px 1px rgba(60,64,67,0.15);
    }

    /* Focus/Keyboard accessibility states */
    .google-btn:focus {
      outline: none;
      background-color: #f1f3f4;
      border-color: #0b57d0;
    }

    /* Active/Pressed state */
    .google-btn:active {
      background-color: #e8eaed;
      box-shadow: none;
    }
  </style>

</head>

<body {if isset($body_class)}class="{$body_class}"{/if} {if isset($body_id)}id="{$body_id}"{/if} class="d-flex align-items-center min-vh-100 " >
     

  <div class="container-fluid px-4 flex-grow-1">
 

  <div class=" row py-2 d-flex justify-content-between  ">
  
      <div class=" col-12 col-md-6 py-4 d-flex flex-column align-items-center justify-content-center"> 

      <div class="w-75">
  
      <a class="navbar-brand" href={urlFor name='home'}> <img src="{urlFor name='home'}ui/img/TWB_Logo.svg" class="mb-4" /> </a>

      {include file="handle-flash-messages.tpl"}
  
      <div class="mb-4 mt-4">
              <h2 class="fw-bold">Create an account with TWB</h2>
      </div>

  <!-- The Button Link -->
  <a href="{$tarjimly}/api/mobile/v2/auth/sso/google?redirectTo={urlencode("{$siteLocation}login")}" class="google-btn">
    <div class="google-icon-wrapper">
      <!-- Official Google 'G' SVG Logo -->
      <svg xmlns="http://w3.org" viewBox="0 0 48 48" width="20px" height="20px">
        <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
        <path fill="#4285F4" d="M46.5 24c0-1.65-.15-3.22-.42-4.75H24v9.03h12.75c-.55 2.97-2.22 5.5-4.75 7.2l7.37 5.72C43.68 36.56 46.5 30.73 46.5 24z"/>
        <path fill="#FBBC05" d="M10.54 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.98-6.19z"/>
        <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.37-5.72c-2.11 1.41-4.81 2.3-8.52 2.3-6.26 0-11.57-4.22-13.46-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
      </svg>
    </div>
    <span>Sign in with Google</span>
  </a>

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
              <input name="age_consent" id="age_consent" class="form-check-input " type="checkbox"> I confirm I am over the age of 18 
              </label>
              <i class="fa-solid fa-circle-question" id="tool" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="If you are under 18 years of age, you can't volunteer with us. Our child protection policy prevents it"></i>
              </div>
              <div class="mb-1">
               <label class="checkbox  check form-check-label d-flex  ">
              <input name="conduct_consent" id="conduct_consent" class="form-check-input " type="checkbox"> <span class="ms-1 required">I agree to the <a href="https://twbplatform.org/user%202026%20Code%20of%20Conduct%20for%20Translators%20and%20Interpreters.pdf" class="custom-link" target="_blank">TWB Code of Conduct for Translators</a> and the <a href="https://translatorswithoutborders.org/privacy-policy/?__hstc=6552685.50947dd5d22eb95562a1c48227dc4cde.1624948951679.1624948951679.1624948951679.1&__hssc=6552685.1.1624948951679&__hsfp=1528584403" class="custom-link" target="_blank">TWB Privacy Policy</a> </span>
             
              </label>
              </div>
              <div class="mb-1">
              <label class="checkbox check form-check-label d-flex ">
              <input name="newsletter_consent" id="newsletter_consent" class="form-check-input " type="checkbox"> <span class="ms-1"> Subscribe to the TWB email newsletter.
              <small >You can unsubscribe at any time</small></span>
              </label>
              </div>
              <input type="hidden" name="g-recaptcha-response" id="g_response">
              
              <div class="d-grid gap-2 reg_btn mt-3">
                  <button type="submit" class="btngray-lg w-full text-center cursor-pointer" name="submit">
                       {Localisation::getTranslation('common_register')}
                  </button>
              </div>
              <div class="fs-5 text-muted text-center mt-2 "> <a href='{urlFor name="login"}' class="link-grayish link-offset-2 link-offset-3-hover link-underline-grayish link-underline-opacity-0 link-underline-opacity-75-hover "> I already have an account</a> </div>
              
        </form>
  
        
      {/if}
  
          
           
  
            
  
  
      </div> 
  
      </div>
  
  
      <div class="col-12 col-md-6 py-4 flex-grow-1 "> 
  
  
      <img src="{urlFor name='home'}ui/img/login_register.svg" alt="login screen image"  class="img-fluid" />
           
          
  
      </div>
      
  
  
  
  </div>
  
  
  
  </body>
  
  
</html>