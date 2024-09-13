{include file="header.tpl"}

<div class="page-header">
    <h1>Change Project Owner</h1>
</div>

{if isset($flash['success'])}
    <div class="alert alert-success">
        <p><strong>{Localisation::getTranslation('common_success')}!</strong> {$flash['success']}</p>
    </div>
{/if}

{if isset($error)}
    <div class="alert alert-error">
        <strong>{Localisation::getTranslation('common_error')}!:</strong> {$error}
    </div>
{/if}

{if isset($warning)}
    <div class="alert">
        <strong>{Localisation::getTranslation("common_warning")}:</strong> {$warning}
    </div>
{/if}

<form method="post" action="{urlFor name="change_owner" options="project_id.$project_id"}" class="well" accept-charset="utf-8">
    <label for="owner_id"><strong>New Owner</strong></label>
    <select name="owner_id" id="owner_id" class="form-select">
        <option value="0" selected="selected">Select New Project Owner...</option>
        {foreach $admin_list as $admin}
        <option value="{$admin['id']}">{TemplateHelper::uiCleanseHTML($admin['email'])}</option>
        {/foreach}
    </select>
    <p>
        <button type="submit" class="btn btn-success" name="submit">
            <i class="icon-star icon-white"></i> Change Owner
        </button>
    </p>
    {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
</form>

{include file="footer.tpl"}
