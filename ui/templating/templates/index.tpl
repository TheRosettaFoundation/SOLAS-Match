{include file="header.tpl" body_id="home"}

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
    <br>
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
            {Localisation::getTranslation('index_translation_tasks')} <small>{Localisation::getTranslation('index_1')}</small>
            <a href="{urlFor name='org-search'}" class="pull-right btn btn-primary">
                <i class="icon-search icon-white"></i> {Localisation::getTranslation('common_search_for_organisations')}
            </a>
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

            {include file="tag/tags.user-tags.inc.tpl"}	
            {include file="tag/tags.top-list.inc.tpl"}
            {if isset($statsArray) && is_array($statsArray)}
                {include file="statistics.tpl"}
            {/if}
            <div id="globe" style="text-align: center">
                <br/>
                <script type="text/javascript" src="http://jh.revolvermaps.com/p.js"></script><script type="text/javascript">rm2d_ki101('7','300','150','7puikkj5km8','ff00ff',0);</script>
                <br/>
            </div>
        </div>

        <div class="pull-left" style="max-width: 70%; overflow-wrap: break-word; word-break:break-all;">

            {if isset($user)}
                <task-stream userid="{$user->getId()}"></task-stream>
            {else}
                <task-stream userid="0"></task-stream>
            {/if}

            {if !isset($user)}
                <div class="alert">
                    <p>{Localisation::getTranslation('index_6')}</p>
                    <p>{sprintf(Localisation::getTranslation('index_register_now'), {urlFor name='register'})}</p>
                </div>
            {/if}      
        </div>
    </div>
            
{include file="footer.tpl"}
