{include file="header.tpl"}

    <div class="page-header">
        <h1>
            {Localisation::getTranslation('org_search_organisation_search')} <small>{Localisation::getTranslation('org_search_0')}</small>
        </h1>
    </div>

    <form class="well" method="post" action="{urlFor name="org-search"}" accept-charset="utf-8">
        <label for="search_name"><strong>{Localisation::getTranslation('common_organisation_name')}</strong></label>
        <input type="text" name="search_name" id="search_name" style="height: 20px" 
                value="{if isset($searchedText)}{$searchedText}{/if}" />
        <br />
                
        <button type="submit" name="submit" class="btn btn-primary">
  		    <i class="icon-search icon-white"></i> {Localisation::getTranslation('org_search_search')}
		</button>
		
		<button type="submit" name="allOrgs" class="btn btn-inverse">
  		   <i class="icon-list icon-white"></i> {Localisation::getTranslation('common_list_all')}
		</button>
    </form>

{if isset($foundOrgs)}
    {assign var="org_count" value=count($foundOrgs)}
    {if $org_count > 0}
        <p>
            <h3>{sprintf(Localisation::getTranslation('org_search_search_results'), $org_count)}</h3>    
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
                        <strong>{Localisation::getTranslation('common_biography')}</strong><br/>

                        {if $org->getBiography() == ''}
                            {Localisation::getTranslation('org_public_profile_no_biography_listed')}
                        {else}                            
                            {TemplateHelper::uiCleanseNewlineAndTabs($org->getBiography())}
                        {/if}
                    </p>
                    <p>
                    <strong>{Localisation::getTranslation('common_home_page')}</strong><br/>
                    {if $org->getHomePage() != "http://" && $org->getHomePage() != ''}
                        <a target="_blank" href="{$org->getHomePage()}">{$org->getHomePage()}</a>
                    {else}
                        {Localisation::getTranslation('org_public_profile_no_home_page_listed')}
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
