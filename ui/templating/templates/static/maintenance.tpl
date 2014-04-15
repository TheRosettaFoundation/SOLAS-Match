<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en" >
    <head>
        <meta charset="utf-8" content="application/xhtml+xml" />
        <!--<meta name="google-translate-customization" content="d0b5975e5905d60f-4e4c167261d2937a-g4574d0ff41a34d5b-10" />-->

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
        <meta name="twitter:card" content="{Settings::get('twitter.card')}">
        <meta name="twitter:site" content="{Settings::get('twitter.site')}">
        <meta name="twitter:title" content="{Settings::get('twitter.title')}">
        <meta name="twitter:description" content="{Settings::get('twitter.description')}">
        <meta name="twitter:image" content="{Settings::get('twitter.image')}">
        
        
        <link rel="stylesheet" type="text/css" media="all" href="{urlFor name="home"}resources/bootstrap/css/bootstrap.min.css"/>
        <link rel="stylesheet" type="text/css" media="all" href="{urlFor name="home"}resources/css/style.1.css"/>
        <link rel="stylesheet" href="{urlFor name="home"}resources/css/jquery-ui.css"/>
        
        
        <!-- extra styles-->        
        {if isset($extra_styles)}
            {$extra_styles}
        {/if}
        
        <!-- style overrides-->
        <link rel="stylesheet" href="{urlFor name="home"}resources/css/solas.css"/>
        
        <!-- google analytics -->
        <script type="text/javascript" src="{urlFor name="home"}ui/js/tracking.js"></script>
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
                </div>
            </div>
        </div>
        <div class="container">

{if !isset($user)}
    <div class="hero-unit">
        <h1>{Localisation::getTranslation('index_translation_commons')}</h1>
        <p>{Localisation::getTranslation('index_0')}</p>
        <p>
            <a class="btn btn-success btn-large" href="{urlFor name="register"}">
                <i class="icon-star icon-white"></i> {Localisation::getTranslation('common_register')}
            </a>
            <a class="btn btn-primary btn-large" href="{urlFor name="login"}">
                <i class="icon-share icon-white"></i> {Localisation::getTranslation('common_log_in')}
            </a>
        </p>
    </div>
{/if}

{if isset($flash['error'])}
    <div class="alert alert-error">
        <a class="close" data-dismiss="alert" href="{urlFor name='home'}">×</a>
        <p><strong>{Localisation::getTranslation('common_warning')}! </strong>{$flash['error']}</p>
    </div>
{/if}

{if isset($flash['info'])}
    <div class="alert alert-info">
        <p><strong>{Localisation::getTranslation('common_note')} </strong>{$flash['info']}</p>
    </div>
{/if}

{if isset($flash['success'])}
    <div class="alert alert-success">
        <a class="close" data-dismiss="alert" href="{urlFor name='home'}">×</a>
        <p><strong>{Localisation::getTranslation('common_success')}! </strong>{$flash['success']}</p>
    </div>
{/if}

{if isset($flash['warning'])}
    <div class="alert alert-warning">
        <p><strong>{$flash['warning']}</strong></p>
    </div>
{/if}

    <div class="page-header">
        <h1>
        {Localisation::getTranslation('common_sitewide_maintenance')}
        </h1>
    </div>

    <div class="row">
        <div class="span4 pull-right">
            <section class="donate-block">
                <p>{Localisation::getTranslation('index_donate_free_service')}</p>
                <a href="http://www.therosettafoundation.org" target="_blank">
                    <img id="donate-trf-logo" src="{urlFor name='home'}ui/img/TheRosettaFoundationLogo.png" alt="The logo of The Rosetta Foundation" height="60"/>
                </a>
                <p>
                    <strong>{Localisation::getTranslation('index_donate_support_us')}</strong>
                </p>
                <div class="donate-button">
                	{sprintf(Localisation::getTranslation('index_donate_every_month'), "donate", "https://www.paypal.com/cgi-bin/webscr?cmd=_xclick-subscriptions&amp;business=Reinhard%2eSchaler%40ul%2eie&amp;item_name=Friend%20of%20The%20Rosetta%20Foundation&amp;src=1&amp;a3=5.00&amp;p3=1&amp;t3=M¤cy_code=EUR")}
                </div>
                <p>
                  	{sprintf(Localisation::getTranslation('index_donate_once_off'), "https://www.paypal.com/cgi-bin/webscr?cmd=_donations&amp;business=Reinhard%2eSchaler%40ul%2eie&amp;lc=US&amp;item_name=The%20Rosetta%20Foundation&amp;no_note=0¤cy_code=EUR", "https://www.paypal.com/cgi-bin/webscr?cmd=_xclick-subscriptions&amp;business=Reinhard%2eSchaler%40ul%2eie&amp;item_name=Friend%20of%20The%20Rosetta%20Foundation&amp;src=1&amp;a3=5.00&amp;p3=1&amp;t3=M¤cy_code=EUR","http://www.therosettafoundation.org/participate/becomeafriend/","_blank")}
                </p>
            </section>

            <div id="globe" style="text-align: center">
                <br/>
                <script type="text/javascript" src="http://jh.revolvermaps.com/p.js"></script><script type="text/javascript">rm2d_ki101('7','300','150','7puikkj5km8','ff00ff',0);</script>
                <br/>
            </div>
        </div>

        <div class="pull-left" style="max-width: 70%; overflow-wrap: break-word; word-break:break-all;">
          {sprintf(Localisation::getTranslation('common_sitewide_maintenance_msg'), $maintenanceDuration)}   
        </div>
    </div>
            
{include file="footer.tpl"}        