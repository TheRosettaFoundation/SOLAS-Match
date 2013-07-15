{include file="header.tpl"}

<h1 class="page-header">
    {Localisation::getTranslation(Strings::CREATE_ORG_CREATE_AN_ORGANISATION)}
    <small>
        {Localisation::getTranslation(Strings::CREATE_ORG_CREATE_YOUR_OWN_ORGANISATION)}
    </small><br/>
    <small>
        {Localisation::getTranslation(Strings::COMMON_NOTE)}:
        <span style="color: red">*</span>
        {Localisation::getTranslation(Strings::COMMON_DENOTES_A_REQUIRED_FIELD)}.
    </small>
</h1>

    <form method='post' action="{urlFor name="create-org"}" class='well'>
        <table>
            {if isset($nameErr)}
                <tr>
                    <td colspan="2">
                        <div class="alert alert-error">
                            <h3>{Localisation::getTranslation(Strings::COMMON_PLEASE_FILL_IN_ALL_REQUIRED_FIELDS)}:</h3>
                            <ol>
                                <li>{$nameErr}</li>
                            </ol>
                        </div> 
                    </td>
                </tr>
             {/if}
            <tr valign="top" align="center"> 
                <td width="50%">
                    
                    <label for='orgName'><strong>{Localisation::getTranslation(Strings::COMMON_ORGANISATION_NAME)}: <span style="color: red">*</span></strong></label>
                    <input type='text' name='orgName' id='orgName' style="width: 80%" {if isset($org)} value="{$org->getName()}" {/if}/>
                    
                    <label for='address'><strong>{Localisation::getTranslation(Strings::COMMON_ADDRESS)}:</strong></label>
                    <textarea name='address' cols='40' rows='7' style="width: 80%">{if isset($org)} {$org->getAddress()} {/if}</textarea>
                    
                    <label for='city'><strong>{Localisation::getTranslation(Strings::COMMON_CITY)}:</strong></label>
                    <input type='text' name='city' id='city' style="width: 80%" {if isset($org)} value="{$org->getCity()}" {/if}/>

                    <label for='country'><strong>{Localisation::getTranslation(Strings::COMMON_COUNTRY)}:</strong></label>
                    <input type='text' name='country' id='country' style="width: 80%" {if isset($org)} value="{$org->getCountry()}" {/if}/>
                    
                </td>
                <td width="50%">
                    
                    <label for='homepage'><strong>{Localisation::getTranslation(Strings::COMMON_HOME_PAGE)}:</strong></label>
                    <input type='text' name='homepage' id='homepage' style="width: 80%" {if isset($org)} value="{$org->getHomePage()}" {/if}/> 
                    
                    <label for='email'><strong>{Localisation::getTranslation(Strings::COMMON_EMAIL)}:</strong></label>
                    <input type='text' name='email' id='email' style="width: 80%"{if isset($org)} value="{$org->getEmail()}" {/if}/>   
                    
                    <label for='biography'><strong>{Localisation::getTranslation(Strings::COMMON_BIOGRAPHY)}:</strong></label>
                    <textarea name='biography' cols='40' rows='10' style="width: 80%">{if isset($org)} {TemplateHelper::uiCleanseNewlineAndTabs({$org->getBiography()})} {/if}</textarea>
                    
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
                            <td style="width: 15%"><input id="africa" name="africa" type="checkbox" {if isset($org)} {if strstr($org->getRegionalFocus(), "Africa")} checked {/if}{/if} /></td>   
                            <td style="width: 15%"><input id="asia" name="asia" type="checkbox" {if isset($org)} {if strstr($org->getRegionalFocus(), "Asia")} checked {/if}{/if} /></td> 
                            <td style="width: 15%"><input id="australia" name="australia" type="checkbox" {if isset($org)} {if strstr($org->getRegionalFocus(), "Australia")} checked {/if}{/if} /></td> 
                            <td style="width: 15%"><input id="europe" name="europe" type="checkbox" {if isset($org)} {if strstr($org->getRegionalFocus(), "Europe")} checked {/if}{/if} /></td> 
                            <td style="width: 15%"><input id="northAmerica" name="northAmerica" type="checkbox" {if isset($org)} {if strstr($org->getRegionalFocus(), "North-America")} checked {/if}{/if} /></td> 
                            <td style="width: 15%"><input id="southAmerica" name="southAmerica" type="checkbox" {if isset($org)} {if strstr($org->getRegionalFocus(), "South-America")} checked {/if}{/if} /></td> 
                        </tr>
                      
                    </table> 
                </td>
            </tr>
            <tr>                
                <td colspan="2" style="padding-bottom: 20px"><hr/></td>
            </tr>
            <tr>
                <td colspan="2" align="center">
                    <button type="submit" name="submit" value="createOrg" class="btn btn-success">
                        <i class="icon-star icon-white"></i> {Localisation::getTranslation(Strings::COMMON_CREATE_ORGANISATION)}
                    </button>
                </td>
            </tr>
  
        </table>
    </form>

{include file="footer.tpl"}