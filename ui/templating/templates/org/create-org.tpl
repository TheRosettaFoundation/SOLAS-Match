{include file="header.tpl"}

{if $isSiteAdmin || (Settings::get('site.org_creation') == 'y')}

<h1 class="page-header">
    {Localisation::getTranslation('create_org_create_an_organisation')}
    <small>
        {Localisation::getTranslation('create_org_create_your_own_organisation')}
    </small><br/>
    <small>
        {Localisation::getTranslation('common_denotes_a_required_field')}
    </small>
</h1>

{include file="handle-flash-messages.tpl"}

    <form method='post' action="{urlFor name="create-org"}" class='well' accept-charset="utf-8">
        <table>

{include file="org/org-private-profile-edit.tpl"}

            <tr>
                <td colspan="2" align="center">
                    <button type="submit" name="submit" value="createOrg" class="btn btn-success">
                        <i class="icon-star icon-white"></i> {Localisation::getTranslation('common_create_organisation')}
                    </button>
                </td>
            </tr>
        </table>
    </form>

{else}

<h1 class="page-header">
{Localisation::getTranslation('thank_registering_organisation')}
</h1>
{Localisation::getTranslation('registrations_twb')}

{/if}

{include file="footer.tpl"}
