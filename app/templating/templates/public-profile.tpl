{include file='header.tpl'}

{if $user->getDisplayName() != ''}
    <div class='page-header'><h1>{$user->getDisplayName()} <small>A member of SOLAS Match</small></h1></div>
{else}
    <div class='page-header'><h1>Public Profile<small>A member of SOLAS Match</small></h1></div>
{/if}
    

<h3>Public display name:</h3><p>{$user->getDisplayName()}</p>

{if $user->getNativeLanguage() != ''} 
    <h3>Native language: </h3>
    <p>{$user->getNativeLanguage()}</p>
{/if}

{if $user->getBiography() != ''}
    <h3>Biography:</h3>
    <p>{$user->getBiography()}</p>
{/if}

{if isset($badges)}
    {if count($badges) > 0}
        <div class='page-header'><h1>Badges<small> A list of badges {$user->getDisplayName()} has earned.</small></h1></div>

        {foreach $badges as $badge }
            <h3>{$badge->getTitle()}</h3><p>{$badge->getDescription()}</p>
        {/foreach}
 
        <p>For a full list of badges go <a href='{urlFor name="badge-list"}'>here</a>.
    {/if} 
{/if}
 
{if isset($orgList)}
    {if count($orgList) > 0}
        <div class='page-header'><h1>Organisations <small>A list of organisations {$user->getDisplayName()} belongs to</small></h1></div>
 
        <ul>
        {foreach $orgList as $org}
            <li>{$org->getName()}</li>
        {/foreach}
        </ul>
    {/if}
{/if}

{include file='footer.tpl'}
