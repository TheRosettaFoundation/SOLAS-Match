{include file='header.tpl'}

{if isset($user)}
    <div class="page-header"><h1>
    <img src="http://www.gravatar.com/avatar/{md5( strtolower( trim($user->getEmail())))}?s=80&r=g" alt="" />
    {if $user->getDisplayName() != ''}
        {$user->getDisplayName()}
    {else}
        Public Profile
    {/if}
    <small>A member of SOLAS Match</small>
    </h1></div>
{/if}
    

<h3>Public display name:</h3>
<p>{$user->getDisplayName()}</p>

{if $user->getNativeLanguage() != ''} 
    <h3>First Maternal Language: </h3>
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
