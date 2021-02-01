{include file="header.tpl"}

<div class="page-header">
    <h1>Add a New Tracking Code</h1>
</div>

{if isset($flash['success'])}
    <p class="alert alert-success" style="margin-bottom: 50px">
        {TemplateHelper::uiCleanseHTMLKeepMarkup($flash['success'])}
    </p>
{/if}

<p>
    Existing codes...<br />
    {foreach $referers as $referer}
        {$referer}<br />
    {/foreach}
</p>

<form method="post" action="{urlFor name="add_tracking_code"}">
    <p>
        New Code (keep short!): <input type='text' value="" name="tracking_code" id="tracking_code"/>
    </p>
    <button type="submit" value="Submit" class="btn btn-success">
        <i class="icon-upload icon-white"></i> {Localisation::getTranslation('common_submit')}
    </button>      
    {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
</form>

{include file="footer.tpl"}
