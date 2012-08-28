{include file="header.tpl"}

<div class="page-header">
    <h1>
        Organisation Search <small>Search for organisation by name</small>
    </h1>
</div>

<form class="well" method="post" action="{urlFor name="org-search"}">
    <label for="search_name">Organisation Name:</label>
    <input type="text" name="search_name" id="search_name" style="height: 20px" />
    <br />
    <input type="submit" name="submit" value="Search" class="btn btn-primary" />
</form>

{if isset($found_orgs)}
    <h3>Search Results:</h3>
    {foreach $found_orgs as $org}
        {assign var="org_id" value=$org->getId()}
        <h4><a href="{urlFor name="org-public-profile" options="org_id.$org_id"}">{$org->getName()}</a></h4>
        {if $org->getBiography() != ''}
            <p>{$org->getBiography()}</p>
        {/if}
        <br />
    {/foreach}
{/if}

{if isset($flash['error'])}
    <div class="alert alert-error">
        <p><b>Warning! </b>{$flash['error']}</p>
    </div>
{/if}

{include file="footer.tpl"}
