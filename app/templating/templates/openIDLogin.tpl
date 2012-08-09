{include file="header.tpl"}
<script type="text/javascript" src="{urlFor name="home"}resources/bootstrap/js/jquery-1.2.6.min.js"></script>
<script type="text/javascript" src="{urlFor name="home"}resources/bootstrap/js/openid-jquery.js"></script>
<script type="text/javascript" src="{urlFor name="home"}resources/bootstrap/js/openid-en.js"></script>
<link type="text/css" rel="stylesheet" href="{urlFor name="home"}resources/css/openid.css" />
<div class="page-header">
	<h1>Log In <small>to Solas Match</small></h1>
</div>
{if isset($flash['error'])}
    <div class="alert alert-error">
        <a class="close" data-dismiss="alert" href="{urlFor name='login'}">×</a>
        <p><strong>Warning! </strong>{$flash['error']}</p>
    </div>
{/if}

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
<script type="text/javascript">
		$(window).load(function() {
			openid.init('openid_identifier');
			openid.setDemoMode(false); //Stops form submission for client javascript-only test purposes
		});
	</script>
{include file="footer.tpl"}
