{include file="header.tpl"}
<div class="page-header">
	<h1>Register on Solas Match <small>As a volunteer translator.</small></h1>
</div>

{if isset($error)}
	<div class="alert alert-error">
		<strong>Error</strong> {$error}
	</div>
{/if}
{if isset($warning)}
	<div class="alert">
		<strong>Warning</strong> {$warning}
	</div>
{/if}
{if isset($openid)&& ($openid==='n'||$openid==='h' )}
<form method="post" action="{urlFor name="register"}" class="well">
	<label for="email"><strong>Email:</strong></label>
	<input type="text" name="email" id="email" placeholder="Your email"/>
	<label for="password"><strong>Password:</strong></label>
	<input type="password" name="password" id="password" placeholder="Your password"/>
	<p>
	<button type="submit" class="btn btn-success" name="submit">
            <i class="icon-star icon-white"></i> Register
        </button>
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
