{include file="header.tpl"}

    <div class="page-header">
            <h1>Log In To {Settings::get('site.title')}.</h1>
    </div>

    {if isset($flash['error'])}
        <div class="alert alert-error">
            <a class="close" data-dismiss="alert" href="{urlFor name='login'}">×</a>
            <p><strong>Warning! </strong>{$flash['error']}</p>
        </div>
    {/if}

    {if isset($flash['info'])}
        <div class="alert alert-info">
            <a class="close" data-dismiss="alert" href="{urlFor name='login'}">×</a>
            <p><strong>NOTE: </strong>{$flash['info']}</p>
        </div>
    {/if}

    {if isset($flash['success'])}
        <div class="alert alert-success">
            <a class="close" data-dismiss="alert" href="{urlFor name='login'}">×</a>
            <p><strong>NOTE: </strong>{$flash['success']}</p>
        </div>
    {/if}

    {if isset($openid)&& ($openid==='n'||$openid==='h' )}
        <form method="post" action="{urlFor name='login'}">
            <label for="email"><strong>Email:</strong></label>
            <input type="text" name="email" id="email"/>
            <label for="password"><strong>Password:</strong></label>
            <input type="password" name="password" id="password"/>
            <p>
                <input type="submit" class="btn btn-primary" name="login" value="   Log In" />
                <input type="submit" class="btn btn-inverse" name="password_reset" value="   Reset Password" />
                <i class="icon-share icon-white" style="position:relative; right:200px;top:2px;"></i>
                <i class="icon-exclamation-sign icon-white" style="position:relative; right:145px; top:2px;"></i>        
            </p>
        </form>
    {/if}

    {if isset($openid)&& ($openid==='y'||$openid==='h' )}
        <!-- Simple OpenID Selector -->
        <form action="{urlFor name='login'}" method="post" id="openid_form">
            <input type="hidden" name="action" value="verify" />
            <fieldset>
                <legend>Sign-in or Create New Account</legend>
                <div id="openid_choice">
                    <p>Please click your account provider:</p>
                    <div id="openid_btns"></div>
                </div>
                <div id="openid_input_area">
                    <input id="openid_identifier" name="openid_identifier" type="text" />
                    <input id="openid_submit" type="submit" class="btn btn-primary" value="Sign-In"/>
                </div>
                <noscript>
                    <p>OpenID is service that allows you to log-on to many different websites using a single indentity.
                    Find out <a href="http://openid.net/what/">more about OpenID</a> and <a href="http://openid.net/get/">how to get an OpenID enabled account</a>.</p>
                </noscript>
            </fieldset>
        </form>
        <!-- /Simple OpenID Selector -->
    {/if}
{include file="footer.tpl"}
