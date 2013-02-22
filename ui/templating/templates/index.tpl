{include file="header.tpl" body_id="home"}

{if !isset($user)}
	<div class="hero-unit">
		<h1>Community translation.</h1>
		<p>Facilitating conversation in global communities.</p>
		<p>
			<a class="btn btn-success btn-large" href="{urlFor name="register"}">
				<i class="icon-star icon-white"></i> Register to Volunteer
			</a>
			<a class="btn btn-primary btn-large" href="{urlFor name="login"}">
				<i class="icon-share icon-white"></i> Login
			</a>
		</p>
	</div>
{/if}

{if isset($flash['error'])}
    <div class="alert alert-error">
        <a class="close" data-dismiss="alert" href="{urlFor name='home'}">×</a>
        <p><strong>Warning! </strong>{$flash['error']}</p>
    </div>
{/if}

{if isset($flash['info'])}
    <div class="alert alert-info">
        <a class="close" data-dismiss="alert" href="{urlFor name='home'}">×</a>
        <p><strong>NOTE: </strong>{$flash['info']}</p>
    </div>
{/if}

{if isset($flash['success'])}
    <div class="alert alert-success">
        <a class="close" data-dismiss="alert" href="{urlFor name='home'}">×</a>
        <p><strong>Success! </strong>{$flash['success']}</p>
    </div>
{/if}

<div class="page-header">
	<h1>
            Translation tasks <small>Claim a task, translate it, upload it.</small>
            <a href="{urlFor name='org-search'}" class="pull-right btn btn-primary">
                <i class="icon-search icon-white"></i> Search for Organisations
            </a>
        </h1>
</div>
                
<div class="row">
    <div class="pull-right">

            {include file="tags.user-tags.inc.tpl"}	
            {include file="tags.top-list.inc.tpl"}
            {if is_array($statsArray)}
                    {include file="statistics.tpl"}
            {/if}
    </div>
    
    <div class="pull-left">
        {if count($tasks) > 0}
                <div id="tasks">
                        {foreach from=$tasks item=task name=tasks_loop}
                                {include file="task.summary-link.tpl" task=$task}
                        {/foreach}
                </div>
        {else}
                <div class="alert alert-warning">
                        {if isset($user_is_organisation_member)}
                                <strong>No open tasks!</strong> You can upload a new task from your Dashboard in the navigation menu above.
                        {else}
                                <strong>No tasks available!</strong> Please wait for organisations to upload more translation tasks.
                        {/if}
                </div>
        {/if}

        {if !isset($user)}
            <div class="alert">
                <p>Help us match you with the most suitable translation tasks.</p>
                <p><a href={urlFor name="register"}>Register now</a> to find the translation tasks best suited to you.</p>
            </div>
        {/if}      
    </div>
</div>

<div class="row">
{include file="footer.tpl"}
</div>