{include file='header.tpl'}

{if isset($user)}
    <div class="page-header">
        <h1>
        <img src="http://www.gravatar.com/avatar/{md5( strtolower( trim($user->getEmail())))}?s=80&r=g" alt="" />
        {if $user->getDisplayName() != ''}
            {$user->getDisplayName()}
        {else}
            Public Profile
        {/if}
        <small>Update your personal details here</small>
        </h1>
    </div>
{/if}

{if isset($warning) && $warning == true}
    <p>Invalid input, please fill in all options below.</p>
{/if}
 
    <form method='post' action='{urlFor name='user-private-profile'}' class='well'>

        <label for='displayName'><strong>Public Display Name:</strong></label>
        <input type='text' name='displayName' id='displayName'
        {if $user->getDisplayName() != ''}
            value='{$user->getDisplayName()}'
        {else}
            placeholder='Display Name'
        {/if} />           

        <label for='nLanguage'><strong>Native Language:</strong></label>
        {assign var="userNativeLocale" value=$user->getNativeLocale()}
        {if isset($userLocale)}
            {assign var="userLanguageCode" value=$userNativeLocale->getLanguageCode()}
            {assign var="userCountryCode" value=$userNativeLocale->getCountryCode()}
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
                <select name="nLanguageCountry" id="nLanguageCountry">
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
            <input type='text' name='nLanguage' id='nLanguage' value={TemplateHelper::getNativeLanguage($userLocale)} />
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

    </form>
{include file='footer.tpl'}
