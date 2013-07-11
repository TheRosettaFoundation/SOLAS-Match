<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en" >
    <head>
        <meta charset="utf-8" content="application/xhtml+xml" />
        <meta name="google-translate-customization" content="d0b5975e5905d60f-4e4c167261d2937a-g4574d0ff41a34d5b-10" />

        <!-- css -->
        <title>{Settings::get('site.title')}</title>
        <link rel="stylesheet" type="text/css" media="all" href="{urlFor name="home"}resources/bootstrap/css/bootstrap.min.css"/>
        <link rel="stylesheet" type="text/css" media="all" href="{urlFor name="home"}resources/css/style.1.css"/>
        <link rel="stylesheet" href="{urlFor name="home"}resources/css/jquery-ui.css"/>
        
        
        <!-- extra styles-->        
        {if isset($extra_styles)}
            {$extra_styles}
        {/if}
        
        <!-- style overrides-->
        <link rel="stylesheet" href="{urlFor name="home"}resources/css/solas.css"/>

        <!-- javascript -->
        <script type="text/javascript" src="{urlFor name="home"}ui/js/lib/jquery-1.9.0.min.js"></script>
        <script type="text/javascript" src="{urlFor name="home"}ui/js/lib/jquery-ui.js"></script>
        <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
        
        <!-- google analytics -->
        <script type="text/javascript" src="{urlFor name="home"}ui/js/tracking.js"></script>
        
        <!-- extra Scripts -->
        {if isset($extra_scripts)}
            {$extra_scripts}
        {/if}
    </head>

    <body {if isset($body_class)}class="{$body_class}"{/if} {if isset($body_id)}id="{$body_id}"{/if}>
        <div class="navbar navbar-fixed-top">
           <div class="navbar-inner">
                <div class="container">
                    <a class="brand" href="{urlFor name='home'}">{Settings::get('site.name')}</a>
                    <ul class="nav">
                        <li {if isset($current_page) && $current_page == 'home'}class="active"{/if} >
                            <a href="{urlFor name="home"}">{Localisation::getTranslation(Strings::HEADER_HOME)}</a>
                        </li> 
                        {if isset($user_is_organisation_member)||isset($site_admin)}
                             <li {if isset($current_page) && $current_page == 'org-dashboard'}class="active"{/if} >
                                 <a href="{urlFor name="org-dashboard"}">{Localisation::getTranslation(Strings::HEADER_DASHBOARD)}</a>
                             </li>
                         {/if}
                        {if isset($user_has_active_tasks)}
                            <li {if isset($current_page) && $current_page == 'claimed-tasks'}class="active" {/if}>
                                <a href="{urlFor name="claimed-tasks" options="page_no.1"}">{Localisation::getTranslation(Strings::HEADER_CLAIMED_TASKS)}</a>
                            </li>
                        {/if}
                        {if isset($user)}
                            {assign var="user_id" value=$user->getId()}
                            <li {if isset($current_page) && $current_page == 'user-profile'}class="active" {/if}>
                                <a href="{urlFor name="user-public-profile" options="user_id.$user_id"}">{Localisation::getTranslation(Strings::HEADER_PROFILE)}</a>
                            </li>
                        {/if}
                        {if isset($site_admin)}
                            {assign var="user_id" value=$user->getId()}
                            <li {if isset($current_page) && $current_page == 'admin-dashboard'}class="active" {/if}>
                                <a href="{urlFor name="site-admin-dashboard" options="user_id.$user_id"}">{Localisation::getTranslation(Strings::HEADER_ADMIN)}</a>
                            </li>
                        {/if}
                            <li {if isset($current_page) && $current_page == 'videos'}class="active" {/if}>
                                <a href="{urlFor name="videos"}">{Localisation::getTranslation(Strings::HEADER_VIDEOS)}</a>
                            </li>
                    </ul>
                    <ul class="nav pull-right" style="max-height: 38px">
                        {if isset($userNotifications)}   
                            <li>
                                <a>{Localisation::getTranslation(Strings::HEADER_NOTIFICATIONS)}<span id="notificationCount">{$userNotifications->lenght()}</span></a>
                            </li>
                        {/if}
                        <li>
                       
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
                        </li>
                        {if isset($user)}
                            <li>
                                <a href="{urlFor name="user-public-profile" options="user_id.$user_id"}">
                                    <img src="{"http://www.gravatar.com/avatar/{md5( strtolower( trim($user->getEmail())))}?s=20{urlencode("&")}r=g"}" alt="" />
                                       {$user->getDisplayName()}
                                </a>
                            </li>
                            <li>
                                <a href="{urlFor name="logout"}">{Localisation::getTranslation(Strings::HEADER_LOG_OUT)}</a>
                            </li>
                        {else}
                            <li><a href="{urlFor name="register"}">{Localisation::getTranslation(Strings::COMMON_REGISTER)}</a></li>
                            <li><a href="{urlFor name="login"}">{Localisation::getTranslation(Strings::COMMON_LOG_IN)}</a></li>
                        {/if}
                    </ul>
                </div>
            </div>
        </div>
        <div class="container">
