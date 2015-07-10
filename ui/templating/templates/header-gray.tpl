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

<!--    <link rel="stylesheet" type="text/css" media="all" href="{urlFor name="home"}resources/bootstrap/css/bootstrap.min.css"/>
        <link rel="stylesheet" type="text/css" media="all" href="{urlFor name="home"}resources/css/style.1.css"/> -->

        <link rel="stylesheet" type="text/css" media="all" href="{urlFor name="home"}resources/public/stylesheets/style.css"/>

        <script src="{urlFor name="home"}resources/public/js/vendor/modernizr-2.8.3-respond-1.4.2.min.js"></script>

        <link rel="stylesheet" href="{urlFor name="home"}resources/css/jquery-ui.css"/>

		<link rel="shortcut icon" type="image/x-icon" href="{urlFor name="home"}favicon.ico">
		
        <!-- extra styles-->
        {if isset($extra_styles)}
            {$extra_styles}
        {/if}

        <!-- style overrides-->
       <!--  <link rel="stylesheet" href="{urlFor name="home"}resources/css/solas.css"/> -->

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
<!-- 
        <body {if isset($body_class)}class="{$body_class}"{/if} {if isset($body_id)}id="{$body_id}"{/if} style="background-image:  url({urlFor name="home"}ui/img/bg.png); background-repeat: repeat"> -->

        <body class="white">
      


  <!-- gray nav -->

  <nav class="navbar-gray navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container">


        <div class="navbar-header">
          <button type="button" class="navbar-toggle pull-left collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
              <div class="pull-right">
                  Login/ Register
              </div>
        </div>

        <div id="navbar" class="navbar-collapse collapse">
                    <ul>{if isset($user)}
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

        </div><!--/.navbar-collapse -->

      </div>
    </nav>



  <!-- end -->
