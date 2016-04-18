{include file='header.tpl'}

{if isset($org)}
    <div class='page-header'><h1>
    {if $org->getName() != ''}
        {$org->getname()}
    {else}
        {Localisation::getTranslation('common_organisation_profile')}
    {/if}
    <small>{Localisation::getTranslation('org_private_profile_alter_profile_here')}</small>
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
                <td colspan="2" style="font-weight: bold; text-align: center; padding-bottom: 10px">
                    {Localisation::getTranslation('org_private_profile_organisation_common_visible_by_all')}...
                    <hr/>
                </td>

                <td width="50%">
                    <label for='orgName'><strong>{Localisation::getTranslation('common_organisation_name')} <span style="color: red">*</span></strong></label>
                    <input type='text' name='orgName' id='orgName' style="width: 80%"
                    {if $org->getName() != ''}
                       value="{$org->getName()}"
                    {else}
                        placeholder='{Localisation::getTranslation('org_private_profile_your_organisation_name')}'
                    {/if}
                    />

                    <label for='biography'><strong>{Localisation::getTranslation('org_private_profile_organisation_overview')} <span style="color: red">*</span></strong></label>
                    <textarea name='biography' cols='40' rows='10' style="width: 80%"
                    {if $org->getBiography() == ''}
                        placeholder="{Localisation::getTranslation('org_private_profile_enter_organisation_biography_here')}"
                    {/if}
                    >{if $org->getBiography() != ''}{TemplateHelper::uiCleanseNewlineAndTabs($org->getBiography())}{/if}</textarea>

                    <label for='activity'><strong>{Localisation::getTranslation('org_private_profile_organisation_activity')}</strong>{Localisation::getTranslation('org_private_profile_organisation_multiple')}</label>
                    <select name='activity' multiple id='activity' style="width: 80%">
                        <option value=""></option>
                        {foreach $activities as $activity}
                            <option value="{$activity['code']}" {if $activity['selected']}selected="selected"{/if}>{$activity['value']}</option>
                        {/foreach}
                    </select>

                    <label for='homepage'><strong>{Localisation::getTranslation('org_private_profile_organisation_website')}</strong></label>
                    <input type='text' name='homepage' id='homepage' style="width: 80%"
                    {if $org->getHomepage() != 'http://'}
                        value="{$org->getHomepage()}"
                    {else}
                        value='http://'
                    {/if}
                     />

                    <label for='facebook'><strong>{Localisation::getTranslation('org_private_profile_organisation_facebook')}</strong></label>
                    <input type='text' name='facebook' id='facebook' style="width: 80%"
                    {if $org2->getFacebook() != 'http://'}
                        value="{$org2->getFacebook()}"
                    {else}
                        value='http://'
                    {/if}
                     />

                    <label for='linkedin'><strong>{Localisation::getTranslation('org_private_profile_organisation_linkedin')}</strong></label>
                    <input type='text' name='linkedin' id='linkedin' style="width: 80%"
                    {if $org->getLinkedin() != 'http://'}
                        value="{$org2->getLinkedin()}"
                    {else}
                        value='http://'
                    {/if}
                     />

                    <label for='email'><strong>{Localisation::getTranslation('org_private_profile_organisation_email_volunteers')}</strong></label>
                    <input type='text' name='email' id='email' style="width: 80%"
                    {if $org->getEmail() != ''}
                         value="{$org->getEmail()}"
                    {else}
                        placeholder='{Localisation::getTranslation('org_private_profile_organisationexamplecom')}'
                    {/if}
                    />
                </td>

                <td width="50%">
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

                        <tr align="center">
                            <td colspan="2" style="font-weight: bold; text-align: center; padding-bottom: 10px">
                                {Localisation::getTranslation('org_private_profile_organisation_common_visible_by_members')}...
                                <hr/>
                            </td>

*Primary Contact Given Name & Family Name
Primary Contact Title
*Primary Contact email
Primary Contact Phone (with country code)
Details for other Contacts
Organisational Structure, Leadership Team, Board & Locations
Organisational Affiliations & Awards
URL (1) for Video or other Document descibing Organisation or Organisation's Impact or Social Responsibility/Impact report
URL (2) for Video or other Document descibing Organisation or Organisation's Impact or Social Responsibility/Impact report
URL (3) for Video or other Document descibing Organisation or Organisation's Impact or Social Responsibility/Impact report
Number of Employees [dropdown with ranges]
Your funding sources [Dropdown]
How did you find us? [Dropdown]
How are you getting your translations done at the moment? [Dropdown]
Which one of the following best defines your request? [Dropdown]
What are the typical content types you need translations for? Eg. Website, Strategy, Advocacy, Manuals, Projects, Campaigns, etc. [Dropdown]
What are the typical subject matters?
What are the typical content volumes during the course of a year (in pages or words) : one page = 250 words [Dropdown]
What are the most frequent source languages? [Dropdown]
What are the most frequent target languages? [Dropdown]
How often do you require translations? [Dropdown]
                            <hr/>
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
                                onclick="return confirm('{Localisation::getTranslation('org_private_profile_confirm_delete')}');"> 
                            <i class="icon-fire icon-white"></i> {Localisation::getTranslation('org_private_profile_delete_organisation')}
                        </button>
                    {/if}
                </td>
            </tr>
  
        </table>
    </form>    



{include file='footer.tpl'}
