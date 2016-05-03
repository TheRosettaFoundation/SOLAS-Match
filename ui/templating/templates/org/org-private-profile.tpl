{include file='header.tpl'}

{if isset($org)}
    <div class='page-header'><h1>
    {if $org->getName() != ''}
        {$org->getName()}
    {else}
        {Localisation::getTranslation('common_organisation_profile')}
    {/if}
    <small>{Localisation::getTranslation('org_private_profile_alter_profile_here')}</small>
    {assign var="org_id" value=$org->getId()}
        <a href="{urlFor name="org-public-profile" options="org_id.$org_id"}" class="pull-right btn btn-primary">
            <i class="icon-list icon-white"></i> {Localisation::getTranslation('org_private_profile_public_profile')}
            </a>
        </h1>
    </div>
{else}
    header({urlFor name='home'});
{/if}

{include file="handle-flash-messages.tpl"}

{assign var="org_id" value=$org->getId()}
    <form method='post' action='{urlFor name='org-private-profile' options="org_id.$org_id"}' class='well' accept-charset="utf-8">
        <table>

{include file="org/org-private-profile-edit.tpl"}

            <tr>
                <td colspan="2" align="center">
                    <button type='submit' class='btn btn-primary' name='updateOrgDetails'>
                        <i class="icon-refresh icon-white"></i> {Localisation::getTranslation('org_private_profile_update_organisation_details')}
                    </button>
                    {if isset($orgAdmin)}
                        <button type="submit" class="btn btn-inverse" value="{$org_id}" name="deleteId"
                                onclick="return confirm('{Localisation::getTranslation('org_private_profile_confirm_delete')}');">
                            <i class="icon-fire icon-white"></i> {Localisation::getTranslation('org_private_profile_delete_organisation')}
                        </button>
                    {/if}
                </td>
            </tr>
        </table>
    </form>



{include file='footer.tpl'}
