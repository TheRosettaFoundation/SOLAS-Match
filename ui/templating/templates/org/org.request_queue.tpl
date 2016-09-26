{include file="header.tpl"}

    {assign var="org_id" value=$org->getId()}
    <h1 class="page-header">{$org->getName()}<small> {Localisation::getTranslation('org_request_queue_0')}</small></h1>

    {if isset($flash['error'])}
        <div class="alert alert-error">{TemplateHelper::uiCleanseHTML($flash['error'])}</div>
    {/if}

    {if isset($flash['success'])}
        <div class="alert alert-success">{TemplateHelper::uiCleanseHTML($flash['success'])}</div>
    {/if}

    <form class="well" method="post" action="{urlFor name="org-request-queue" options="org_id.$org_id"}" accept-charset="utf-8">
        <label for="email">{Localisation::getTranslation('org_request_queue_2')}</label>
        <input type="text" name="email" />
        <p>
            <input type="submit" value="    Add User" class="btn btn-primary" />
            <i class="icon-plus-sign icon-white" style="position:relative; right:88px; top:2px;"></i>
        </p>
    </form>

{include file="footer.tpl"}
