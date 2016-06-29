{include file="header.tpl"}

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

{include file="footer.tpl"}
