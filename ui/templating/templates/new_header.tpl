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
            <div class="d-md-flex align-items-center justify-content-around w-100">
            <ul class="navbar-nav  d-flex align-items-center ">
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

                {if isset($user_has_active_tasks)}
                    {assign var="tmp_id" value=$user->getId()}
                    <li class="nav-item fw-bold" >
                        <a href="{urlFor name="claimed-tasks" options="user_id.$tmp_id"}" class="fs-5 nav-link fw-bold"  {if isset($current_page) && $current_page == 'claimed-tasks'} class="nav-link " {/if}>{Localisation::getTranslation('header_claimed_tasks')}</a>
                    </li>
                {/if} 

                {if isset($show_admin_dashboard)}
                {assign var="user_id" value=$user->getId()}
                    <li class="nav-item fw-bold" {if isset($current_page) && $current_page == 'site-admin-dashboard'}" {/if}>
                        <a href="{urlFor name="site-admin-dashboard" options="user_id.$user_id"}"  class="fs-5 nav-link fw-bold">{Localisation::getTranslation('header_admin')}  </a>
                    </li>
                {/if} 

                 {if !isset($site_admin)}
                             <li {if isset($current_page) && $current_page == 'faq'}" {/if} class="nav-item ">
                                <a href="https://communitylibrary.translatorswb.org/login" target="_blank" class=" fs-5 nav-link fw-bold">Library</a>
                            </li>
                {/if}
                {if Settings::get('site.forum_enabled') == 'y'}
                    <li>
                        <a href="{Settings::get('site.forum_link')}"  class=" fs-5 nav-link fw-bold">{Localisation::getTranslation('common_forum')}</a>
                    </li>
                {/if}

                {if isset($site_admin)}
                            <li class="nav-item">
                                <a href="{urlFor name="analytics"}"  class=" fs-5 nav-link fw-bold">Analytics</a>
                            </li>
                        {/if}
                 {if !isset($site_admin)}
                            <li class="nav-item">
                                {if isset($user)}
                                <a href="https://elearn.translatorswb.org/auth/saml2/login.php?wants&idp=bd3eb3e6241260ee537b9a55145d852d&passive=off" target="_blank" class="fs-5 nav-link fw-bold">TWB Learning Center</a>
                                {else}
                                <a href="https://elearn.translatorswb.org/" target="_blank" class=" fs-5 nav-link fw-bold">TWB Learning Center</a>
                                {/if}
                            </li>
                            <li class="nav-item">
                                <a href="https://form.asana.com?k=dlsF11XkOwpfFllbq325dg&d=170818793545926" target="_blank" class=" fs-5 nav-link fw-bold">Feedback?</a>
                            </li>
                            {if !empty($user) && Settings::get('banner.enabled') == 'v' && $user->getNativeLocale() != null && (in_array($user->getNativeLocale()->getLanguageCode(), ['shu']) || in_array($user->getNativeLocale()->getLanguageCode(), ['ha', 'kr', 'en']) && in_array($user->getId(), [184722,39794,39707,38260,29387,39223,21773,28600,73316,35154,22929,101194,21221,38255,58843,184885,45796,33415,30433,72412,32887,30925,32886,24797,26079,96332,24688,24697,26843,25241,248228, 26170, 248026,105815,21211,248025,195146,248027,203291,213875,202975, 18162, 248504, 136330,  22929, 101194, 21221, 27696, 87328, 37112, 31683, 184885, 203791, 21642, 45796, 80541, 33415, 249757, 30433, 72412, 36112, 75522, 30747, 78972, 43280, 29623 ,244711,133770,218381,109722,32887,33861,37833,191514,249893,30853,250683,30925,31551,33892,21344,31611,250528,32886,159363,238482,238958,109723,245837,245788,119659,218239,248106,31552,36109,240640,191767,24797,34230,26079,109737,96332,113616,45796,250281,31883,24827,250608,24688,32844,135695,97359,249757,250651,31641,24697,33846,110320,238340,33824,31550,250563,237329,248198,30836,26843,34054,239334,249285,241247,246952,238979,98943,25241,13072,36142,95960,241119,31067, 241289,  202860, 145338, 210445, 198808, 224866, 184923, 214065, 249840, 249091, 248715, 247918, 245976, 238957, 238390, 237345, 237197, 228956, 224669, 207752, 190802, 188546, 188367, 188107, 186987, 186168, 185297, 21837, 21642, 21419, 32304, 36049, 38500, 30973, 21415, 38512, 98949, 33751, 109483, 246403, 251897, 89862, 91306, 28320, 75431, 26177, 23533, 252497, 90637, 91324, 244874]))}
                            <li class="nav-item">
                                <a href="https://twbvoice.org" target="_blank" class=" fs-5 nav-link fw-bold">TWB Voice</a>
                            </li>
                            {/if}
                        {else}
                            <li class="nav-item">
                                <a href="https://elearn.translatorswb.org/auth/saml2/login.php?wants&idp=bd3eb3e6241260ee537b9a55145d852d&passive=off" target="_blank" class=" fs-5 nav-link fw-bold">Learn. Center</a>
                            </li>
                        {/if}
            </ul>

             <ul class="navbar-nav d-flex align-items-center">
                        {if isset($user)}
                        {assign var="user_id" value=$user->getId()}
                              <li class="nav_item me-2" id="theme">
                               <img src="{urlFor name='home'}ui/img/light.svg"   alt="theme button" id="light">
                           
                               <img src="{urlFor name='home'}ui/img/night.svg" class="d-none" alt="theme button" id="night">
                            </li>
                            <li class="profile nav-item">
                                <a href="{urlFor name="user-public-profile" options="user_id.$user_id"}"  class=" fs-5 nav-link fw-bold">
                                    <img src="https://www.gravatar.com/avatar/{md5( strtolower( trim($user->getEmail())))}?s=20{urlencode("&")}r=g" alt="" />
                                       {TemplateHelper::uiCleanseHTML($user->getDisplayName())}
                                </a>
                            </li>
                            <li class="logout nav-item" >
                                <a href="{urlFor name="logout"}" class=" fs-5 nav-link fw-bold">{Localisation::getTranslation('header_log_out')}</a>
                            </li>
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
        {if !empty($user) && Settings::get('banner.enabled') == 'v' && $user->getNativeLocale() != null && (in_array($user->getNativeLocale()->getLanguageCode(), ['shu']) || in_array($user->getNativeLocale()->getLanguageCode(), ['ha', 'kr', 'en']) && in_array($user->getId(), [184722,39794,39707,38260,29387,39223,21773,28600,73316,35154,22929,101194,21221,38255,58843,184885,45796,33415,30433,72412,32887,30925,32886,24797,26079,96332,24688,24697,26843,25241,248228, 26170, 248026,105815,21211,248025,195146,248027,203291,213875,202975, 18162, 248504, 136330,  22929, 101194, 21221, 27696, 87328, 37112, 31683, 184885, 203791, 21642, 45796, 80541, 33415, 249757, 30433, 72412, 36112, 75522, 30747, 78972, 43280, 29623 ,244711,133770,218381,109722,32887,33861,37833,191514,249893,30853,250683,30925,31551,33892,21344,31611,250528,32886,159363,238482,238958,109723,245837,245788,119659,218239,248106,31552,36109,240640,191767,24797,34230,26079,109737,96332,113616,45796,250281,31883,24827,250608,24688,32844,135695,97359,249757,250651,31641,24697,33846,110320,238340,33824,31550,250563,237329,248198,30836,26843,34054,239334,249285,241247,246952,238979,98943,25241,13072,36142,95960,241119,31067, 241289,  202860, 145338, 210445, 198808, 224866, 184923, 214065, 249840, 249091, 248715, 247918, 245976, 238957, 238390, 237345, 237197, 228956, 224669, 207752, 190802, 188546, 188367, 188107, 186987, 186168, 185297, 21837, 21642, 21419, 32304, 36049, 38500, 30973, 21415, 38512, 98949, 33751, 109483, 246403, 251897, 89862, 91306, 28320, 75431, 26177, 23533, 252497, 90637, 91324, 244874]))}
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
