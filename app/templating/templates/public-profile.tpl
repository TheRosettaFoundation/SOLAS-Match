{include file='header.tpl'}

<div class='page-header'><h1>Public Profile <small>View info about other users of Solas Match</small></h1></div>

<h3>Public display name:</h3><p>{$user->getDisplayName()}</p>

<h3>Native language: </h3>
<p>
{if $user->getNativeLanguage() != ''} 
    {$user->getNativeLanguage()} 
{else}
    This user has not specified a native language. {$user->getNativeLanguage()}
{/if}
</p>

<h3>Biography:</h3>
<p>
{if $user->getBiography() != ''}
    {$user->getBiography()}
{else}
    This user has not given a biography
{/if}
</p>


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
