{include file="header.tpl"}

    <h1 class="page-header">
        {Localisation::getTranslation(Strings::ORG_MANAGE_BADGE_MANAGE_BADGE)} {$badge->getTitle()}
        <small>{Localisation::getTranslation(Strings::ORG_MANAGE_BADGE_0)}</small>
    </h1>

    {if isset($flash['success'])}
        <div class="alert alert-success">
            <strong>{Localisation::getTranslation(Strings::COMMON_NOTE)}:</strong> {$flash['success']}
        </div>
    {/if}

    <h3>{Localisation::getTranslation(Strings::ORG_MANAGE_BADGE_1)}</h3>
    <p>{Localisation::getTranslation(Strings::ORG_MANAGE_BADGE_2)}</p>

    {assign var="badge_id" value=$badge->getId()}
    <form method="post" action="{urlFor name="org-manage-badge" options="org_id.$org_id|badge_id.$badge_id"}" class="well">
        <label for="email">{Localisation::getTranslation(Strings::ORG_MANAGE_BADGE_3)}</label>
        <input type='text' name='email' id='email' />

        {if isset($flash['error'])}
            <div class="alert alert-error">
                <strong>{Localisation::getTranslation(Strings::COMMON_WARNING)}!</strong> {$flash['error']}
            </div>
        {/if}
        <p>
            <button type='submit' class='btn btn-primary' name='submit'>
                <i class="icon-plus-sign icon-white"></i> {Localisation::getTranslation(Strings::COMMON_ASSIGN)}
            </button>
        </p>
    </form>

    <h3>{Localisation::getTranslation(Strings::ORG_MANAGE_BADGE_USERS_WITH_THIS_BADGE)}</h3>
    <p>{Localisation::getTranslation(Strings::ORG_MANAGE_BADGE_4)}:</p>
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
                            <h4>{Localisation::getTranslation(Strings::COMMON_DISPLAY_NAME)}:</h4>
                            <a  href="{urlFor name="user-public-profile" options="user_id.$user_id"}">
                                {$displayName}
                            </a>
                        </div>
                        <div class="right">
                            <form method="post" action="{urlFor name="org-manage-badge" options="org_id.$org_id|badge_id.$badge_id"}" class="">
                                <input type="hidden" name="user_id" value="{$user->getId()}" />
                                <input type="hidden" value="Remove" onClick="return confirmPost()" />
                                <a href="#" onclick="this.parentNode.submit()" class="pull-right btn btn-inverse">
                                    <i class="icon-fire icon-white"></i> {Localisation::getTranslation(Strings::ORG_MANAGE_BADGE_REMOVE_BADGE)}
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
            {Localisation::getTranslation(Strings::ORG_MANAGE_BADGE_5)}. {Localisation::getTranslation(Strings::ORG_MANAGE_BADGE_6)}.
        </p>
    {/if}

{include file="footer.tpl"}