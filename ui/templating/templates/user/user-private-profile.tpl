{include file='header.tpl'}

{if isset($profileUser)}
    <div class="page-header">
        <h1>
        <img src="http://www.gravatar.com/avatar/{md5( strtolower( trim($profileUser->getEmail())))}?s=80&r=g" alt="" />
        {if $profileUser->getDisplayName() != ''}
            {$profileUser->getDisplayName()}
        {else}
            {Localisation::getTranslation('user_private_profile_private_profile')}
        {/if}
        <small>{Localisation::getTranslation('user_private_profile_0')}</small><br>
        <small>
            {Localisation::getTranslation('common_denotes_a_required_field')}
        </small>
        </h1>
    </div>
{/if}

<div class="well alert-info">
    <p><strong>{Localisation::getTranslation('user_private_profile_please_note')}</strong></p>
    <p>{Localisation::getTranslation('user_private_profile_3')} {Localisation::getTranslation('user_private_profile_4')}</p>
</div>
    
<user-private-profile-form userid="{$profileUser->getId()}"></user-private-profile-form>

{include file='footer.tpl'}
