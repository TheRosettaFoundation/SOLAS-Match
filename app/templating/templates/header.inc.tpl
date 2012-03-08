<!DOCTYPE html>
<html lang="en">  
<head>  
	<meta charset="utf-8">  
	<title>{if isset($title)}{$title}{else}Solas Match{/if}</title>

	<link rel="stylesheet" type="text/css" media="all" href="/resources/css/bootstrap.min.css">

	<!--<link rel="stylesheet" type="text/css" media="all" href="/resources/css/reset.css">
	<link rel="stylesheet" type="text/css" media="all" href="/resources/css/style.1.css">
	<link rel="stylesheet" type="text/css" media="all" href="/resources/css/960.css">-->
</head>  
<body {if isset($body_class)}class="{$body_class}"{/if}>
<div class="container_12"><!-- setting up a grid of 12 columns -->
	<div id="header" class="grid_12">
		<h1><a href="/">Solas Match</a></h1>
		<div id="header-right">
			{if isset($user)}
				{$user->getEmail()}
				&middot; <a href="{$url_logout}">Log out</a> 
			{else}
				<a href="{$url_login}">Log in</a>
				&middot; <a href="{$url_register}">Register</a>
			{/if}
		</div>
		<div id="tagline">
			NGOs need your help to help translate and review their content.
	 	</div>
	</div>
