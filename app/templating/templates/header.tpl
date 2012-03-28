<!DOCTYPE html>
<html lang="en">  
<head>  
	<meta charset="utf-8">  
	<title>{if isset($title)}{$title}{else}Solas Match{/if}</title>

	<link rel="stylesheet" type="text/css" media="all" href="/resources/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" media="all" href="/resources/css/style.1.css">
	<!--<link rel="stylesheet" type="text/css" media="all" href="/resources/css/reset.css">
	<link rel="stylesheet" type="text/css" media="all" href="/resources/css/style.1.css">
	<link rel="stylesheet" type="text/css" media="all" href="/resources/css/960.css">-->
</head>  
<body {if isset($body_class)}class="{$body_class}"{/if}>

<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
	    <div class="container">
    	    <a class="brand" href="/">
				Solas Match
		    </a>
		    <ul class="nav">
		    	<li><a href="{urlFor name="home"}">Home</a></li>
		    	{if isset($user_is_organisation_member)}
			    	<li><a href="{urlFor name="client-dashboard"}">Dashboard</a></li>
		    	{/if}
		    </ul>
		    <ul class="nav pull-right">
		    	{if isset($user)}
					<li><a href="{$url_logout}">Log out</a></li>
				{else}
					<li><a href="{$url_register}">Register</a></li>
					<li><a href="{$url_login}">Log in</a></li>
				{/if}
		    </ul>
	    </div>
    </div>
</div>

<div class="container">
