
<div id="accordionBannedUsers">
    <h3>{Localisation::getTranslation(Strings::ADMIN_BANNED_USERS_LIST_ALL_BANNED_USERS_OF)} {$siteName}.</h3>
    <div name="bannedOrgList">
        <table class="table table-striped">
            <thead>
                <th>{Localisation::getTranslation(Strings::ADMIN_BANNED_USERS_LIST_USER_DISPLAY_NAME)}</th>
                <th>{Localisation::getTranslation(Strings::COMMON_BANNED_BY)}</th> 
                <th>{Localisation::getTranslation(Strings::COMMON_BAN_DURATION)}</th>
                <th>{Localisation::getTranslation(Strings::COMMON_BAN_REASON)}</th>
                <th>{Localisation::getTranslation(Strings::COMMON_BANNED_DATE)}</th>
                <th style="width: 12%">{Localisation::getTranslation(Strings::COMMON_RESTORE)}</th>
            </thead>
            {foreach $bannedUserList as $bannedUser}
                <tr>
                    <td>                
                        <a href="{urlFor name="user-public-profile" options="user_id.{$bannedUser->getUserId()}"}">{$bannedUser['user']->getDisplayName()}</a>
                    </td>
                    <td>                
                        <a href="{urlFor name="user-public-profile" options="user_id.{$bannedUser->getUserIdAdmin()}"}">{$bannedUser['adminUser']->getDisplayName()}</a>
                    </td>
                    <td>
                        {if $bannedUser->getBanType() == BanTypeEnum::DAY}
                            {Localisation::getTranslation(Strings::COMMON_DAY)}
                        {elseif $bannedUser->getBanType() == BanTypeEnum::WEEK}
                            {Localisation::getTranslation(Strings::COMMON_WEEK)}
                        {elseif $bannedUser->getBanType() == BanTypeEnum::MONTH}
                            {Localisation::getTranslation(Strings::COMMON_MONTH)}
                        {elseif $bannedUser->getBanType() == BanTypeEnum::PERMANENT}
                            {Localisation::getTranslation(Strings::COMMON_PERMANENT)}
                        {/if}
                    </td>
                    <td>                
                        {$bannedUser->getComment()}
                    </td>
                    <td>                
                        {date(Settings::get("ui.date_format"), strtotime($bannedUser->getBannedDate()))}
                    </td>
                    <td>
                        <form method="post" action="{urlFor name="site-admin-dashboard" options="user_id.{$adminUserId}"}">
                            <i class="icon-upload icon-white" style="position:relative; right:-25px; top:1px;"></i>
                            <input type="hidden" name="userId" value="{$bannedUser->getUserId()}" />
                            <input type="submit" class='btn btn-primary' name="unBanUser" value="    {Localisation::getTranslation(Strings::COMMON_RESTORE)}" 
                           onclick="return confirm('{Localisation::getTranslation(Strings::ADMIN_BANNED_USERS_LIST_0)}')"/>
                        </form> 
                    </td>
                </tr>
            {/foreach}
        </table>
    </div>
</div>

