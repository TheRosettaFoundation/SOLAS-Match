{include file="header.tpl"}

<div class="page-header">
    <h1>
        Organisation Search <small>Search for organisations by name.</small>
    </h1>
</div>

<form class="well" method="post" action="{urlFor name="org-search"}">
    <label for="search_name">Organisation Name:</label>
    <input type="text" name="search_name" id="search_name" style="height: 20px" />
    <br />
    <input type="submit" name="submit" value="    Search" class="btn btn-primary" />
    <i class="icon-search icon-white" style="position:relative; right:75px; top:2px;"></i>
</form>

{if isset($found_orgs)}
    {assign var="org_count" value=count($found_orgs)}
    <p>
        <h3>Search Results: Found {$org_count} match(es)</h3>    
    </p>
    {foreach $found_orgs as $org}
        <div class="row">
            {assign var="org_id" value=$org->getId()}
            <div class="span8">
                <h3>
                    <i class="icon-briefcase"></i>
                    <a href="{urlFor name="org-public-profile" options="org_id.$org_id"}">{$org->getName()}</a>
                </h3>
                <br/>
            </div>
            <div class="span8">
                <p>
                    <b>Biography:</b><br/>

                    {if $org->getBiography() == ''}
                        This organisation does not have a biography listed.
                    {else}                            
                        {$org->getBiography()}
                    {/if}
                </p>
                <p>
                <b>Home Page:</b><br/>
                {if $org->getHomePage() != "http://"}
                    <a target="_blank" href="{$org->getHomePage()}">{$org->getHomePage()}</a>
                {else}
                    This organisation does not have a web site listed.
                {/if}
                </p>
            </div>
        </div>
        <p style="margin-bottom:20px;"></p>
        <hr>
    {/foreach}
{/if}

{if isset($flash['error'])}
    <div class="alert alert-error">
        <p><b>Warning! </b>{$flash['error']}</p>
    </div>
{/if}

{include file="footer.tpl"}
