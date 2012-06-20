{include file='header.tpl'}

<h1>User Profile</h1>

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

<h1>Badges</h1>

{if isset($badges)}

    {foreach $badges as $badge }
    	<h3>{$badge->getTitle()}</h3><p>{$badge->getDescription()}</p>
    {/foreach}

    <p>For a full list of badges go <a href='badge/list'>here</a>.

{else}

	<p>You do not have any badges to display. Try being more active to earn more badges</p>

{/if}

{include file='footer.tpl'}
