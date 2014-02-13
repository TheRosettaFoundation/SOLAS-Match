{include file="header.tpl"}

    <h1 class="page-header">
        {sprintf(Localisation::getTranslation('org_manage_badge_manage_badge'), $badge->getTitle())}
        <small>{Localisation::getTranslation('org_manage_badge_0')}</small>
    </h1>

    {if isset($flash['success'])}
        <div class="alert alert-success">
            <strong>{Localisation::getTranslation('common_note')}</strong> {$flash['success']}
        </div>
    {/if}


    {assign var="badge_id" value=$badge->getId()}
    <form method="post" action="{urlFor name="org-manage-badge" options="org_id.$org_id|badge_id.$badge_id"}" class="well" accept-charset="utf-8">
        <label for="email">{Localisation::getTranslation('org_manage_badge_3')}</label>
        <input type='text' name='email' id='email' />

        {if isset($flash['error'])}
            <div class="alert alert-error">
                <strong>{Localisation::getTranslation('common_warning')}</strong> {$flash['error']}
            </div>
        {/if}
        <p>
            <button type='submit' class='btn btn-primary' name='submit'>
                <i class="icon-plus-sign icon-white"></i> {Localisation::getTranslation('common_assign_badge')}
            </button>
        </p>
    </form>

    <h3>{Localisation::getTranslation('org_manage_badge_users_with_this_badge')}</h3>
    <p>{Localisation::getTranslation('org_manage_badge_4')}</p>
    {if isset($user_list) && count($user_list) > 0}
        <ul class="unstyled">
            {foreach $user_list as $user}
                <div class="row">
                    {if $user->getDisplayName() != ''}
                        {assign var="displayName" value=$user->getDisplayName()}
                    {else}
                        {assign var="displayName" value=$user->getEmail()}
                    {/if}
                    {assign var="user_id" value=$user->getId()}
                    <li>
                        <div class="span8">
                            <h4>{Localisation::getTranslation('common_display_name')}</h4>
                            <a  href="{urlFor name="user-public-profile" options="user_id.$user_id"}">
                                {$displayName}
                            </a>
                        </div>
                        <div class="right">
                            <form method="post" action="{urlFor name="org-manage-badge" options="org_id.$org_id|badge_id.$badge_id"}" >
                                <input type="hidden" name="user_id" value="{$user->getId()}" />
                                <input type="hidden" value="Remove" onClick="return confirmPost()" />
                                <a href="#" onclick="this.parentNode.submit()" class="pull-right btn btn-inverse">
                                    <i class="icon-fire icon-white"></i> {Localisation::getTranslation('org_manage_badge_remove_badge')}
                                </a>
                            </form>
                        </div>
                    </li>
                </div>
                <br />
            {/foreach}
        </ul>
    {else}
        <p class="alert alert-info">
            {Localisation::getTranslation('org_manage_badge_5')}
        </p>
    {/if}

{include file="footer.tpl"}