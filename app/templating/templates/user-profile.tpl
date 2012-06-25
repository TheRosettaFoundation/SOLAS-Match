{include file='header.tpl'}

{if isset($user)}
    {assign var="user_id" value=$user->getUserId()}
    {if $user->getDisplayName() != ''}
        <div class='page-header'><h1>{$user->getDisplayName()} <small>Update your user settings here</small>
    {else}
        <div class='page-header'><h1>User Profile <small>Update your user settings here</small>
    {/if}
    <a href='{urlFor name="public-profile" options="user_id.$user_id"}' class='pull-right btn btn-primary'>Public Profile</a></h1></div>
{else}
    <div class='page-header'><h1>User Profile <small>Update your user settings here</small></h1></div>
{/if}

{if isset($warning) && $warning == true }
	<p>Invalid input, please fill in all options below.</p>
{/if}

<form method='post' action='{urlFor name='user-profile'}' class='well'>
	<label for='name'>Public display name:</label>
	<input type='text' name='name' id='name' placeholder='Name' />
	<label for='nLanguage'>Native Language:</label>
	<input type='text' name='nLanguage' id='nLanguage' {if isset($language)} placeholder={$language} {/if}/>
	<label for='bio'>Biography:</label>
	<textarea name='bio' cols='40' rows='5'></textarea>
	<p>
		<button type='submit' class='btn btn-primary' name='submit'>Update</button>
	</p>
</form>

{if isset($badges)}
    {if count($badges) > 0}
        <div class='page-header'><h1>Badges<small> A list of badges you have earned</small></h1></div>

        {foreach $badges as $badge }
    	    <h3>{$badge->getTitle()}</h3>
            <p>{$badge->getDescription()}</p>
        {/foreach}

        <p>For a full list of badges go <a href='{urlFor name="badge-list"}'>here</a>.</p>
    {/if}
{/if}

{if isset($orgList)}
    {if count($orgList) > 0}
        <div class='page-header'><h1>Organisations <small>A list of organisations you belong to</small></h1></div>

        <ul>
        {foreach $orgList as $org}
            <li>{$org->getName()}</li>
        {/foreach}
        </ul>
    {/if}
{/if}

{if isset($activeJobs)}
    {if count($activeJobs) > 0}
        <div class='page-header'><h1>Active Jobs <small>A list of jobs you are currently working on</small></h1></div>

        {foreach $activeJobs as $job}
            {include file="task.summary-link.tpl" task=$job}
        {/foreach}
    {/if}
{/if}

{if isset($archivedJobs)}
    {if count($archivedJobs) > 0}
        <div class='page-header'><h1>Archived Jobs <small>A list of jobs you have worked on in the past</small></h1></div>

        {foreach $archivedJobs as $job}
            {include file="task.profile-display.tpl" task=$job}
        {/foreach}
    {/if}
{/if}

{include file='footer.tpl'}
