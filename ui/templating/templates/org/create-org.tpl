{include file="header.tpl"}

<h1 class="page-header">
    {Localisation::getTranslation('create_org_create_an_organisation')}
    <small>
        {Localisation::getTranslation('create_org_create_your_own_organisation')}
    </small><br/>
    <small>
        {Localisation::getTranslation('common_denotes_a_required_field')}
    </small>
</h1>

    <form method='post' action="{urlFor name="create-org"}" class='well' accept-charset="utf-8">
        <table>
            {if isset($nameErr)}
                <tr>
                    <td colspan="2">
                        <div class="alert alert-error">
                            <h3>{Localisation::getTranslation('common_please_correct_errors')}</h3>
                            <ol>
                                <li>{$nameErr} {Localisation::getTranslation('common_invalid_characters')}</li>
                            </ol>
                        </div> 
                    </td>
                </tr>
             {/if}
            <tr valign="top" align="center"> 
                <td width="50%">
                    
                    <label for='orgName'><strong>{Localisation::getTranslation('common_organisation_name')} <span style="color: red">*</span></strong></label>
                    <input type='text' name='orgName' id='orgName' style="width: 80%" {if isset($org)} value="{$org->getName()}" {/if}/>
                    
                    <label for='address'><strong>{Localisation::getTranslation('common_address')}</strong></label>
                    <textarea name='address' cols='40' rows='7' style="width: 80%">{if isset($org)} {TemplateHelper::uiCleanseNewlineAndTabs($org->getAddress())} {/if}</textarea>
                    
                    <label for='city'><strong>{Localisation::getTranslation('common_city')}</strong></label>
                    <input type='text' name='city' id='city' style="width: 80%" {if isset($org)} value="{$org->getCity()}" {/if}/>

                    <label for='country'><strong>{Localisation::getTranslation('common_country')}</strong></label>
                    <input type='text' name='country' id='country' style="width: 80%" {if isset($org)} value="{$org->getCountry()}" {/if}/>
                    
                </td>
                <td width="50%">
                    
                    <label for='homepage'><strong>{Localisation::getTranslation('common_home_page')}</strong></label>
                    <input type='text' name='homepage' id='homepage' style="width: 80%" {if isset($org)} value="{$org->getHomePage()}" {/if}/> 
                    
                    <label for='email'><strong>{Localisation::getTranslation('common_email')}</strong></label>
                    <input type='text' name='email' id='email' style="width: 80%"{if isset($org)} value="{$org->getEmail()}" {/if}/>   
                    
                    <label for='biography'><strong>{Localisation::getTranslation('common_biography')}</strong></label>
                    <textarea name='biography' cols='40' rows='10' style="width: 80%">{if isset($org)} {TemplateHelper::uiCleanseNewlineAndTabs($org->getBiography())} {/if}</textarea>
                    
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
                        <i class="icon-star icon-white"></i> {Localisation::getTranslation('common_create_organisation')}
                    </button>
                </td>
            </tr>
  
        </table>
    </form>

{include file="footer.tpl"}
