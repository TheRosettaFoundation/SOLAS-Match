{include file='header.tpl'}

{if isset($org)}
    <div class='page-header'><h1>
    {if $org->getName() != ''}
        {$org->getname()}
    {else}
        {Localisation::getTranslation('common_organisation_profile')}
    {/if}
    <small>{Localisation::getTranslation('org_private_profile_0')}</small>
    {assign var="org_id" value=$org->getId()}
        <a href="{urlFor name="org-public-profile" options="org_id.$org_id"}" class="pull-right btn btn-primary">
            <i class="icon-list icon-white"></i> {Localisation::getTranslation('org_private_profile_public_profile')}
            </a>
        </h1>
    </div>
{else}
    header({urlFor name='home'});
{/if}

{include file="handle-flash-messages.tpl"}

{if isset($errorOccured)}
    <tr>
        <td colspan="2">
            <div class="alert alert-error">
                <h3>{Localisation::getTranslation('common_please_correct_errors')}</h3>
                <ol>
                {foreach from=$errorList item=error}
                    <li>{$error}</li>
                {/foreach}
                </ol>
            </div> 
        </td>
    </tr>
{/if}
{assign var="org_id" value=$org->getId()}
    <form method='post' action='{urlFor name='org-private-profile' options="org_id.$org_id"}' class='well' accept-charset="utf-8">
        <table>
            <tr valign="top" align="center"> 
                <td width="50%">         
                    <label for='displayName'><strong>{Localisation::getTranslation('common_display_name')}</strong></label>
                    <input type='text' name='displayName' id='displayName' style="width: 80%"
                    {if $org->getName() != ''}
                       value="{$org->getName()}"
                    {else}
                        placeholder='{Localisation::getTranslation('org_private_profile_your_organisation_name')}' 
                    {/if}
                    />
                    
                    <label for='address'><strong>{Localisation::getTranslation('common_address')}</strong></label>
                    <textarea name='address' cols='40' rows='7' style="width: 80%">
                    {if $org->getAddress() != ''}{TemplateHelper::uiCleanseNewlineAndTabs($org->getAddress())}{/if}</textarea>
                    
                    <label for='city'><strong>{Localisation::getTranslation('common_city')}</strong></label>
                    <input type='text' name='city' id='city' style="width: 80%"
                    {if $org->getCity() != ''}
                         value="{$org->getCity()}"
                    {/if}
                    />

                    <label for='country'><strong>{Localisation::getTranslation('common_country')}</strong></label>
                    <input type='text' name='country' id='country' style="width: 80%"
                    {if $org->getCountry() != ''}
                         value="{$org->getCountry()}"
                    {/if}
                    />
                    
                </td>
                <td width="50%">
                    
                    <label for='homepage'><strong>{Localisation::getTranslation('common_home_page')}</strong></label>
                    <input type='text' name='homepage' id='homepage' style="width: 80%"
                    {if $org->getHomePage() != 'http://'}
                        value="{$org->getHomePage()}"
                    {else}
                        value='http://'
                    {/if}
                     /> 
                    
                    <label for='email'><strong>{Localisation::getTranslation('common_email')}</strong></label>
                    <input type='text' name='email' id='email' style="width: 80%"
                    {if $org->getEmail() != ''}
                         value="{$org->getEmail()}"
                    {else}
                        placeholder='{Localisation::getTranslation('org_private_profile_organisationexamplecom')}'
                    {/if}
                    />   
                    
                    <label for='biography'><strong>{Localisation::getTranslation('common_biography')}</strong></label>
                    <textarea name='biography' cols='40' rows='10' style="width: 80%" 
                    {if $org->getBiography() == ''}
                        placeholder="{Localisation::getTranslation('org_private_profile_enter_organisation_biography_here')}"
                    {/if}
                    >{if $org->getBiography() != ''}{TemplateHelper::uiCleanseNewlineAndTabs($org->getBiography())}{/if}</textarea>
                    
                </td>
            </tr>
            <tr>                
                <td colspan="2" style="font-weight: bold; text-align: center; padding-bottom: 10px">
                    <hr/>
                    {Localisation::getTranslation('common_regional_focus')}
                </td>
            </tr>  
            <tr align="center">
                <td colspan="2">
                    <table> 
                        <thead>
                            <th>{Localisation::getTranslation('common_africa')}</th>
                            <th>{Localisation::getTranslation('common_asia')}</th>
                            <th>{Localisation::getTranslation('common_australia')}</th>
                            <th>{Localisation::getTranslation('common_europe')}</th>
                            <th>{Localisation::getTranslation('common_north_america')}</th>
                            <th>{Localisation::getTranslation('common_south_america')}</th>                       
                        </thead>
                        <tr align="center">
                            <td style="width: 15%"><input id="africa" name="africa" type="checkbox" {if strstr($org->getRegionalFocus(), "Africa")} checked="true" {/if} /></td>   
                            <td style="width: 15%"><input id="asia" name="asia" type="checkbox" {if strstr($org->getRegionalFocus(), "Asia")} checked="true" {/if} /></td> 
                            <td style="width: 15%"><input id="australia" name="australia" type="checkbox" {if strstr($org->getRegionalFocus(), "Australia")} checked="true" {/if} /></td> 
                            <td style="width: 15%"><input id="europe" name="europe" type="checkbox" {if strstr($org->getRegionalFocus(), "Europe")} checked="true" {/if}/></td> 
                            <td style="width: 15%"><input id="northAmerica" name="northAmerica" type="checkbox" {if strstr($org->getRegionalFocus(), "North-America")} checked="true" {/if} /></td> 
                            <td style="width: 15%"><input id="southAmerica" name="southAmerica" type="checkbox" {if strstr($org->getRegionalFocus(), "South-America")} checked="true" {/if} /></td> 
                        </tr>
                      
                    </table> 
                </td>
            </tr>
            <tr>                
                <td colspan="2" style="padding-bottom: 20px"><hr/></td>
            </tr>
            <tr>
                <td colspan="2" align="center">
                    <button type='submit' class='btn btn-primary' name='updateOrgDetails'>
                        <i class="icon-refresh icon-white"></i> {Localisation::getTranslation('org_private_profile_update_organisation_details')}
                    </button>
                    {if isset($orgAdmin)}
                        <button type="submit" class="btn btn-inverse" value="{$org_id}" name="deleteId"
                                onclick="return confirm('{Localisation::getTranslation('org_private_profile_1')}');"> 
                            <i class="icon-fire icon-white"></i> {Localisation::getTranslation('org_private_profile_delete_organisation')}
                        </button>
                    {/if}
                </td>
            </tr>
  
        </table>
    </form>    



{include file='footer.tpl'}
