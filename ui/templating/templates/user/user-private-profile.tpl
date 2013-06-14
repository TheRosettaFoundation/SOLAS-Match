{include file='header.tpl'}

{if isset($profileUser)}
    <div class="page-header">
        <h1>
        <img src="http://www.gravatar.com/avatar/{md5( strtolower( trim($profileUser->getEmail())))}?s=80&r=g" alt="" />
        {if $profileUser->getDisplayName() != ''}
            {$profileUser->getDisplayName()}
        {else}
            Private Profile
        {/if}
        <small>Update your personal details here.</small><br>
        <small>
            Note:
            <span style="color: red">*</span>
            denotes a required field.
        </small>
        <form id="deleteProfileForm" method='post' action='' class="pull-right">
            <input type="hidden" value="{$profileUser->getId()}" name="deleteUser" />
            
            <button class='btn btn-inverse' onclick="return confirm('Are you sure you want to permanently delete your account?')">
                <i class="icon-fire icon-white"></i> Delete Profile
            </button>
        </form>
        </h1>
    </div>
{/if}

{if isset($warning) && $warning == true}
    <p>Invalid input, please fill in all options below.</p>
{/if}

<div class="well alert-info">
    <p><strong>Please Note:</strong></p>
    <p>All these fields are optional. However, filling them out will provide us with valuable information for contacting you and for matching you with more relevant tasks for your Task Stream.</p>
</div>
 
    <form method='post' action='#' class='well'>
        
        <table>
            <tr valign="top" align="center"> 
                <td width="50%">
                    <label for='displayName'><strong>Public Display Name: <span style="color: red">*</span></strong></label>
                    <input type='text' name='displayName' id='displayName' style="width: 80%"
                    {if $profileUser->getDisplayName() != ''}
                        value='{$profileUser->getDisplayName()}'
                    {else}
                        placeholder='Display Name'
                    {/if} /> 
                    
                    <label for='nLanguage'><strong>Native Language:</strong></label>
                    <div id='userNativeLanguage'>
                        {assign var="usersNativeLocale" value=$profileUser->getNativeLocale()}
                        {if isset($usersNativeLocale)}
                            {assign var="userLanguageCode" value=$usersNativeLocale->getLanguageCode()}
                            {assign var="userCountryCode" value=$usersNativeLocale->getCountryCode()}
                        {/if}

                        <select name="nativeLanguage" id="nativeLanguage" style="width: 82%">
                            {foreach $languages as $language}
                                {if isset($usersNativeLocale) && $userLanguageCode == $language->getCode()}
                                    <option value="{$language->getCode()}" selected="selected">{$language->getName()}</option>
                                {else}
                                    <option value="{$language->getCode()}">{$language->getName()}</option>
                                {/if}
                            {/foreach}
                        </select>

                        <select name="nativeCountry" id="nativeCountry" style="width: 82%">
                            {foreach $countries as $country}
                                {if isset($usersNativeLocale) && $userCountryCode == $country->getCode()}
                                <option value="{$country->getCode()}" selected="selected">{$country->getName()}</option>
                                {else}
                                    <option value="{$country->getCode()}">{$country->getName()}</option>
                                {/if}
                            {/foreach}
                        </select>
                        <hr id="horizontalLine" width="60%"/>
                    </div>
                    
                    <label for='extraSecondaryLanguages'><strong>Secondary Language(s):</strong></label>
                    <div id="extraSecondaryLanguages">
                        
                        {if isset($languages) && isset($countries) && isset($secondaryLanguages)}
                            
                            {assign var=increment value=0}
                            {foreach from=$secondaryLanguages item=secLang}
                                <span id="newSecondaryLanguage{$increment}">
                                    <select name="secondaryLanguage_{$increment}" id="secondaryLanguage_{$increment}" style="width: 82%">
                                        {foreach $languages as $language}
                                            {if $secLang->getLanguageCode() == $language->getCode()}
                                                <option value="{$language->getCode()}" selected="selected">{$language->getName()}</option>
                                            {else}
                                                <option value="{$language->getCode()}">{$language->getName()}</option>
                                            {/if}
                                        {/foreach}
                                    </select>

                                    <select name="secondaryCountry_{$increment}" id="secondaryCountry_{$increment++}" style="width: 82%">
                                        {foreach $countries as $country}
                                            {if $secLang->getCountryCode() == $country->getCode()}
                                            <option value="{$country->getCode()}" selected="selected">{$country->getName()}</option>
                                            {else}
                                                <option value="{$country->getCode()}">{$country->getName()}</option>
                                            {/if}
                                        {/foreach}
                                    </select>       
                                    <hr id="horizontalLine" width="60%"/>
                                </span>
                            {/foreach}
                            
                        {/if}
                    </div>
                    <div id="alertinfo" class="alert alert-info" style="display: none; text-align: center; width: 80%">You have reached the maximum number of Secondary Language fields allowed.</div>  
                    <p>
                        <button id="addNewSecondaryLanguageBtn" class="btn btn-success" type="button" onclick="addNewSecondaryLanguage()"><i class="icon-upload icon-white"></i> Add Secondary Language</button>
                        <button id="removeNewSecondaryLanguageBtn" class="btn btn-inverse"  type="button" onclick="removeNewSecondaryLanguage()" disabled><i class="icon-fire icon-white"></i> Remove</button>
                        <input type="hidden" id="secondaryLanguagesArraySize" name="secondaryLanguagesArraySize" value="0"/>
                    </p>
                    
                    <label for='biography'><strong>Biography:</strong></label>
                    <textarea name='biography' cols='40' rows='7' {if $profileUser->getBiography() == ''} placeholder="Enter Bio Here" {/if}
                    style="width: 80%">{if $profileUser->getBiography() != ''}{$profileUser->getBiography()}{/if}</textarea>
                    
                </td>
                <td width="50%">
                    <label for='firstName'><strong>First Name:</strong></label>
                    <input type='text' name='firstName' id='firstName' style="width: 80%"
                    {if !is_null($userPersonalInfo) && $userPersonalInfo->getFirstName() != ''}
                        value='{$userPersonalInfo->getFirstName()}'
                    {/if} />
                    
                    <label for='lastName'><strong>Last Name:</strong></label>
                    <input type='text' name='lastName' id='lastName' style="width: 80%"
                    {if !is_null($userPersonalInfo) && $userPersonalInfo->getLastName() != ''}
                        value='{$userPersonalInfo->getLastName()}'
                    {/if} />

                    <label for='mobileNumber'><strong>Mobile Number:</strong></label>
                    <input type='text' name='mobileNumber' id='mobileNumber' style="width: 80%"
                    {if !is_null($userPersonalInfo) && $userPersonalInfo->getMobileNumber() != ''}
                        value='{$userPersonalInfo->getMobileNumber()}'
                    {/if} />
                    
                    <label for='businessNumber'><strong>Business Number:</strong></label>
                    <input type='text' name='businessNumber' id='businessNumber' style="width: 80%"
                    {if !is_null($userPersonalInfo) && $userPersonalInfo->getBusinessNumber() != ''}
                        value='{$userPersonalInfo->getBusinessNumber()}'
                    {/if} />
                    
                    <label for='sip'><strong>Session Initiation Protocol (SIP):</strong></label>
                    <input type='text' name='sip' id='sip' style="width: 80%"
                    {if !is_null($userPersonalInfo) && $userPersonalInfo->getSip() != ''}
                        value='{$userPersonalInfo->getSip()}'
                    {/if} />
                    
                    <label for='jobTitle'><strong>Job Title:</strong></label>
                    <input type='text' name='jobTitle' id='jobTitle' style="width: 80%"
                    {if !is_null($userPersonalInfo) && $userPersonalInfo->getJobTitle() != ''}
                        value='{$userPersonalInfo->getJobTitle()}'
                    {/if} />
                    
                    <label for='address'><strong>Address:</strong></label>
                    <textarea id="address" name='address' cols='40' rows='5' style="width: 80%">{if !is_null($userPersonalInfo) && $userPersonalInfo->getAddress() != ''}{$userPersonalInfo->getAddress()}{/if}</textarea>
                    
                    <label for='city'><strong>City:</strong></label>
                    <input type='text' name='city' id='city' style="width: 80%"
                    {if !is_null($userPersonalInfo) && $userPersonalInfo->getCity() != ''}
                        value='{$userPersonalInfo->getCity()}'
                    {/if} />
                    
                    <label for='country'><strong>Country:</strong></label>
                    <input type='text' name='country' id='country' style="width: 80%"
                    {if !is_null($userPersonalInfo) && $userPersonalInfo->getCountry() != ''}
                        value='{$userPersonalInfo->getCountry()}'
                    {/if} />
                </td>
            <tr>                
                <td colspan="2">
                    <hr/>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <table>
                        <tr>
                            <td colspan="3" align="center" style="font-weight: bold">
                                Task Type Preferences:
                            </td>
                        </tr>
                        <tr align="center">
                            <td>
                                Translating
                            </td>
                            <td>
                                Proofreading
                            </td>
                            <td>
                                Interpreting
                            </td>
                        </tr>
                        <tr align="center">
                            <td>
                                <input name="translating" value="1" type="checkbox" {if in_array(BadgeTypes::TRANSLATOR, $userBadgeIds)} checked {/if} />  
                            </td>
                            <td>
                                <input name="proofreading" value="1" type="checkbox" {if in_array(BadgeTypes::PROOFREADER, $userBadgeIds)} checked {/if} />
                            </td>
                            <td>
                                <input name="interpreting" value="1" type="checkbox" {if in_array(BadgeTypes::INTERPRETOR, $userBadgeIds)} checked {/if} />
                            </td>
                        </tr>                        
                    </table>  
                </td>
            </tr>
            <tr>                
                <td colspan="2" style="padding-bottom: 20px">
                    <hr/>
                </td>
            </tr>
            <tr>
                <td colspan="2" align="center">
                    <button type='submit' class='btn btn-primary' name='updateProfileDetails'>
                        <i class="icon-refresh icon-white"></i> Update Profile Details
                    </button>   
                    <button type="submit" class="btn btn-inverse" value="{$profileUser->getId()}" name="deleteUser"
                            onclick="return confirm('Are you sure you want permanently delete your account?');"> 
                        <i class="icon-fire icon-white"></i> Delete User Account
                    </button>
                </td>
            </tr>
  
        </table>
    </form> 

{include file='footer.tpl'}
