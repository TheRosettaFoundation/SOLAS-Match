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
    
   
    </head>

        <body {if isset($body_class)}class="{$body_class}"{/if} {if isset($body_id)}id="{$body_id}"{/if}>
        
        <nav data-bs-theme="light" id="nav" class="navbar navbar-expand-lg bg-body-tertiary fw-bold">
        <div class="container-fluid">
            <a class="navbar-brand" href={urlFor name='home'}"> <img height="60px" style="margin-right: 25px;"  src="{urlFor name='home'}ui/img/TWB_logo1.PNG"> </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 ">
                {if !isset($admin)}
                    <li class="nav-item">
                    <a href="{urlFor name="home"}" class="nav-link" {if isset($current_page) && $current_page == 'home'} class="active"{/if}>{Localisation::getTranslation('header_home')}</a>
                    </li>
                {/if} 

                {if !isset($dashboard)}
                    <li class="nav-item">
                        <a href="{urlFor name="org-dashboard"}" class="nav-link" {if isset($current_page) && $current_page == 'home'} class="active"{/if} >{Localisation::getTranslation('header_dashboard')}</a>
                    </li>
                {/if} 

            
                {if isset($user_has_active_tasks)}
                    {assign var="tmp_id" value=$user->getId()}
                    <li class="nav-item" >
                        <a href="{urlFor name="claimed-tasks" options="user_id.$tmp_id"}" class="nav-link"  {if isset($current_page) && $current_page == 'claimed-tasks'} class="nav-link active" {/if}>{Localisation::getTranslation('header_claimed_tasks')}</a>
                    </li>
                {/if} 

                {if isset($user)}
                {assign var="user_id" value=$user->getId()}
                    <li class="nav-item" {if isset($current_page) && $current_page == 'user-profile'}class="active" {/if} >
                        <a href="{urlFor name="user-public-profile" options="user_id.$user_id"}" class="nav-link">{Localisation::getTranslation('header_profile')}</a>
                    </li>
                {/if} 

                {if isset($show_admin_dashboard)}
                {assign var="user_id" value=$user->getId()}
                    <li class="nav-item" {if isset($current_page) && $current_page == 'site-admin-dashboard'}class="active" {/if}>
                        <a href="{urlFor name="site-admin-dashboard" options="user_id.$user_id" class="nav-link"}">{Localisation::getTranslation('header_admin')}</a>
                    </li>
                {/if} 

                 {if !isset($site_admin)}
                             <li {if isset($current_page) && $current_page == 'faq'}class="active" {/if} class="nav-item">
                                <a href="https://community.translatorswb.org/t/the-translators-toolkit/3138" target="_blank" class="nav-link">{Localisation::getTranslation('common_faq')}</a>
                            </li>
                        {/if}
                           	{if Settings::get('site.forum_enabled') == 'y'}
	                            <li>
	                                <a href="{Settings::get('site.forum_link')}" target="_blank" class="nav-link">{Localisation::getTranslation('common_forum')}</a>
	                            </li>
                            {/if}
                {if isset($site_admin)}
                            <li class="nav-item">
                                <a href="https://analytics.translatorswb.org" target="_blank" class="nav-link">Analytics</a>
                            </li>
                        {/if}
                 {if !isset($site_admin)}
                            <li class="nav-item">
                                {if isset($user)}
                                <a href="https://elearn.translatorswb.org/auth/saml2/login.php?wants&idp=bd3eb3e6241260ee537b9a55145d852d&passive=off" target="_blank" class="nav-link">TWB Learning Center</a>
                                {else}
                                <a href="https://elearn.translatorswb.org/" target="_blank" class="nav-link">TWB Learning Center</a>
                                {/if}
                            </li>
                            <li class="nav-item">
                                <a href="https://form.asana.com?k=dlsF11XkOwpfFllbq325dg&d=170818793545926" target="_blank" class="nav-link">Feedback?</a>
                            </li>
                        {else}
                            <li class="nav-item">
                                <a href="https://elearn.translatorswb.org/auth/saml2/login.php?wants&idp=bd3eb3e6241260ee537b9a55145d852d&passive=off" target="_blank" class="nav-link">Learn. Center</a>
                            </li>
                        {/if}
            

            </ul>

             <ul class="navbar-nav flex-row flex-wrap ms:md-auto d-flex d-flex align-items-center">
                        {if isset($userNotifications)}   
                            <li class="nav-item">
                                <a  class="nav-link">{Localisation::getTranslation('header_notifications')}<span id="notificationCount">{$userNotifications->lenght()}</span></a>
                            </li>
                        {/if}
                        {if isset($user)}
                            <li class="profile nav-item">

                                <a href="{urlFor name="user-public-profile" options="user_id.$user_id"}"  class="nav-link">
                                    <img src="https://www.gravatar.com/avatar/{md5( strtolower( trim($user->getEmail())))}?s=20{urlencode("&")}r=g" alt="" />
                                       {TemplateHelper::uiCleanseHTML($user->getDisplayName())}
                                </a>
                            </li>
                            <li class="logout nav-item" >
                                <a href="{urlFor name="logout"}" class="nav-link">{Localisation::getTranslation('header_log_out')}</a>
                            </li>
                            <li class="nav_item" id="theme">
                               <img src="{urlFor name='home'}ui/img/light.svg"   alt="theme button" id="light">
                           
                               <img src="{urlFor name='home'}ui/img/night.svg" class="d-none" alt="theme button" id="night">
                            </li>
                        {else}
                          
                            <li class="nav-item"><a href="{urlFor name="register"}" class="nav-link">Join</a></li>
                            <li class="nav-item"><a href="{urlFor name="login"}" class="nav-link">{Localisation::getTranslation('common_log_in')}</a></li>
                        {/if}
                    </ul>
            
            </div>
        </div>
        </nav>

        
        <div class="container">
        
        {assign var="home_page" value="{urlFor name="home"}"}
        
        {if ((Settings::get('banner.enabled') == 'y') and (isset($user) or ($smarty.server.REQUEST_URI!=$home_page)))}
		    <div id="banner-container">
		    <a href = "{Settings::get('banner.link')}" target = "_blank">
		    	<div id="banner-container-blocks">
			    	<div id="banner-left">
              <img src="{urlFor name='home'}ui/img/banner/banner-left-en2.png" alt="{Settings::get('banner.info')}">
			    	</div>
			    	<div id="banner-mid">
              <img src="{urlFor name='home'}ui/img/banner/banner-mid-en2.png" alt="{Settings::get('banner.info')}">
			    	</div>
			    	<div id="banner-right">
              <img src="{urlFor name='home'}ui/img/banner/banner-right-en2.png" alt="{Settings::get('banner.info')}">
			    	</div>
		    	</div>
		    </a>
		    </div>
		{/if}
        <br/>
        <br/>
