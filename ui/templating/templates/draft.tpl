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
                                <a href="https://analytics.translatorswb.org" target="_blank">Analytics</a>
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
        