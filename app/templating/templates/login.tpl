{include file="header.tpl"}
<div class="page-header">
	<h1>Log In <small>to Solas Match</small></h1>
</div>

<form method="post" action="{urlFor name='login'}">
	<label for="email">Email</label>
	<input type="text" name="email" id="email">
	<label for="password">Password</label>
	<input type="password" name="password" id="password">
	<p>
		<button type="submit" class="btn btn-primary" name="submit">Log in</button>
	</p>
</form>

{include file="footer.tpl"}
