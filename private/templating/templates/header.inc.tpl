<!DOCTYPE html>
<html lang="en">  
<head>  
	<meta charset="utf-8">  
	<title>{if isset($title)}{$title}{else}Translation eXchange{/if}</title>
	<link rel="stylesheet" type="text/css" media="all" href="/assets/css/reset.css">
	<link rel="stylesheet" type="text/css" media="all" href="/assets/css/style.1.css">
	<link rel="stylesheet" type="text/css" media="all" href="/assets/css/960.css">
</head>  
<body {if isset($body_class)}class="{$body_class}"{/if}>
<div class="container_12"><!-- setting up a grid of 12 columns -->
	<div id="header" class="grid_12">
		<h1><a href="/">Rosetta Translation eXchange</a></h1>
		<div id="header-right">
			{if $s->users->currentUserID() !== false}
				{$s->users->userEmail($s->users->currentUserID())}
				&middot; <a href="{$s->url->logout()}">Log out</a> 
			{else}
				<a href="{$s->url->login()}">Log in</a>
				&middot; <a href="{$s->url->register()}">Register</a>
			{/if}
		</div>
		<!--
		<ul class="nav">
			<li><a href="index.html" class="active">Contribute</a></li>
			<li><a href="tags.html">Tags</a></li>
			<li><a href="organizations.html">Organisations</a></li>
		</ul>
		-->
		<div id="tagline">
			NGOs need your help to help translate and review their content.
	 	</div>
	</div>
