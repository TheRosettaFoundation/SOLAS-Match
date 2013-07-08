{include file='header.tpl'}

{if isset($org)}
    <div class='page-header'><h1>
    {if $org->getName() != ''}
        {$org->getname()}
    {else}
        {Localisation::getTranslation(Strings::COMMON_ORGANISATION_PROFILE)}
    {/if}
    <small>{Localisation::getTranslation(Strings::ORG_PRIVATE_PROFILE_0)}</small>
    {assign var="org_id" value=$org->getId()}
        <a href="{urlFor name="org-public-profile" options="org_id.$org_id"}" class="pull-right btn btn-primary">
            <i class="icon-list icon-white"></i> {Localisation::getTranslation(Strings::ORG_PRIVATE_PROFILE_PUBLIC_PROFILE)}
            </a>
        </h1>
    </div>
{else}
    header({urlFor name='home'});
{/if}

{include file="handle-flash-messages.tpl"}

{assign var="org_id" value=$org->getId()}
    <form method='post' action='{urlFor name='org-private-profile' options="org_id.$org_id"}' class='well'>
        <table>
            <tr valign="top" align="center"> 
                <td width="50%">
                    
                    <label for='displayName'><strong>{Localisation::getTranslation(Strings::COMMON_DISPLAY_NAME)}:</strong></label>
                    <input type='text' name='displayName' id='displayName' style="width: 80%"
                    {if $org->getName() != ''}
                       value="{$org->getName()}"
                    {else}
                        placeholder='{Localisation::getTranslation(Strings::ORG_PRIVATE_PROFILE_YOUR_ORGANISATION_NAME)}.' 
                    {/if}
                    />
                    
                    <label for='address'><strong>{Localisation::getTranslation(Strings::COMMON_ADDRESS)}:</strong></label>
                    <textarea name='address' cols='40' rows='7' style="width: 80%"
                    >{if $org->getAddress() != ''}{$org->getAddress()}{/if}</textarea>
                    
                    <label for='city'><strong>{Localisation::getTranslation(Strings::COMMON_CITY)}:</strong></label>
                    <input type='text' name='city' id='city' style="width: 80%"
                    {if $org->getCity() != ''}
                         value="{$org->getCity()}"
                    {/if}
                    />

                    <label for='country'><strong>{Localisation::getTranslation(Strings::COMMON_COUNTRY)}:</strong></label>
                    <input type='text' name='country' id='country' style="width: 80%"
                    {if $org->getCountry() != ''}
                         value="{$org->getCountry()}"
                    {/if}
                    />
                    
                </td>
                <td width="50%">
                    
                    <label for='homepage'><strong>{Localisation::getTranslation(Strings::COMMON_HOME_PAGE)}:</strong></label>
                    <input type='text' name='homepage' id='homepage' style="width: 80%"
                    {if $org->getHomePage() != 'http://'}
                        value="{$org->getHomePage()}"
                    {else}
                        value='http://'
                    {/if}
                     /> 
                    
                    <label for='email'><strong>{Localisation::getTranslation(Strings::COMMON_EMAIL)}:</strong></label>
                    <input type='text' name='email' id='email' style="width: 80%"
                    {if $org->getEmail() != ''}
                         value="{$org->getEmail()}"
                    {else}
                        placeholder='{Localisation::getTranslation(Strings::ORG_PRIVATE_PROFILE_ORGANISATIONEXAMPLECOM)}'
                    {/if}
                    />   
                    
                    <label for='biography'><strong>{Localisation::getTranslation(Strings::COMMON_BIOGRAPHY)}:</strong></label>
                    <textarea name='biography' cols='40' rows='10' style="width: 80%" 
                    {if $org->getBiography() == ''}
                        placeholder="{Localisation::getTranslation(Strings::ORG_PRIVATE_PROFILE_ENTER_ORGANISATION_BIOGRAPHY_HERE)}."
                    {/if}
                    >{if $org->getBiography() != ''}{$org->getBiography()}{/if}</textarea>
                    
                </td>
            </tr>
            <tr>                
                <td colspan="2" style="font-weight: bold; text-align: center; padding-bottom: 10px">
                    <hr/>
                    {Localisation::getTranslation(Strings::COMMON_REGIONAL_FOCUS)}:
                </td>
            </tr>  
            <tr align="center">
                <td colspan="2">
                    <table> 
                        <thead>
                            <th>{Localisation::getTranslation(Strings::COMMON_AFRICA)}</th>
                            <th>{Localisation::getTranslation(Strings::COMMON_ASIA)}</th>
                            <th>{Localisation::getTranslation(Strings::COMMON_AUSTRALIA)}</th>
                            <th>{Localisation::getTranslation(Strings::COMMON_EUROPE)}</th>
                            <th>{Localisation::getTranslation(Strings::COMMON_NORTH_AMERICA)}</th>
                            <th>{Localisation::getTranslation(Strings::COMMON_SOUTH_AMERICA)}</th>                       
                        </thead>
                        <tr align="center">
                            <td style="width: 15%"><input id="africa" name="africa" type="checkbox" {if strstr($org->getRegionalFocus(), "Africa")} checked {/if} /></td>   
                            <td style="width: 15%"><input id="asia" name="asia" type="checkbox" {if strstr($org->getRegionalFocus(), "Asia")} checked {/if} /></td> 
                            <td style="width: 15%"><input id="australia" name="australia" type="checkbox" {if strstr($org->getRegionalFocus(), "Australia")} checked {/if} /></td> 
                            <td style="width: 15%"><input id="europe" name="europe" type="checkbox" {if strstr($org->getRegionalFocus(), "Europe")} checked {/if}/></td> 
                            <td style="width: 15%"><input id="northAmerica" name="northAmerica" type="checkbox" {if strstr($org->getRegionalFocus(), "North-America")} checked {/if} /></td> 
                            <td style="width: 15%"><input id="southAmerica" name="southAmerica" type="checkbox" {if strstr($org->getRegionalFocus(), "South-America")} checked {/if} /></td> 
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
                        <i class="icon-refresh icon-white"></i> {Localisation::getTranslation(Strings::ORG_PRIVATE_PROFILE_UPDATE_ORGANISATION_DETAILS)}
                    </button>
                    {if isset($orgAdmin)}
                        <button type="submit" class="btn btn-inverse" value="{$org_id}" name="deleteId"
                                onclick="return confirm('{Localisation::getTranslation(Strings::ORG_PRIVATE_PROFILE_1)}');"> 
                            <i class="icon-fire icon-white"></i> {Localisation::getTranslation(Strings::ORG_PRIVATE_PROFILE_DELETE_ORGANISATION)}
                        </button>
                    {/if}
                </td>
            </tr>
  
        </table>
    </form>    



{include file='footer.tpl'}
