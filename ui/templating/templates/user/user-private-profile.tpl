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
        <button class='btn btn-inverse pull-right' id="deleteUserButton">
            <i class="icon-fire icon-white"></i> Delete Profile
        </button>
        </h1>
    </div>
{/if}

<div class="well alert-info">
    <p><strong>Please Note:</strong></p>
    <p>All these fields are optional. However, filling them out will provide us with valuable information for contacting you and for matching you with more relevant tasks for your Task Stream.</p>
</div>
    
    <div class="well" is="x-user-private-profile-form" user-id="{$profileUser->getId()}" id="PrivateProfileForm"></div>

    <script type="application/dart" src="{urlFor name="home"}ui/dart/web/Routes/Users/UserPrivateProfile.dart"></script>
    <script src="{urlFor name="home"}ui/dart/web/packages/browser/dart.js"></script>
    <script src="{urlFor name="home"}ui/dart/web/packages/browser/interop.js"></script>
 
{include file='footer.tpl'}
