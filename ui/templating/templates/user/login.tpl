<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en" >
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

<body {if isset($body_class)}class="{$body_class}"{/if} {if isset($body_id)}id="{$body_id}"{/if} class="d-flex align-items-center min-vh-100" >
     

<div class="container-fluid px-4  flex-grow-1">

 

<div class=" row py-2 d-flex justify-content-between  ">


    <div class=" col-12 col-md-6 py-4 d-flex flex-column align-items-center justify-content-center"> 


    <div class="w-75" >

   
    <a class="navbar-brand" href={urlFor name='home'}> <img src="{urlFor name='home'}ui/img/TWB_Logo.svg" class="mb-4" /> </a>

    {if isset($flash['error'])}
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <a class="btn-close" type="button" data-bs-dismiss="alert" href="{urlFor name='login'}"></a>
            <p>{TemplateHelper::uiCleanseHTMLKeepMarkup($flash['error'])}</p>
        </div>
    {/if}

    {if isset($flash['info'])}
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <a class="btn-close" type="button" data-bs-dismiss="alert" href="{urlFor name='login'}"></a>
            <p><strong>{Localisation::getTranslation('common_note')}: </strong>{TemplateHelper::uiCleanseHTMLKeepMarkup($flash['info'])}</p>
        </div>
    {/if}

    {if isset($flash['success'])}
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <a class="btn-close" type="button" data-bs-dismiss="alert" href="{urlFor name='login'}"></a>
            <p><strong>{Localisation::getTranslation('common_success')}! </strong>{TemplateHelper::uiCleanseHTMLKeepMarkup($flash['success'])}</p>
        </div>
    {/if}

      <h2 class="fw-bold mt-4 mb-4"> Login to TWB Platform</h2>

        <form action="{urlFor name='login'}" method="post" class="mt-4 mb-4" >
            <input type="hidden" name="action" value="verify" />
            <fieldset class="w-100">
                
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

         <div class="d-flex align-items-center mt-4 mb-4"><div class="flex-fill border-top border-1 border-dark-subtle " ></div><div class=" text-center mx-4 text-muted">OR CONTINUE WITH </div><div class=" flex-fill border-top border-1 border-dark-subtle" ></div> </div>

          <form method="post" action="{urlFor name='login'}" accept-charset="utf-8" class="mt-4">
           <div class="mb-2"> 
            <label for="email" class="form-label text-grayish "><strong>{Localisation::getTranslation('common_email')}</strong></label>
            <input type="text" name="email" id="email" class="form-control"/>
           
           </div>
             <div class="mb-3"> 
            <label for="password" class="form-label text-grayish " ><strong>{Localisation::getTranslation('common_password')}</strong></label>
            <input type="password" name="password" id="password" class="form-control"/>
            </div>
            <div class="text-end">
          
            <a class="text-grayish border-0 cursor-pointer mb-2 bg-transparent text-decoration-underline" href='{urlFor name="password-reset"}' name="password_reset"> Forgot password ? </a>
          

            </div>
              <div class="d-grid gap-2">
                <button type="submit" name="login" class="btngray-lg w-full text-center cursor-pointer">
  				     {Localisation::getTranslation('common_log_in')}
				</button>
                
                </div>
                <div class="fs-5 text-muted text-center mt-2 "> <a href='{urlFor name="register"}' class="link-grayish link-offset-2 link-offset-3-hover link-underline-grayish link-underline-opacity-0 link-underline-opacity-75-hover"> I don't have an account</a> </div>
           
				
			
        </form>

        </div>


        

    </div>


    <div class="col-12 col-md-6 py-4 flex-grow-1 "> 


    <img src="{urlFor name='home'}ui/img/login_register.svg" alt="login screen image"  class="img-fluid" />
         
        

    </div>
    



</div>


</div>



</body>
</html>

