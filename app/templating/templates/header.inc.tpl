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

<div class="navbar">
    <div class="navbar-inner">
	    <div class="container">
	    	    <a class="brand" href="/">
					Solas Match
			    </a>
			    <ul class="nav pull-right">
			    	{if isset($user)}
						<li><a href="{$url_logout}">Log out</a></li>
					{else}
						<li><a href="{$url_login}">Log in</a></li>
						<li><a href="{$url_register}">Register</a></li>
					{/if}
			    </ul>
	    </div>
    </div>
    
</div>

<div class="container">
