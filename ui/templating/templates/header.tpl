<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>{if isset($title)}{$title}{else}SOLAS Match{/if}</title>
	<link rel="stylesheet" type="text/css" media="all" href="{urlFor name="home"}resources/bootstrap/css/bootstrap.min.css"/>
	<link rel="stylesheet" type="text/css" media="all" href="{urlFor name="home"}resources/css/style.1.css"/>
        <link rel="stylesheet" href="{urlFor name="home"}resources/css/jquery-ui.css"/>
        <link rel="stylesheet" href="{urlFor name="home"}resources/css/solas.css"/>
        <script type="text/javascript" src="{urlFor name="home"}ui/js/jquery-1.9.0.min.js"></script>
        <script type="text/javascript" src="{urlFor name="home"}ui/js/jquery-ui.js"></script>
    
    {if isset($extra_scripts)}
        {$extra_scripts}
    {/if}

</head>
<body {if isset($body_class)}class="{$body_class}"{/if} {if isset($body_id)}id="{$body_id}"{/if}>

<div class="navbar navbar-fixed-top">
   <div class="navbar-inner">
	    <div class="container">
    	    <a class="brand" href="{urlFor name='home'}">
				SOLAS Match
		    </a>
		    <ul class="nav">
		    	<li
		    		{if isset($current_page) && $current_page == 'home'}class="active"{/if}
		    	>
		    		<a href="{urlFor name="home"}">Home</a></li>

				

		    	{if isset($user_is_organisation_member)}
			    	<li
			    		{if isset($current_page) && $current_page == 'org-dashboard'}class="active"{/if}
			    	>
			    		<a href="{urlFor name="org-dashboard"}">Dashboard</a>
			    	</li>
		    	{/if}
                {if isset($user_has_active_tasks)}
					<li {if isset($current_page) && $current_page == 'claimed-tasks'}class="active" {/if}>
						<a href="{urlFor name="claimed-tasks" options="page_no.1"}">Claimed Tasks</a>
					</li>
				{/if}
			{if isset($user)}
                {assign var="user_id" value=$user->getUserId()}
				<li {if isset($current_page) && $current_page == 'user-profile'}class="active" {/if}>
					<a href="{urlFor name="user-public-profile" options="user_id.$user_id"}">Profile</a>
				</li>
			{/if}
		    </ul>
		    <ul class="nav pull-right">
                    {if isset($userNotifications)}   
                        <li>
                            <a>Notifications:<span id="notificationCount">{$userNotifications->lenght()}</span></a>
                        </li>
                    {/if}
                    {if isset($user)}
                        
                        <li>
                            <a href="{urlFor name="user-public-profile" options="user_id.$user_id"}">
                                <img src="http://www.gravatar.com/avatar/{md5( strtolower( trim($user->getEmail())))}?s=20&r=g" alt="" />
                                   {$user->getDisplayName()}
                            </a>
                        </li>
                        <li>
                            <a href="{urlFor name="logout"}">Log out</a>
                        </li>
                    {else}
                        <li><a href="{urlFor name="register"}">Register</a></li>
                        <li><a href="{urlFor name="login"}">Log in</a></li>
                    {/if}
		    </ul>
	    </div>
    </div>
</div>

<div class="container">
