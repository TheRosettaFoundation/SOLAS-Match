{include file='header.tpl'}

{if isset($org)}
    <div class='page-header'><h1>
    {if $org->getName() != ''}
        {$org->getName()}
    {else}
        Organisation Profile
    {/if}
    <small> An organisation on SULAS Match</small>
    </h1></div>
{/if}

<h3>Organisation Name</h3>
<p>{$org->getName()}</p>

<h3>Home Page</h3>
<p><a href='{$org->getHomePage()}'>{$org->getHomePage()}</a></p>

<h3>Biography</h3>
<p>{$org->getBiography()}</p>


{include file='footer.tpl'}
