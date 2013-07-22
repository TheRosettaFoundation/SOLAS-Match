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
        <button class='btn btn-inverse pull-right' id="deleteUserButton">
            <i class="icon-fire icon-white"></i> {Localisation::getTranslation(Strings::USER_PRIVATE_PROFILE_DELETE_PROFILE)}
        </button>
        </h1>
    </div>
{/if}

<div class="well alert-info">
    <p><strong>{Localisation::getTranslation(Strings::USER_PRIVATE_PROFILE_PLEASE_NOTE)}</strong></p>
    <p>{Localisation::getTranslation(Strings::USER_PRIVATE_PROFILE_3)} {Localisation::getTranslation(Strings::USER_PRIVATE_PROFILE_4)}</p>
</div>
    
<div class="well" is="x-user-private-profile-form" user-id="{$profileUser->getId()}" id="PrivateProfileForm"></div>

<script type="application/dart" src="{urlFor name="home"}ui/dart/deploy/web/Routes/Users/UserPrivateProfile.dart"></script>
<script src="{urlFor name="home"}ui/dart/deploy/web/packages/browser/dart.js"></script>
<script src="{urlFor name="home"}ui/dart/deploy/web/packages/browser/interop.js"></script>
 
{include file='footer.tpl'}
