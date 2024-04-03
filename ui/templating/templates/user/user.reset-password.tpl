<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en" data-bs-theme="light">
    <head>
        <meta charset="utf-8" content="application/xhtml+xml" />

<!--        <meta name="google-translate-customization" content="d0b5975e5905d60f-4e4c167261d2937a-g4574d0ff41a34d5b-10" />-->

        <!-- css -->
        <title>{Settings::get('site.title')}</title>
        <meta name="description" content="{Settings::get('site.meta_desc')}" />
        <meta name="keywords" content="{Settings::get('site.meta_key')}" />
        
 

 
        
        
        <link href="{urlFor name="home"}ui/css/custom.css" rel="stylesheet" type="text/css">
        {* <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"> *}
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

      
   
        
        
		
        
        
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
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
     
 </head>

        <body {if isset($body_class)}class="{$body_class}"{/if} {if isset($body_id)}id="{$body_id}"{/if} >
        <div class="d-flex flex-column min-vh-100  bg-light-subtle">
        
        <nav data-bs-theme="light" id="nav" class="navbar navbar-expand-lg bg-body-tertiary shadow bg-secondary d-flex ">
        <div class="container py-2">
       

                 <a class="navbar-brand" href={urlFor name='home'}> <img  src="{urlFor name='home'}ui/img/TWB_Logo.svg" class="logo"> </a>
             
           
      
        </div>
        </nav>

        <main class="flex-grow-1 d-flex flex-column align-items-center  justify-content-center">

        <div class=" w-50l">

        {include file="handle-flash-messages.tpl"}
  

        
            <div class="page-header">
                <h1>{Localisation::getTranslation('user_reset_password_reset_user_password')}</h1>
            </div>

            <form class="well" action="{urlFor name="password-reset-request"}" method="post" accept-charset="utf-8">
                <p>
                    {Localisation::getTranslation('user_reset_password_0')}
                </p>
                <p>
                    {Localisation::getTranslation('user_reset_password_1')}
                </p>
                <label for="email" class="form-label">
                    <h2>
                        {Localisation::getTranslation('common_email')}
                    </h2>
                </label>
                <p><input type="text"  class="form-control" name="email_address" id="email_address" /></p>
                
                <div class="d-grid gap-2">
                <button type="submit" name="password_reset" class="btngray-lg">
                     {Localisation::getTranslation('user_reset_password_send_request')}
                </button>
                </div>

                    
            </form>
    </div>
    </main>
    

  </div>  


</body>

</html>
