{include file='header.tpl'}

<div class='page-header'><h1>User Profile <small>Update your user settings here</small></h1></div>

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

{if isset($user)}
    {assign var="user_id" value=$user->getUserId()}
    <p>To view your public profile click <a href="{urlFor name="public-profile" options="user_id.$user_id"}">here</a></p>
{/if}

<div class='page-header'><h1>Badges<small> A list of badges you have attained</small></h1></div>

{if isset($badges)}

    {foreach $badges as $badge }
    	<h3>{$badge->getTitle()}</h3><p>{$badge->getDescription()}</p>
    {/foreach}

    <p>For a full list of badges go <a href='{urlFor name="badge-list"}'>here</a>.

{else}

	<p>You do not have any badges to display. Try being more active to earn more badges</p>

{/if}

<div class='page-header'><h1>Organisations <small>A list of organisations you belong to</small></h1></div>

{if isset($orgList)}
    <ul>
    {foreach $orgList as $org}
        <li>{$org->getName()}</li>
    {/foreach}
    </ul>
{/if}


{include file='footer.tpl'}
