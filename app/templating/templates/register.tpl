{include file="header.tpl"}
<div class="page-header">
	<h1>Register on Solas Match <small>As a volunteer translator</small></h1>
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

<form method="post" action="{urlFor name="register"}" class="well">
	<label for="email">Email</label>
	<input type="text" name="email" id="email" placeholder="Your email">
	<label for="password">Password</label>
	<input type="password" name="password" id="password" placeholder="Your password">
	<p>
	<button type="submit" class="btn btn-primary" name="submit">Register</button>
</p>
</form>

{include file="footer.tpl"}
