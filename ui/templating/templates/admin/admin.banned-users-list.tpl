
<div id="accordionBannedUsers">
    <h3>{sprintf(Localisation::getTranslation('admin_banned_users_list_all_banned_users_of'), $siteName)}</h3>
    <div name="bannedOrgList">
        <table class="table table-striped">
            <thead>
                <th>{Localisation::getTranslation('admin_banned_users_list_user_display_name')}</th>
                <th>{Localisation::getTranslation('common_banned_by')}</th> 
                <th>{Localisation::getTranslation('common_ban_duration')}</th>
                <th>{Localisation::getTranslation('common_ban_reason')}</th>
                <th>{Localisation::getTranslation('common_banned_date')}</th>
                <th style="width: 12%">{Localisation::getTranslation('common_restore')}</th>
            </thead>
            {foreach $bannedUserList as $bannedUser}
                <tr>
                    <td>                
                        <a href="{urlFor name="user-public-profile" options="user_id.{$bannedUser->getUserId()}"}">
                            {TemplateHelper::uiCleanseHTML($bannedUserNames[$bannedUser->getUserId()])}
                        </a>
                    </td>
                    <td>                
                        <a href="{urlFor name="user-public-profile" options="user_id.{$bannedUser->getUserIdAdmin()}"}">
                            {TemplateHelper::uiCleanseHTML($bannedUserAdminNames[$bannedUser->getUserId()])}
                        </a>
                    </td>
                    <td>
                        {if $bannedUser->getBanType() == BanTypeEnum::DAY}
                            {Localisation::getTranslation('common_day')}
                        {elseif $bannedUser->getBanType() == BanTypeEnum::WEEK}
                            {Localisation::getTranslation('common_week')}
                        {elseif $bannedUser->getBanType() == BanTypeEnum::MONTH}
                            {Localisation::getTranslation('common_month')}
                        {elseif $bannedUser->getBanType() == BanTypeEnum::PERMANENT}
                            {Localisation::getTranslation('common_permanent')}
                        {/if}
                    </td>
                    <td>                
                        {TemplateHelper::uiCleanseHTML($bannedUser->getComment())}
                    </td>
                    <td>                
                        {date(Settings::get("ui.date_format"), strtotime($bannedUser->getBannedDate()))}
                    </td>
                    <td>
                        <form method="post" action="{urlFor name="site-admin-dashboard" options="user_id.{$adminUserId}"}">
                            <i class="icon-upload icon-white" style="position:relative; right:-25px; top:1px;"></i>
                            <input type="hidden" name="userId" value="{$bannedUser->getUserId()}" />
                            <input type="submit" class='btn btn-primary' name="unBanUser" value="    {Localisation::getTranslation('common_restore')}" 
                           onclick="return confirm('{Localisation::getTranslation('admin_banned_users_list_confirm_unban')}')"/>
                            <input type="hidden" name="sesskey" value="{$sesskey}" />
                        </form> 
                    </td>
                </tr>
            {/foreach}
        </table>
    </div>
</div>

