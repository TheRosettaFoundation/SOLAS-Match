<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en" data-bs-theme="light"  >
    <head>
        <meta charset="utf-8" content="application/xhtml+xml" />

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

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <link href="{urlFor name="home"}ui/css/custom.css" rel="stylesheet" type="text/css"> 
        <link rel="shortcut icon" type="image/x-icon" href="{urlFor name="home"}ui/img/favicon/faviconM.png"> 
        <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
		
        {if isset($extra_styles)}
            {$extra_styles}
        {/if}
        {if isset($platformJS)}
            {$platformJS}
        {/if}
        <script type="text/javascript" src="{urlFor name="home"}ui/js/lib/jquery-1.9.0.js"></script>
        <script type="text/javascript" src="{urlFor name="home"}ui/js/lib/jquery-ui.js"></script>
        <script type="text/javascript" src="{urlFor name="home"}ui/js/lib/dayjs.min.js"></script>
        <script type="text/javascript" src="{urlFor name="home"}ui/js/lib/plugin/utc.js"></script>
   
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
            var source_and_target = [0,
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
        {if isset($extra_scripts)}
            {$extra_scripts}
        {/if}
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.6/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@eonasdan/tempus-dominus@6.7.16/dist/js/tempus-dominus.js"></script>
        <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@eonasdan/tempus-dominus@6.7.16/dist/css/tempus-dominus.css"
        />
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    </head>

        <body  class="flex-grow-1">
        <div class="d-flex flex-column min-vh-100 ">
      
        <nav data-bs-theme="light" id="nav" class="navbar navbar-expand-lg bg-body-tertiary shadow bg-secondary d-flex ">
        <div class="container py-2">

                 <a class="navbar-brand" href={urlFor name='home'}> <img  src="{urlFor name='home'}ui/img/TWB_Logo.svg" class="logo"> </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
                </button>
           
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <div class="d-md-flex align-items-center justify-content-between w-100">
            <ul class="navbar-nav   d-flex align-items-center ">
                {if !isset($admin)}
                    <li class="nav-item ms-md-6 fw-bold">
                    <a href="{urlFor name="home"}" class="fs-5 nav-link fw-bold" {if isset($current_page) && $current_page == 'home'} {/if}>{Localisation::getTranslation('header_home')}</a>
                    </li>
                {/if} 
               

                 {if isset($dashboard)}
                             <li {if isset($current_page) && $current_page == 'org-dashboard'} class="nav-item fw-bold"{/if} >
                                 <a href="{urlFor name="org-dashboard"}" class="fs-5 nav-link fw-bold">{Localisation::getTranslation('header_dashboard')}</a>
                             </li>
                {/if}

             

                {if isset($show_admin_dashboard)}
                {assign var="user_id" value=$user->getId()}
                    <li class="nav-item fw-bold" {if isset($current_page) && $current_page == 'site-admin-dashboard'}" {/if}>
                        <a href="{urlFor name="site-admin-dashboard" options="user_id.$user_id"}"  class="fs-5 nav-link fw-bold">{Localisation::getTranslation('header_admin')}  </a>
                    </li>
                {/if} 

             
               
                    {if isset($site_admin)}
                            <li class="nav-item">
                                <a href="{urlFor name="analytics"}"  class=" fs-5 nav-link fw-bold">Analytics</a>
                            </li>
                        {/if}
                <li class="nav-item dropdown fw-bold text-secondary">
                <a class="nav-link dropdown-toggle fs-5  fw-bold no-caret  " href="#" id="hoverDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Resources
                </a>
                <ul class="dropdown-menu" aria-labelledby="hoverDropdown">
                   {if Settings::get('site.forum_enabled') == 'y'}
                    <li class="py-2">
                    <a href="{Settings::get('site.forum_link')}" target="_blank" class=" dropdown-item  py-2 fw-bold"> 
                        <div>
                            <div>
                            <span class='mx-2'>🎓</span>
                            Community Forum 
                            </div>
                            <div class="fs-5 fw-bold  text-break mt-2 mx-2">Ask questions and talk with other community members</div>                        
                        </div>
                
                    </a>
                    
                       
                    </li>
                    {/if}
                    {* {if !isset($site_admin)} *}
                    <li class="py-2">
                    {* {if isset($user)} *}
                    <a href="https://elearn.translatorswb.org/auth/saml2/login.php?wants&idp=bd3eb3e6241260ee537b9a55145d852d&passive=off" target="_blank" class="dropdown-item py-2 fw-bold" >
                    
                    <div>
                        <div><span class="mx-2">📚</span> Learning Center </div>
                        <div class=" mt-2 mx-2 fs-5 fw-bold "> Courses and training </div>
                    </div>
                     
                    </a>
                    {* {else} *}
                    {* <a href="https://elearn.translatorswb.org/" target="_blank" class="dropdown-item py-2">Learning Center
                     <div class=" mt-2 fs-5 fw-lighter drop_description">A dynamic learning center offering resources, tutorials, and interactive content to help users grow their skills, explore new topics, and stay updated. Empowering individuals to learn at their own pace, anytime and anywhere, with support from a vibrant knowledge-sharing community.</div>
                    </a> *}
                    {* {/if} *}
                   
                     </li>
                    {* {/if} *}
                    {* {if !isset($site_admin)} *}
                    <li {if isset($current_page) && $current_page == 'faq'}" {/if} class="py-2" > <a  href="https://communitylibrary.translatorswb.org/login" target="_blank" class="dropdown-item py-2 fw-bold">
                        <div> 
                            <div><span class="mx-2">💬</span> Community Library</div>
                            <div class="mt-2 fs-5 fw-bold mx-2">Instructions, guidelines and reference material</div>
                        </div>
                          
                        </a>
                      
                        </li>
                    {* {/if} *}
                  
                </ul>
                 </li>

                 {* second trial  *}

                 <li class="nav-item dropdown fw-bold text-secondary">
                <a class="nav-link dropdown-toggle fs-5  fw-bold no-caret  " href="#" id="hoverDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Resources-2
                </a>
                <ul class="dropdown-menu" aria-labelledby="hoverDropdown">
                   {if Settings::get('site.forum_enabled') == 'y'}
                    <li ><a href="{Settings::get('site.forum_link')}" target="_blank" class=" dropdown-item  py-4 fw-bold "> <div class="drop_description__shadow p-2">
                    
                    <div > <span class='mx-2'>🎓</span>Community Forum</div> 
                     <div class="fs-5 fw-bold  text-break mt-2 mx-2 ">Ask questions and talk with other community members</div>
                    </div>
               
                    </a>
                    
                       
                    </li>
                    {/if}
                    {* {if !isset($site_admin)} *}
                    <li >
                    {* {if isset($user)} *}
                    <a href="https://elearn.translatorswb.org/auth/saml2/login.php?wants&idp=bd3eb3e6241260ee537b9a55145d852d&passive=off" target="_blank" class="dropdown-item py-2 fw-bold " ><div class="drop_description__shadow p-2"> 
                    <div><span class="mx-2">📚</span> Learning Center</div>
                     <div class=" mt-2 fs-5 fw-bold  mx-2 "> Courses and training </div>
                     </div>
                    
                    </a>
                
                   
                     </li>
                    {* {/if} *}
                    {* {if !isset($site_admin)} *}
                    <li {if isset($current_page) && $current_page == 'faq'}" {/if}  > <a  href="https://communitylibrary.translatorswb.org/login" target="_blank" class="dropdown-item py-2 fw-bold "><div class="drop_description__shadow p-2"><div><span class="mx-2">💬</span> Community Library</div>
                         <div class=" mt-2 fs-5 fw-bold  mx-2 ">Instructions, guidelines and reference material</div></div>
                         
                        </a>
                      
                        </li>
                    {* {/if} *}
                  
                </ul>
                 </li>
               
                {* {if Settings::get('site.forum_enabled') == 'y'}
                    <li>
                        <a href="{Settings::get('site.forum_link')}"  class=" fs-5 nav-link fw-bold">{Localisation::getTranslation('common_forum')}</a>
                    </li>
                {/if} *}

            
                 {if !isset($site_admin)}
                            {* <li class="nav-item">
                                {if isset($user)}
                                <a href="https://elearn.translatorswb.org/auth/saml2/login.php?wants&idp=bd3eb3e6241260ee537b9a55145d852d&passive=off" target="_blank" class="fs-5 nav-link fw-bold">TWB Learning Center</a>
                                {else}
                                <a href="https://elearn.translatorswb.org/" target="_blank" class=" fs-5 nav-link fw-bold">TWB Learning Center</a>
                                {/if}
                            </li> *}
                          
                            {if !empty($user) && Settings::get('banner.enabled') == 'v' && $user->getNativeLocale() != null && in_array($user->getNativeLocale()->getLanguageCode(), ['ha', 'kr', 'shu'])}
                            <li class="nav-item">
                                <a href="https://twbvoice.org" target="_blank" class=" fs-5 nav-link fw-bold">TWB Voice</a>
                            </li>
                            {/if}
                        {* {else}
                            <li class="nav-item">
                                <a href="https://elearn.translatorswb.org/auth/saml2/login.php?wants&idp=bd3eb3e6241260ee537b9a55145d852d&passive=off" target="_blank" class=" fs-5 nav-link fw-bold">Learn. Center</a>
                            </li> *}
                        {/if}
            </ul>

             <ul class="navbar-nav d-flex align-items-center mx-4 fw-bold">
                        {if isset($user)}
                        {assign var="user_id" value=$user->getId()}

                <li class="nav-item dropdown ">
                        <a class="nav-link dropdown-toggle no-caret " href="#" id="hoverDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="https://www.gravatar.com/avatar/{md5( strtolower( trim($user->getEmail())))}?s=20{urlencode("&")}r=g" alt="" />
                                       <span class="profile_name"> {TemplateHelper::uiCleanseHTML($user->getDisplayName())} </span>    
                        </a>
                        <ul class="dropdown-menu"   aria-labelledby="hoverDropdown">
                            <li><a href="{urlFor name="user-public-profile" options="user_id.$user_id"}"   class="dropdown-item fs-5" id="dropdown-menu-user" >Profile</a></li>
                                        
                            {if isset($user_has_active_tasks)}
                            {assign var="tmp_id" value=$user->getId()}
                            <li class="nav-item fw-bold" >
                                <a href="{urlFor name="claimed-tasks" options="user_id.$tmp_id"}" class="dropdown-item fs-5" id="dropdown-menu-user"  {if isset($current_page) && $current_page == 'claimed-tasks'} class="nav-link " {/if}>My Tasks</a>
                            </li>
                        {/if} 
                        <li> <a href="{urlFor name="logout"}" class="dropdown-item fs-5" id="dropdown-menu-user">{Localisation::getTranslation('header_log_out')}</a></li>
            
                            
                        
                
                        </ul>
                 </li>

                <li class="nav_item " id="theme">
                <img src="{urlFor name='home'}ui/img/light.svg"   alt="theme button" id="light">
            
                <img src="{urlFor name='home'}ui/img/night.svg" class="d-none" alt="theme button" id="night">
                </li>

                   {if !isset($site_admin)}

                      <li class="nav-item">
                                <a href="https://form.asana.com?k=dlsF11XkOwpfFllbq325dg&d=170818793545926" target="_blank" class=" fs-5 nav-link fw-bold">Feedback?</a>
                            </li>

                   {/if}
                             
                            {* <li class="profile nav-item">
                                <a href="{urlFor name="user-public-profile" options="user_id.$user_id"}"  class=" fs-5 nav-link fw-bold">
                                    <img src="https://www.gravatar.com/avatar/{md5( strtolower( trim($user->getEmail())))}?s=20{urlencode("&")}r=g" alt="" />
                                       {TemplateHelper::uiCleanseHTML($user->getDisplayName())}
                                </a>
                            </li> *}
                            {* <li class="logout nav-item" >
                                <a href="{urlFor name="logout"}"   class=" fs-5 nav-link fw-bold">{Localisation::getTranslation('header_log_out')}</a>
                            </li> *}
                        {else}
                            <li class="nav-item"><a href="{urlFor name="register"}" class="nav-link fw-bold">Join</a></li>
                            <li class="nav-item"><a href="{urlFor name="login"}" class="nav-link fw-bold">{Localisation::getTranslation('common_log_in')}</a></li>
                        {/if}
                        {if isset($userNotifications)}   
                            <li class="nav-item">
                                <a  class=" fs-5 nav-link fw-bold">{Localisation::getTranslation('header_notifications')}<span id="notificationCount">{$userNotifications->lenght()}</span></a>
                            </li>
                        {/if}
                    </ul>
            </div>
            </div>
        </div>
        </nav>
        {if isset($user) && Settings::get('banner.enabled') == 'y'}
            <div id="banner-container" class="container">
                <div id="banner-container-blocks" class="d-flex justify-content-center">
                    <a href="https://drive.google.com/file/d/1FQNRR-iilpB8Yn8fjT5iF2e0nyqUBub3/view?usp=sharing">
                        <div id="banner-mid">
                           <img src="{urlFor name='home'}ui/img/banner.png" alt="{Settings::get('banner.info')}">
                        </div>
                    </a>
                </div>
            </div>
        {/if}
        {if !empty($user) && Settings::get('banner.enabled') == 'v' && $user->getNativeLocale() != null && in_array($user->getNativeLocale()->getLanguageCode(), ['ha', 'kr', 'shu'])}
            <div id="banner-container" class="container">
                <div id="banner-container-blocks" class="d-flex justify-content-center" >
                    <a href="https://twbvoice.org" target="_blank">
                        <div id="banner-mid">
                           <img src="{urlFor name='home'}ui/img/voice.png" style="width: 925px; height:118px" alt="TWB Voice">
                        </div>
                    </a>
                </div>
            </div>
        {/if}

        <main class="flex-grow-1">
