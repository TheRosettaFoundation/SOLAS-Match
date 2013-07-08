{include file="header.tpl"}

    <div class="page-header">
        <h1>
            {Localisation::getTranslation(Strings::ORG_SEARCH_ORGANISATION_SEARCH)} <small>{Localisation::getTranslation(Strings::ORG_SEARCH_0)}</small>
        </h1>
    </div>

    <form class="well" method="post" action="{urlFor name="org-search"}">
        <label for="search_name"><strong>{Localisation::getTranslation(Strings::COMMON_ORGANISATION_NAME)}:</strong></label>
        <input type="text" name="search_name" id="search_name" style="height: 20px" 
                value="{if isset($searchedText)}{$searchedText}{/if}" />
        <br />
        <input type="submit" name="submit" value="    {Localisation::getTranslation(Strings::ORG_SEARCH_SEARCH)}" class="btn btn-primary" />
        <i class="icon-search icon-white" style="position:relative; right:75px; top:2px;"></i>
        <input type="submit" name="allOrgs" value="    {Localisation::getTranslation(Strings::COMMON_LIST_ALL)}" class="btn btn-inverse" />
        <i class="icon-list icon-white" style="position:relative; right:75px; top:2px;"></i>
    </form>

{if isset($foundOrgs)}
    {assign var="org_count" value=count($foundOrgs)}
    {if $org_count > 0}
        <p>
            <h3>{Localisation::getTranslation(Strings::ORG_SEARCH_SEARCH_RESULTS)}: {Localisation::getTranslation(Strings::ORG_SEARCH_FOUND)} {$org_count} {Localisation::getTranslation(Strings::ORG_SEARCH_MATCHES)}</h3>    
        </p>
            {foreach $foundOrgs as $org}
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
                        <strong>{Localisation::getTranslation(Strings::COMMON_BIOGRAPHY)}:</strong><br/>

                        {if $org->getBiography() == ''}
                            {Localisation::getTranslation(Strings::ORG_PUBLIC_PROFILE_NO_BIOGRAPHY_LISTED)}.
                        {else}                            
                            {$org->getBiography()}
                        {/if}
                    </p>
                    <p>
                    <strong>{Localisation::getTranslation(Strings::COMMON_HOME_PAGE)}:</strong><br/>
                    {if $org->getHomePage() != "http://"}
                        <a target="_blank" href="{$org->getHomePage()}">{$org->getHomePage()}</a>
                    {else}
                        {Localisation::getTranslation(Strings::ORG_PUBLIC_PROFILE_NO_HOME_PAGE_LISTED)}.
                    {/if}
                    </p>
                </div>
            </div>
            <p style="margin-bottom:20px;"/>
            <hr/>
        {/foreach}
    {/if}
{/if}

{if isset($flash['error'])}
    <div class="alert alert-error">
        <p>{$flash['error']}</p>
    </div>
{/if}

{include file="footer.tpl"}
