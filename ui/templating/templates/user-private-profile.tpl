{include file='header.tpl'}

{if isset($user)}
    <div class="page-header">
        <h1>
        <img src="http://www.gravatar.com/avatar/{md5( strtolower( trim($user->getEmail())))}?s=80&r=g" alt="" />
        {if $user->getDisplayName() != ''}
            {$user->getDisplayName()}
        {else}
            Private Profile
        {/if}
        <small>Update your personal details here</small>
        </h1>
    </div>
{/if}

{if isset($warning) && $warning == true}
    <p>Invalid input, please fill in all options below.</p>
{/if}
 
    <form method='post' action='{urlFor name='user-private-profile'}' class='well'>
        
        <table border="0">
            <tr valign="top" align="center"> 
                <td width="50%">
                    <label for='displayName'><strong>Public Display Name:</strong></label>
                    <input type='text' name='displayName' id='displayName' style="width: 80%"
                    {if $user->getDisplayName() != ''}
                        value='{$user->getDisplayName()}'
                    {else}
                        placeholder='Display Name'
                    {/if} /> 
                    
                    <label for='nLanguage'><strong>Native Language:</strong></label>
                    {assign var="usersNativeLocale" value=$user->getNativeLocale()}
                    {if isset($usersNativeLocale)}
                        {assign var="userLanguageCode" value=$usersNativeLocale->getLanguageCode()}
                        {assign var="userCountryCode" value=$usersNativeLocale->getCountryCode()}
                    {/if}
                    {if isset($languages)}
                        <select name="nativeLanguage" id="nativeLanguage" style="width: 80%">
                            {foreach $languages as $language}
                                {if isset($usersNativeLocale) && $userLanguageCode == $language->getCode()}
                                    <option value="{$language->getCode()}" selected="selected">{$language->getName()}</option>
                                {else}
                                    <option value="{$language->getCode()}">{$language->getName()}</option>
                                {/if}
                            {/foreach}
                        </select>

                        {if isset($countries)}
                            <select name="nativeCountry" id="nativeCountry" style="width: 80%">
                                {foreach $countries as $country}
                                    {if isset($usersNativeLocale) && $userCountryCode == $country->getCode()}
                                    <option value="{$country->getCode()}" selected="selected">{$country->getName()}</option>
                                    {else}
                                        <option value="{$country->getCode()}">{$country->getName()}</option>
                                    {/if}
                                {/foreach}
                            </select>
                        {/if}
                    {else}
                        <input type='text' name='nLanguage' id='nLanguage' value={TemplateHelper::getLanguageAndCountry($usersNativeLocale)} />            
                    {/if}
                    
                    <label for='extraSecondaryLanguages'><strong>Secondary Language(s):</strong></label>
                    <div id="extraSecondaryLanguages">
                        
                        {if isset($languages) && isset($countries) && isset($secondaryLanguages)}
                            
                            {assign var=increment value=0}
                            {foreach from=$secondaryLanguages item=secLang}
                                <span id="newSecondaryLanguage{$increment}">
                                    <select name="secondaryLanguage" id="secondaryLanguage_{$increment}" style="width: 80%">
                                        {foreach $languages as $language}
                                            {if $secLang->getLanguageCode() == $language->getCode()}
                                                <option value="{$language->getCode()}" selected="selected">{$language->getName()}</option>
                                            {else}
                                                <option value="{$language->getCode()}">{$language->getName()}</option>
                                            {/if}
                                        {/foreach}
                                    </select>

                                    <select name="secondaryCountry" id="secondaryCountry_{$increment++}" style="width: 80%">
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
                        <input id="addNewSecondaryLanguageBtn" class="btn btn-success" type="button" onclick="addNewSecondaryLanguage()" value="Add Secondary Language"/>
                        <input id="removeNewSecondaryLanguageBtn" class="btn btn-inverse"  type="button" onclick="removeNewSecondaryLanguage()" value="Remove" disabled="true" />  
                        <input type="hidden" id="secondaryLanguagesArraySize" name="secondaryLanguagesArraySize" value="0"/>
                    </p>
                    
                    <label for='biography'><strong>Biography:</strong></label>
                    <textarea name='biography' cols='40' rows='7' {if $user->getBiography() == ''} placeholder="Enter Bio Here" {/if}
                    style="width: 80%">{if $user->getBiography() != ''}{$user->getBiography()}{/if}</textarea>
                    
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
                <td colspan="2" style="padding-bottom: 20px"><hr/></td>
            </tr>
            <tr>
                <td colspan="2" align="center">
                    <button type='submit' class='btn btn-primary' name='updateProfileDetails'>
                        <i class="icon-refresh icon-white"></i> Update Profile Details
                    </button>   
                </td>
            </tr>
  
        </table>
    </form> 
{*                    
                    
                    
                    <label for='displayName'><strong>Public Display Name:</strong></label>
                    <input type='text' name='displayName' id='displayName' style="width: 80%"
                    {if $org->getName() != ''}
                       value="{$org->getName()}"
                    {else}
                        placeholder='Your organisation name.' 
                    {/if}
                    />
                    
                    <label for='address'><strong>Address:</strong></label>
                    <textarea name='address' cols='40' rows='7' style="width: 80%"
                    >{if $org->getAddress() != ''}{$org->getAddress()}{/if}</textarea>
                    
                    <label for='city'><strong>City:</strong></label>
                    <input type='text' name='city' id='city' style="width: 80%"
                    {if $org->getCity() != ''}
                         value="{$org->getCity()}"
                    {/if}
                    />

                    <label for='country'><strong>Country:</strong></label>
                    <input type='text' name='country' id='country' style="width: 80%"
                    {if $org->getCountry() != ''}
                         value="{$org->getCountry()}"
                    {/if}
                    />
                    
                </td>
                <td width="50%">
                    
                    <label for='homepage'><strong>Home Page:</strong></label>
                    <input type='text' name='homepage' id='homepage' style="width: 80%"
                    {if $org->getHomePage() != 'http://'}
                         value="{$org->getHomePage()}"
                    {else}
                        placeholder='http://www.example.com'
                    {/if}
                    /> 
                    
                    <label for='email'><strong>E-Mail:</strong></label>
                    <input type='text' name='email' id='email' style="width: 80%"
                    {if $org->getEmail() != ''}
                         value="{$org->getEmail()}"
                    {else}
                        placeholder='organisation@example.com'
                    {/if}
                    />   
                    
                    <label for='biography'><strong>Biography:</strong></label>
                    <textarea name='biography' cols='40' rows='10' style="width: 80%" 
                    {if $org->getBiography() == ''}
                        placeholder="Enter Organisation Biography Here"
                    {/if}
                    >{if $org->getBiography() != ''}{$org->getBiography()}{/if}</textarea>
                    
                </td>
            </tr>
            <tr>                
                <td colspan="2" style="font-weight: bold; text-align: center; padding-bottom: 10px">
                    <hr/>
                    Regional Focus:
                </td>
            </tr>  
            <tr align="center">
                <td colspan="2">
                    <table> 
                        <thead>
                            <th>Africa</th>
                            <th>Asia</th>
                            <th>Australia</th>
                            <th>Europe</th>
                            <th>North America</th>
                            <th>South America</th>                       
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
                        <i class="icon-refresh icon-white"></i> Update Organisation Details
                    </button>   
                </td>
            </tr>
            {/if}
        </table>

        <label for='displayName'><strong>Public Display Name:</strong></label>
        <input type='text' name='displayName' id='displayName'
        {if $user->getDisplayName() != ''}
            value='{$user->getDisplayName()}'
        {else}
            placeholder='Display Name'
        {/if} />           

        <label for='nLanguage'><strong>Native Language:</strong></label>
        {assign var="usersNativeLocale" value=$user->getNativeLocale()}
        {if isset($usersNativeLocale)}
            {assign var="userLanguageCode" value=$usersNativeLocale->getLanguageCode()}
            {assign var="userCountryCode" value=$usersNativeLocale->getCountryCode()}
        {/if}
        {if isset($languages)}
            <select name="nativeLanguage" id="nativeLanguage">
                {foreach $languages as $language}
                    {if isset($userLocale) && $userLanguageCode == $language->getCode()}
                        <option value="{$language->getCode()}" selected="selected">{$language->getName()}</option>
                    {else}
                        <option value="{$language->getCode()}">{$language->getName()}</option>
                    {/if}
                {/foreach}
            </select>

            {if isset($countries)}
                <select name="nativeCountry" id="nativeCountry">
                    {foreach $countries as $country}
                        {if isset($userLocale) && $userCountryCode == $country->getCode()}
                        <option value="{$country->getCode()}" selected="selected">{$country->getName()}</option>
                        {else}
                            <option value="{$country->getCode()}">{$country->getName()}</option>
                        {/if}
                    {/foreach}
                </select>
            {/if}
        {else}
            <input type='text' name='nLanguage' id='nLanguage' value={TemplateHelper::getLanguageAndCountry($userLocale)} />            
        {/if}

        <label for='extraSecondaryLanguages'><strong>Secondary Language(s):</strong></label>
        <div id="extraSecondaryLanguages"></div>
        <div id="alertinfo" class="alert alert-info" style="display: none; text-align: center; width: 40%">You have reached the maximum number of Secondary Language fields allowed.</div>  
        <p>
            <input id="addNewSecondaryLanguageBtn" type="button" onclick="addNewSecondaryLanguage()" value="Add Secondary Language"/>
            <input id="removeNewSecondaryLanguageBtn" type="button" onclick="removeNewSecondaryLanguage()" value="Remove" disabled="true" style="visibility: hidden"/>  
            <input type="hidden" id="secondaryLanguagesArraySize" name="secondaryLanguagesArraySize" value="1"/>
        </p>

        <label for='biography'><strong>Biography:</strong></label>
        <textarea name='biography' cols='40' rows='5' {if $user->getBiography() == ''} placeholder="Enter Bio Here" {/if}
        >{if $user->getBiography() != ''}{$user->getBiography()}{/if}</textarea>
        <p>Register with <a href="http://en.gravatar.com/" target="_blank">Gravatar</a> to choose your avatar!</p>

        <p> 
            <button type='submit' class='btn btn-primary' name='submit'>
                <i class="icon-refresh icon-white"></i> Update
            </button>
        </p>

    </form>*}
{include file='footer.tpl'}
