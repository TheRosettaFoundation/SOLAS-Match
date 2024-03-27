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

<script>
            var task_types = [0,
            {foreach from=TaskTypeEnum::$enum_to_UI key=task_type item=ui}
                {$ui['type_enum']},
            {/foreach}
            ]; 



            var source_and_target = [
                {foreach from=TaskTypeEnum::$enum_to_UI key=task_type item=ui}
                    {$ui['source_and_target']},
                {/foreach}
            ];

    

            var colours = ["",
                {foreach from=TaskTypeEnum::$enum_to_UI key=task_type item=ui}
                    "{$ui['colour']}",
                {/foreach}
            ];
            var unit_count_text_shorts = ["",
                {foreach from=TaskTypeEnum::$enum_to_UI key=task_type item=ui}
                    "{$ui['unit_count_text_short']}",
                {/foreach}
            ];

             var type_texts = ["",
                {foreach from=TaskTypeEnum::$enum_to_UI key=task_type item=ui}
                    "{$ui['type_text']}",
                {/foreach}
            ];
        
        
</script>






        <!-- extra Scripts -->
        {if isset($extra_scripts)}
            {$extra_scripts}
        {/if}
       

     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>  

     
    </head>

        <body {if isset($body_class)}class="{$body_class}"{/if} {if isset($body_id)}id="{$body_id}"{/if} >
     
        


<div class=""container-fluid>

<div class="container">

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


<div class=" row  py-2">

    <div class=" col-12 col-md-6 py-4"> 


    <div class="mb-4">
            <h1>{Localisation::getTranslation('login_log_in_to')} {Settings::get('site.name')}</h1>
    </div>

          
        <form action="{urlFor name='login'}" method="post">
            <input type="hidden" name="action" value="verify" />
            <fieldset>
                <legend><strong> sign-in via Google</strong></legend>
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
         <div class="fw-bol d-flex items-center justify-content-between "><hr /> <div>OR CONTINUE WITH</div> <hr/> </div>

          <form method="post" action="{urlFor name='login'}" accept-charset="utf-8" class="mt-4">
           <div class="mb-2"> 
            <label for="email" class="form-label"><strong>{Localisation::getTranslation('common_email')}</strong></label>
            <input type="text" name="email" id="email" class="form-control"/>
           
           </div>
             <div class="mb-3"> 
            <label for="password" class="form-label"><strong>{Localisation::getTranslation('common_password')}</strong></label>
            <input type="password" name="password" id="password" class="form-control"/>
            </div>
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


    <div class="col-12 col-md-6 py-4"> 


    <img src="{urlFor name='home'}ui/img/login_register.svg" alt="login screen image"  class=" text-center w-auto" />
         
        

    </div>
    



</div>

</div>

</div>    

</body>

