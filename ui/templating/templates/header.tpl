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

        <link rel="stylesheet" type="text/css" media="all" href="{urlFor name="home"}resources/bootstrap/css/bootstrap.min.css"/>
        <link rel="stylesheet" type="text/css" media="all" href="{urlFor name="home"}resources/css/style.1.css"/>
        <link rel="stylesheet" href="{urlFor name="home"}resources/css/jquery-ui.css"/>

		<link rel="shortcut icon" type="image/x-icon" href="{urlFor name="home"}favicon.ico">
		
        <!-- extra styles-->
        {if isset($extra_styles)}
            {$extra_styles}
        {/if}

        <!-- style overrides-->
        <link rel="stylesheet" href="{urlFor name="home"}resources/css/solas.css"/>

        {if isset($platformJS)}
            {$platformJS}
        {/if}
        <!-- javascript -->
        <script type="text/javascript" src="{urlFor name="home"}ui/js/lib/jquery-1.9.0.js"></script>
        <script type="text/javascript" src="{urlFor name="home"}ui/js/lib/jquery-ui.js"></script>

        <!-- google analytics -->
        <script type="text/javascript" src="{urlFor name="home"}ui/js/tracking.js"></script>

        <!-- extra Scripts -->
        {if isset($extra_scripts)}
            {$extra_scripts}
        {/if}
    </head>

        <body {if isset($body_class)}class="{$body_class}"{/if} {if isset($body_id)}id="{$body_id}"{/if} style="background-image:  url({urlFor name="home"}ui/img/bg.png); background-repeat: repeat">
        <div class="navbar navbar-fixed-top">
           <div class="navbar-inner">
                <div class="container">
                    <a class="brand" href="{urlFor name='home'}">{Settings::get('site.name')}</a>
                    <ul class="nav">
                        <li {if isset($current_page) && $current_page == 'home'}class="active"{/if} >
                            <a href="{urlFor name="home"}">{Localisation::getTranslation('header_home')}</a>
                        </li> 
                        {if isset($user_is_organisation_member)||isset($site_admin)}
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
                        {if isset($site_admin)}
                            {assign var="user_id" value=$user->getId()}
                            <li {if isset($current_page) && $current_page == 'site-admin-dashboard'}class="active" {/if}>
                                <a href="{urlFor name="site-admin-dashboard" options="user_id.$user_id"}">{Localisation::getTranslation('header_admin')}</a>
                            </li>
                        {/if}
                            <li {if isset($current_page) && $current_page == 'videos'}class="active" {/if}>
                                <a href="{urlFor name="videos"}">{Localisation::getTranslation('header_videos')}</a>
                            </li>
                             <li {if isset($current_page) && $current_page == 'faq'}class="active" {/if}>
                                <a href="{urlFor name="faq"}">{Localisation::getTranslation('common_faq')}</a>
                            </li>
                           	{if Settings::get('site.forum_enabled') == 'y'}
	                            <li>
	                                <a href="{Settings::get('site.forum_link')}" target="_blank">{Localisation::getTranslation('common_forum')}</a>
	                            </li>
                            {/if}
                    </ul>
                    <ul class="nav pull-right" style="max-height: 38px">
                        {if isset($userNotifications)}   
                            <li>
                                <a>{Localisation::getTranslation('header_notifications')}<span id="notificationCount">{$userNotifications->lenght()}</span></a>
                            </li>
                        {/if}
                        <li>
                       
                            {if isset($locs)}
                            	
                                <div class="languageForm">
                                    <form id="languageListForm" method="post" action="{urlFor name="siteLanguage"}">
                                        <select id="languageList" name="language" onchange="jQuery('#languageListForm').submit();">
                                            {foreach $locs as $loc}
                                                {if $loc->getCode() == {UserSession::getUserLanguage()}}
                                                    <option value="{$loc->getCode()}" selected>{$loc->getName()}</option>
                                                {else}
                                                    <option value="{$loc->getCode()}">{$loc->getName()}</option>
                                                {/if}
                                            {/foreach}
                                        </select>
                                    </form>
                                </div>
                            {/if}
                        </li>
                        {if isset($user)}
                            <li>
                                <a href="{urlFor name="user-public-profile" options="user_id.$user_id"}">
                                    <img src="http://www.gravatar.com/avatar/{md5( strtolower( trim($user->getEmail())))}?s=20{urlencode("&")}r=g" alt="" />
                                       {$user->getDisplayName()}
                                </a>
                            </li>
                            <li>
                                <a href="{urlFor name="logout"}">{Localisation::getTranslation('header_log_out')}</a>
                            </li>
                        {else}
                            <li><a href="{urlFor name="register"}">{Localisation::getTranslation('common_register')}</a></li>
                            <li><a href="{urlFor name="login"}">{Localisation::getTranslation('common_log_in')}</a></li>
                        {/if}
                    </ul>
                </div>
            </div>
        </div>
        <div class="container">
        
        {assign var="home_page" value="{urlFor name="home"}"}
        
        {if ((Settings::get('banner.enabled') == 'y') and isset($user))}
		    <div id="banner-container">
		    <a href = "{Settings::get('banner.link')}" target = "_blank">
		    	<div id="banner-container-blocks">
			    	<div id="banner-left">
			    		<img src="{urlFor name='home'}ui/img/banner/banner-left-en.png" alt="{Settings::get('banner.info')}">
			    	</div>
			    	<div id="banner-mid">
			    		<img src="{urlFor name='home'}ui/img/banner/banner-mid-en.png" alt="{Settings::get('banner.info')}">
			    	</div>
			    	<div id="banner-right">
			    		<img src="{urlFor name='home'}ui/img/banner/banner-right-en.png" alt="{Settings::get('banner.info')}">
			    	</div>
		    	</div>
		    </a>
		    </div>
		{/if}
