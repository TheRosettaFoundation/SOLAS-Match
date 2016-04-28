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

                    <label for='activitys'><strong>{Localisation::getTranslation('org_private_profile_organisation_activity')}</strong><br />{Localisation::getTranslation('org_private_profile_organisation_multiple')}</label>
                    <select name='activitys[]' multiple id='activitys' size="8" style="width: 80%">
                        {foreach from=$activitys item=activity}
                            <option value="{$activity['code']}" {if $activity['selected']}selected="selected"{/if}>{$activity['value']}</option>
                        {/foreach}
                    </select>

                    <label for='homepage'><strong>{Localisation::getTranslation('org_private_profile_organisation_website')}</strong></label>
                    <input type='text' name='homepage' id='homepage' style="width: 80%"
                    {if isset($org) && !is_null($org->getHomepage()) && $org->getHomepage() != ''}
                        value="{$org->getHomepage()}"
                    {else}
                        placeholder="http://"
                    {/if}
                    />

                    <label for='facebook'><strong>{Localisation::getTranslation('org_private_profile_organisation_facebook')}</strong></label>
                    <input type='text' name='facebook' id='facebook' style="width: 80%"
                    {if $org2->getFacebook() != ''}
                        value="{$org2->getFacebook()}"
                    {else}
                        placeholder="http://"
                    {/if}
                    />

                    <label for='linkedin'><strong>{Localisation::getTranslation('org_private_profile_organisation_linkedin')}</strong></label>
                    <input type='text' name='linkedin' id='linkedin' style="width: 80%"
                    {if $org2->getLinkedin() != ''}
                        value="{$org2->getLinkedin()}"
                    {else}
                        placeholder="http://"
                    {/if}
                    />

                    <label for='twitter'><strong>{Localisation::getTranslation('org_private_profile_organisation_twitter')}</strong></label>
                    <input type='text' name='twitter' id='twitter' style="width: 80%"
                    {if $org2->getPrimaryContactEmail() != ''}
                        value="{$org2->getPrimaryContactEmail()}"
                    {else}
                        placeholder="http://"
                    {/if}
                    />
                </td>

                <td width="50%">
                    <label for='address'><strong>{Localisation::getTranslation('common_address')}</strong></label>
                    <textarea name='address' id='address' cols='40' rows='7' style="width: 80%">{if isset($org) && !is_null($org) && $org->getAddress() != ''}{TemplateHelper::uiCleanseHTMLReinsertNewlineAndTabs($org->getAddress())}{/if}</textarea>

                    <label for='city'><strong>{Localisation::getTranslation('common_city')}</strong></label>
                    <input type='text' name='city' id='city' style="width: 80%"
                    {if $org && !is_null($org->getCity()) && $org->getCity() != ''}
                        value="{TemplateHelper::uiCleanseHTML($org->getCity())}"
                    {/if}
                    />

                    <label for='country'><strong>{Localisation::getTranslation('common_country')}</strong></label>
                    <input type='text' name='country' id='country' style="width: 80%"
                    {if $org && !is_null($org->getCountry()) && $org->getCountry() != ''}
                        value="{TemplateHelper::uiCleanseHTML($org->getCountry())}"
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
                            <td style="width: 15%"><input id="africa"       name="africa"       type="checkbox" {if isset($org) && !is_null($org->getRegionalFocus())} {if strstr($org->getRegionalFocus(), "Africa")}        checked {/if}{/if} /></td>
                            <td style="width: 15%"><input id="asia"         name="asia"         type="checkbox" {if isset($org) && !is_null($org->getRegionalFocus())} {if strstr($org->getRegionalFocus(), "Asia")}          checked {/if}{/if} /></td>
                            <td style="width: 15%"><input id="australia"    name="australia"    type="checkbox" {if isset($org) && !is_null($org->getRegionalFocus())} {if strstr($org->getRegionalFocus(), "Australia")}     checked {/if}{/if} /></td>
                            <td style="width: 15%"><input id="europe"       name="europe"       type="checkbox" {if isset($org) && !is_null($org->getRegionalFocus())} {if strstr($org->getRegionalFocus(), "Europe")}        checked {/if}{/if} /></td>
                            <td style="width: 15%"><input id="northAmerica" name="northAmerica" type="checkbox" {if isset($org) && !is_null($org->getRegionalFocus())} {if strstr($org->getRegionalFocus(), "North-America")} checked {/if}{/if} /></td>
                            <td style="width: 15%"><input id="southAmerica" name="southAmerica" type="checkbox" {if isset($org) && !is_null($org->getRegionalFocus())} {if strstr($org->getRegionalFocus(), "South-America")} checked {/if}{/if} /></td>
                        </tr>
                    </table> 
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
                    <label for='primarycontactname'><strong>{Localisation::getTranslation('org_private_profile_organisation_primary_contact_name')} <span style="color: red">*</span></strong></label>
                    <input type='text' name='primarycontactname' id='primarycontactname' style="width: 80%"
                    {if $org2->getPrimaryContactName() != ''}
                        value="{TemplateHelper::uiCleanseHTML($org2->getPrimaryContactName())}"
                    {/if}
                    />

                    <label for='primarycontacttitle'><strong>{Localisation::getTranslation('org_private_profile_organisation_primary_contact_title')}</strong></label>
                    <input type='text' name='primarycontacttitle' id='primarycontacttitle' style="width: 80%"
                    {if $org2->getPrimaryContactTitle() != ''}
                        value="{TemplateHelper::uiCleanseHTML($org2->getPrimaryContactTitle())}"
                    {/if}
                    />

                    <label for='email'><strong>{Localisation::getTranslation('org_private_profile_organisation_primary_contact_email')} <span style="color: red">*</span></strong></label>
                    <input type='text' name='email' id='email' style="width: 80%"
                    {if isset($org) && !is_null($org->getEmail()) && $org->getEmail() != ''}
                        value="{$org->getEmail()}"
                    {else}
                        placeholder="{Localisation::getTranslation('org_private_profile_organisationexamplecom')}"
                    {/if}
                    />

                    <label for='primarycontactphone'><strong>{Localisation::getTranslation('org_private_profile_organisation_primary_contact_phone')}</strong></label>
                    <input type='text' name='primarycontactphone' id='primarycontactphone' style="width: 80%"
                    {if $org2->getPrimaryContactPhone() != ''}
                         value="{TemplateHelper::uiCleanseHTML($org2->getPrimaryContactPhone())}"
                    {/if}
                    />

                    <label for='othercontacts'><strong>{Localisation::getTranslation('org_private_profile_organisation_other_contacts')}</strong></label>
                    <textarea name='othercontacts' id='othercontacts' cols='40' rows='7' style="width: 80%">{if $org2->getOtherContacts() != ''}{TemplateHelper::uiCleanseHTMLReinsertNewlineAndTabs($org2->getOtherContacts())}{/if}</textarea>

                    <label for='structure'><strong>{Localisation::getTranslation('org_private_profile_organisation_structure')}</strong></label>
                    <textarea name='structure' id='structure' cols='40' rows='10' style="width: 80%">{if $org2->getStructure() != ''}{TemplateHelper::uiCleanseHTMLReinsertNewlineAndTabs($org2->getStructure())}{/if}</textarea>

                    <label for='affiliations'><strong>{Localisation::getTranslation('org_private_profile_organisation_affiliations')}</strong></label>
                    <textarea name='affiliations' id='affiliations' cols='40' rows='10' style="width: 80%">{if $org2->getAffiliations() != ''}{TemplateHelper::uiCleanseHTMLReinsertNewlineAndTabs($org2->getAffiliations())}{/if}</textarea>

                    <label for='urlvideo1'><strong>{Localisation::getTranslation('org_private_profile_organisation_url_video_1')}<br />(1)</strong></label>
                    <input type='text' name='urlvideo1' id='urlvideo1' style="width: 80%"
                    {if $org2->getUrlVideo1() != ''}
                        value="{$org2->getUrlVideo1()}"
                    {else}
                        placeholder="http://"
                    {/if}
                    />

                    <label for='urlvideo2'><strong>(2)</strong></label>
                    <input type='text' name='urlvideo2' id='urlvideo2' style="width: 80%"
                    {if $org2->getUrlVideo2() != ''}
                        value="{$org2->getUrlVideo2()}"
                    {else}
                        placeholder="http://"
                    {/if}
                    />

                    <label for='urlvideo3'><strong>(3)</strong></label>
                    <input type='text' name='urlvideo3' id='urlvideo3' style="width: 80%"
                    {if $org2->getUrlVideo3() != ''}
                        value="{$org2->getUrlVideo3()}"
                    {else}
                        placeholder="http://"
                    {/if}
                    />

                    <label for='employees'><strong>{Localisation::getTranslation('org_private_profile_organisation_employee')}</strong></label>
                    <select name='employees[]' multiple id='employees' size="6" style="width: 80%">
                        {foreach from=$employees item=employee}
                            <option value="{$employee['code']}" {if $employee['selected']}selected="selected"{/if}>{$employee['value']}</option>
                        {/foreach}
                    </select>

                    <label for='fundings'><strong>{Localisation::getTranslation('org_private_profile_organisation_funding')}</strong><br />{Localisation::getTranslation('org_private_profile_organisation_multiple')}</label>
                    <select name='fundings[]' multiple id='fundings' size="4" style="width: 80%">
                        {foreach from=$fundings item=funding}
                            <option value="{$funding['code']}" {if $funding['selected']}selected="selected"{/if}>{$funding['value']}</option>
                        {/foreach}
                    </select>

                    <label for='finds'><strong>{Localisation::getTranslation('org_private_profile_organisation_find')}</strong></label>
                    <select name='finds[]' multiple id='finds' size="7" style="width: 80%">
                        {foreach from=$finds item=find}
                            <option value="{$find['code']}" {if $find['selected']}selected="selected"{/if}>{$find['value']}</option>
                        {/foreach}
                    </select>

                    <label for='translations'><strong>{Localisation::getTranslation('org_private_profile_organisation_translation')}</strong><br />{Localisation::getTranslation('org_private_profile_organisation_multiple')}</label>
                    <select name='translations[]' multiple id='translations' size="4" style="width: 80%">
                        {foreach from=$translations item=translation}
                            <option value="{$translation['code']}" {if $translation['selected']}selected="selected"{/if}>{$translation['value']}</option>
                        {/foreach}
                    </select>

                    <label for='requests'><strong>{Localisation::getTranslation('org_private_profile_organisation_request')}</strong></label>
                    <select name='requests[]' multiple id='requests' size="2" style="width: 80%">
                        {foreach from=$requests item=request}
                            <option value="{$request['code']}" {if $request['selected']}selected="selected"{/if}>{$request['value']}</option>
                        {/foreach}
                    </select>

                    <label for='contents'><strong>{Localisation::getTranslation('org_private_profile_organisation_content')}</strong><br />{Localisation::getTranslation('org_private_profile_organisation_multiple')}</label>
                    <select name='contents[]' multiple id='contents' size="7" style="width: 80%">
                        {foreach from=$contents item=content}
                            <option value="{$content['code']}" {if $content['selected']}selected="selected"{/if}>{$content['value']}</option>
                        {/foreach}
                    </select>

                    <label for='subjectmatters'><strong>{Localisation::getTranslation('org_private_profile_organisation_subject_matters')}</strong></label>
                    <textarea name='subjectmatters' id='subjectmatters' cols='40' rows='7' style="width: 80%">{if $org2->getSubjectMatters() != ''}{TemplateHelper::uiCleanseHTMLReinsertNewlineAndTabs($org2->getSubjectMatters())}{/if}</textarea>

                    <label for='pages'><strong>{Localisation::getTranslation('org_private_profile_organisation_pages')}</strong></label>
                    <select name='pages[]' multiple id='pages' size="5" style="width: 80%">
                        {foreach from=$pages item=page}
                            <option value="{$page['code']}" {if $page['selected']}selected="selected"{/if}>{$page['value']}</option>
                        {/foreach}
                    </select>

                    <label for='sources'><strong>{Localisation::getTranslation('org_private_profile_organisation_source')}</strong><br />{Localisation::getTranslation('org_private_profile_organisation_multiple')}</label>
                    <select name='sources[]' multiple id='sources' size="8" style="width: 80%">
                        {foreach from=$sources item=source}
                            <option value="{$source['code']}" {if $source['selected']}selected="selected"{/if}>{$source['value']}</option>
                        {/foreach}
                    </select>

                    <label for='targets'><strong>{Localisation::getTranslation('org_private_profile_organisation_target')}</strong><br />{Localisation::getTranslation('org_private_profile_organisation_multiple')}</label>
                    <select name='targets[]' multiple id='targets' size="8" style="width: 80%">
                        {foreach from=$targets item=target}
                            <option value="{$target['code']}" {if $target['selected']}selected="selected"{/if}>{$target['value']}</option>
                        {/foreach}
                    </select>

                    <label for='oftens'><strong>{Localisation::getTranslation('org_private_profile_organisation_often')}</strong></label>
                    <select name='oftens[]' multiple id='oftens' size="4" style="width: 80%">
                        {foreach from=$oftens item=often}
                            <option value="{$often['code']}" {if $often['selected']}selected="selected"{/if}>{$often['value']}</option>
                        {/foreach}
                    </select>
                </td>
            </tr>
            <tr>                
                <td colspan="2" style="padding-bottom: 20px"><hr/></td>
            </tr>
