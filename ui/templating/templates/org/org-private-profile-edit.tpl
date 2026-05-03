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
            <tr valign="top" align="center">
                <td colspan="2" style="font-weight: bold; text-align: center; padding-bottom: 10px">
                    {Localisation::getTranslation('org_private_profile_organisation_visible_by_all')}...
                    <hr/>
                </td>
            </tr>
            <tr valign="top" align="center">
                <td width="50%">
                    <label for='orgName'><strong>{Localisation::getTranslation('common_organisation_name')} <span style="color: red">*</span></strong></label>
                    <input type='text' name='orgName' id='orgName' style="width: 80%"
                    {if isset($org) && $org->getName() != ''}
                        value="{$org->getName()}"
                    {else}
                         placeholder="{Localisation::getTranslation('org_private_profile_your_organisation_name')}"
                    {/if}
                    />

                    <label for='biography'><strong>{Localisation::getTranslation('org_private_profile_organisation_overview')} <span style="color: red">*</span></strong></label>
                    <textarea name='biography' id='biography' cols='40' rows='10' style="width: 80%"
                    {if !isset($org) || is_null($org->getBiography()) || $org->getBiography() == ''}
                        placeholder="{Localisation::getTranslation('org_private_profile_enter_organisation_biography_here')}"
                    {/if}
                    >{if isset($org) && !is_null($org->getBiography()) && $org->getBiography() != ''}{TemplateHelper::uiCleanseHTMLReinsertNewlineAndTabs($org->getBiography())}{/if}</textarea>

                    <label for='homepage'><strong>{Localisation::getTranslation('org_private_profile_organisation_website')}</strong></label>
                    <input type='text' name='homepage' id='homepage' style="width: 80%"
                    {if isset($org) && !is_null($org->getHomepage()) && $org->getHomepage() != '' && $org->getHomepage() != 'https://'}
                        value="{$org->getHomepage()}"
                    {else}
                        placeholder="https://"
                    {/if}
                    />

                    <label for='facebook'><strong>{Localisation::getTranslation('org_private_profile_organisation_facebook')}</strong></label>
                    <input type='text' name='facebook' id='facebook' style="width: 80%"
                    {if $org->getAddress != NULL && $org->getAddress != ''}
                        value="{$org->getAddress}"
                    {else}
                        placeholder="https://"
                    {/if}
                    />

                    <label for='linkedin'><strong>{Localisation::getTranslation('org_private_profile_organisation_linkedin')}</strong></label>
                    <input type='text' name='linkedin' id='linkedin' style="width: 80%"
                    {if $org->getCity() != NULL && $org->getCity() != ''}
                        value="{$org->getCity()}"
                    {else}
                        placeholder="https://"
                    {/if}
                    />

                    <label for='twitter'><strong>{Localisation::getTranslation('org_private_profile_organisation_twitter')}</strong></label>
                    <input type='text' name='twitter' id='twitter' style="width: 80%"
                    {if $org->getRegionalFocus() != NULL && $org->getRegionalFocus() != ''}
                        value="{$org->getRegionalFocus()}"
                    {else}
                        placeholder="https://"
                    {/if}
                    />
                </td>

                <td width="50%">
                    <label for='country'><strong>{Localisation::getTranslation('common_country')}</strong></label>
                    <input type='text' name='country' id='country' style="width: 80%"
                    {if $org && !is_null($org->getCountry()) && $org->getCountry() != ''}
                        value="{TemplateHelper::uiCleanseHTML($org->getCountry())}"
                    {/if}
                    />
                </td>
            </tr>
            <tr valign="top" align="center">
                <td colspan="2" style="font-weight: bold; text-align: center; padding-bottom: 10px">
                    <hr/>
                    {Localisation::getTranslation('org_private_profile_organisation_visible_by_members')}...
                    <hr/>
                </td>
            </tr>
            <tr valign="top" align="center">
                <td colspan="2">
                    <label for='email'><strong>{Localisation::getTranslation('org_private_profile_organisation_primary_contact_email')} <span style="color: red">*</span></strong></label>
                    <input type='text' name='email' id='email' style="width: 80%"
                    {if isset($org) && !is_null($org->getEmail()) && $org->getEmail() != ''}
                        value="{$org->getEmail()}"
                    {else}
                        placeholder="{Localisation::getTranslation('org_private_profile_organisationexamplecom')}"
                    {/if}
                    />

                    <input type="hidden" name="sesskey" value="{$sesskey}" />
                </td>
            </tr>
            <tr>                
                <td colspan="2" style="padding-bottom: 20px"><hr/></td>
            </tr>
