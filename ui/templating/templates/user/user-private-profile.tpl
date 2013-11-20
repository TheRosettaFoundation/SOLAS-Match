{include file='header.tpl'}

{if isset($profileUser)}
    <div class="page-header">
        <h1>
        <img src="http://www.gravatar.com/avatar/{md5( strtolower( trim($profileUser->getEmail())))}?s=80&r=g" alt="" />
        {if $profileUser->getDisplayName() != ''}
            {$profileUser->getDisplayName()}
        {else}
            {Localisation::getTranslation(Strings::USER_PRIVATE_PROFILE_PRIVATE_PROFILE)}
        {/if}
        <small>{Localisation::getTranslation(Strings::USER_PRIVATE_PROFILE_0)}</small><br>
        <small>
            {Localisation::getTranslation(Strings::COMMON_NOTE)}:
            <span style="color: red">*</span>
            {Localisation::getTranslation(Strings::COMMON_DENOTES_A_REQUIRED_FIELD)}
        </small>
        </h1>
    </div>
{/if}

<div class="well alert-info">
    <p><strong>{Localisation::getTranslation(Strings::USER_PRIVATE_PROFILE_PLEASE_NOTE)}</strong></p>
    <p>{Localisation::getTranslation(Strings::USER_PRIVATE_PROFILE_3)} {Localisation::getTranslation(Strings::USER_PRIVATE_PROFILE_4)}</p>
</div>
    
<user-private-profile-form userid="{$profileUser->getId()}"></user-private-profile-form>

{include file='footer.tpl'}
