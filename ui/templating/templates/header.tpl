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

        <link rel="stylesheet" type="text/css" media="all" href="{urlFor name="home"}resources/bootstrap/css/bootstrap.min1.css"/>
        <link rel="stylesheet" type="text/css" media="all" href="{urlFor name="home"}resources/css/style.2.css"/>
        <link rel="stylesheet" href="{urlFor name="home"}resources/css/jquery-ui.css"/>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

		<link rel="shortcut icon" type="image/x-icon" href="{urlFor name="home"}ui/img/favicon/faviconM.png"> 
        <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">

        {if isset($extra_styles)}
            {$extra_styles}
        {/if}

        {* <link rel="stylesheet" href="{urlFor name="home"}resources/css/solas2.css"/> *}
            <link rel="stylesheet" href="{urlFor name="home"}resources/css/costum.css"/>

        {if isset($platformJS)}
            {$platformJS}
        {/if}
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
    <style>
    .navbar .nav li a{
        color:#143878 !important;
    }
     
    .social_media_icons{
        width:50%;
    }
    
    .header-link{
        margin-bottom:1%;
        margin-right:1%;
       }
       .navbar-inner{
           margin-top:0.2%;
       }
        .not-active {
        pointer-events: none;
        cursor: default;
    }
    .containerBox {
    position: relative;
    display: inline-block;
}
.text-box {
    position: absolute;    
    height: 100%;
    text-align: center;    
    width: 100%;

}
.text-box:before {
   content: '';
   display: inline-block;
   height: 100%;
   vertical-align: middle;
}

.first_badge_name {
    display: inline-block;
    font-size: 20px;
    color: #000000;
    position: relative;
    top: -35px;
    left: -100px;
}

.first_badge {
    display: inline-block;
    font-weight: bold;
    font-size: 18px;
    position: relative;
    top: -135px;
    left: -102px;
}
/* 2 following contained in above */
.first_badge_number {
    color: #E8991C;
    font-size:25px;
    position: relative;
    top: -10px;
}
.first_badge_desc {
    display: inline-block;
    color: #576E82;
    position: relative;
    top: 0px;
    text-transform: uppercase;
}

.recognition_name {
    display: inline-block;
    font-size: 20px;
    color: #000000;
    position: relative;
    top: -35px;
    left: -95px;
}

.recognition {
    display: inline-block;
    font-size: 17px;
    color: #e8991c;
    position: relative;
    top: -135px;
    left: -90px;  
}
/* 2 following contained in above */
.recognition_number {
    color: #E8991C;
    font-size: 25px;
    position: relative;
    top: -10px;
}
.recognition_desc {
    color: #576E82;
    text-transform: uppercase;
}

.strategic_name {
    display: inline-block;
    font-size: 20px;
    color: #000000;
    position: relative;
    top: -65px;
    left: -85px;
}

.strategic {
    display: inline-block;
    font-weight: bold;
    font-size: 17px;
    color: #e8991c;
    position: relative;
    top: -200px;
    left: -55px;
}
/* 4 following contained in above */
.strategic_number {
    color: #E8991C;
    font-size:25px;
    position: relative;
    top: 15px;
    left: -37px;
}
.strategic_desc {
    color: #576E82;
    text-transform: uppercase;
    position: relative;
    top: 20px;
    left: -35px;
}
.strategic_desc2 {
    color: #576E82;
    text-transform: uppercase;
    position: relative;
    top: 10px;
    left: -40px;
}
.strategic_number2 {
    color: #E8991C;
    font-size: 25px;
    position: relative;
    top: 1px;
}
    </style>
    </head>

        <body {if isset($body_class)}class="{$body_class}"{/if} {if isset($body_id)}id="{$body_id}"{/if}>
        <div class="navbar navbar-fixed-top">
           <div class="navbar-inner">
                <div class="container">
                    <a href="{urlFor name='home'}" class="pull-left header-link"><img height="60px" style="margin-right: 25px;"  src="{urlFor name='home'}ui/img/TWB_logo1.PNG"></a> 
                    <ul class="nav main_nav">
                        {if !isset($site_admin)}
                        <li {if isset($current_page) && $current_page == 'home'}class="active"{/if} >
                            <a href="{urlFor name="home"}">{Localisation::getTranslation('header_home')}</a>
                        </li> 
                        {/if}
                        {if isset($dashboard)}
                             <li {if isset($current_page) && $current_page == 'org-dashboard'}class="active"{/if} >
                                 <a href="{urlFor name="org-dashboard"}">{Localisation::getTranslation('header_dashboard')}</a>
                             </li>
                         {/if}
                        {if isset($user_has_active_tasks)}
                            {assign var="tmp_id" value=$user->getId()}
                            <li {if isset($current_page) && $current_page == 'claimed-tasks'}class="active" {/if}>
                                <a href="{urlFor name="claimed-tasks" options="user_id.$tmp_id"}">{Localisation::getTranslation('header_claimed_tasks')}</a>
                            </li>
                        {/if}
                        {if isset($user)}
                            {assign var="user_id" value=$user->getId()}
                            <li {if isset($current_page) && $current_page == 'user-profile'}class="active" {/if}>
                                <a href="{urlFor name="user-public-profile" options="user_id.$user_id"}">{Localisation::getTranslation('header_profile')}</a>
                            </li>
                        {/if}
                        {if isset($show_admin_dashboard)}
                            {assign var="user_id" value=$user->getId()}
                            <li {if isset($current_page) && $current_page == 'site-admin-dashboard'}class="active" {/if}>
                                <a href="{urlFor name="site-admin-dashboard" options="user_id.$user_id"}">{Localisation::getTranslation('header_admin')}</a>
                            </li>
                        {/if}
                        {if !isset($site_admin)}
                             <li {if isset($current_page) && $current_page == 'faq'}class="active" {/if}>
                                <a href="https://community.translatorswb.org/t/the-translators-toolkit/3138" target="_blank">{Localisation::getTranslation('common_faq')}</a>
                            </li>
                        {/if}
                           	{if Settings::get('site.forum_enabled') == 'y'}
	                            <li>
	                                <a href="{Settings::get('site.forum_link')}" target="_blank">{Localisation::getTranslation('common_forum')}</a>
	                            </li>
                            {/if}
                        {if isset($site_admin)}
                            <li>
                                <a href="{urlFor name="analytics"}" target="_blank">Analytics</a>
                            </li>
                        {/if}
                        {if !isset($site_admin)}
                            <li>
                                {if isset($user)}
                                <a href="https://elearn.translatorswb.org/auth/saml2/login.php?wants&idp=bd3eb3e6241260ee537b9a55145d852d&passive=off" target="_blank">TWB Learning Center</a>
                                {else}
                                <a href="https://elearn.translatorswb.org/" target="_blank">TWB Learning Center</a>
                                {/if}
                            </li>
                            <li>
                                <a href="https://form.asana.com?k=dlsF11XkOwpfFllbq325dg&d=170818793545926" target="_blank">Feedback?</a>
                            </li>
                        {else}
                            <li>
                                <a href="https://elearn.translatorswb.org/auth/saml2/login.php?wants&idp=bd3eb3e6241260ee537b9a55145d852d&passive=off" target="_blank">Learn. Center</a>
                            </li>
                        {/if}
                    </ul>
                    <ul class="nav pull-right main_nav_right" style="max-height: 38px">
                        {if isset($userNotifications)}   
                            <li>
                                <a>{Localisation::getTranslation('header_notifications')}<span id="notificationCount">{$userNotifications->lenght()}</span></a>
                            </li>
                        {/if}
                        {if isset($user)}
                            <li class="profile">
                                <a href="{urlFor name="user-public-profile" options="user_id.$user_id"}">
                                    <img src="https://www.gravatar.com/avatar/{md5( strtolower( trim($user->getEmail())))}?s=20{urlencode("&")}r=g" alt="" />
                                       {TemplateHelper::uiCleanseHTML($user->getDisplayName())}
                                </a>
                            </li>
                            <li class="logout">
                                <a href="{urlFor name="logout"}">{Localisation::getTranslation('header_log_out')}</a>
                            </li>
                        {else}
            
                            <li class="social_link"><a href="https://facebook.com/translatorswithoutborders" target="_blank"><img class="social_media_icons" src="{urlFor name='home'}ui/img/social_media_icons/facebook_logo_social network_icon.png" alt="FB_Logo"></a></li>
                            <li><a href="https://www.instagram.com/translatorswb/?hl=en" target="_blank"><img class="social_media_icons" src="{urlFor name='home'}ui/img/social_media_icons/instagram logo_icon.png" alt="FB_Logo"></a></li>
                            <li><a  href="https://linkedin.com/company/translators-without-borders" target="_blank"><img class="social_media_icons" src="{urlFor name='home'}ui/img/social_media_icons/linkedin logo_icon.png" alt="FB_Logo"></a></li>
                            <li><a   href="https://twitter.com/TranslatorsWB" target="_blank"><img class="social_media_icons" src="{urlFor name='home'}ui/img/social_media_icons/twitter logo_icon.png" alt="FB_Logo"></a></li>
                            <br/>

                            <li><a href="{urlFor name="register"}">Join</a></li>
                            <li><a href="{urlFor name="login"}">{Localisation::getTranslation('common_log_in')}</a></li>
                        {/if}
                    </ul>
                </div>
            </div>
        </div>
        <div class="container">
        
        {assign var="home_page" value="{urlFor name="home"}"}
        
        {if ((Settings::get('banner.enabled') == 'y') and (isset($user) or ($smarty.server.REQUEST_URI!=$home_page)))}
            
		    <div id="banner-container" >
            
            <a href="https://drive.google.com/file/d/1FQNRR-iilpB8Yn8fjT5iF2e0nyqUBub3/view?usp=sharing">
		    
		    	<div style="display:flex; justify-content:center;  align-items:center;">
            
              <img src="{urlFor name='home'}ui/img/banner.png"  alt="{Settings::get('banner.info')}">
		    
		    	</div>
            </a>
            </div>
          	
		    
		    
		{/if}
        <br/>
        <br/>
