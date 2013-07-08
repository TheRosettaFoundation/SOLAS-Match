{include file="header.tpl"}

    <h1 class="page-header">
        {Localisation::getTranslation(Strings::ORG_ASSIGN_BADGE_ASSIGN_A_BADGE)}
        <small>{Localisation::getTranslation(Strings::ORG_ASSIGN_BADGE_0)}</small>
    </h1>

    <h3>{Localisation::getTranslation(Strings::ORG_ASSIGN_BADGE_0)}</h3>
    <p>{Localisation::getTranslation(Strings::ORG_ASSIGN_BADGE_1)}</p>

    {assign var="badge_id" value=$badge->getId()}
    badge id: {$badge_id}<br/>
    <form method="post" action="{urlFor name="org-manage-badge" options="org_id.$org_id|badge_id.$badge_id"}" class="well">
        <label for="email">{Localisation::getTranslation(Strings::ORG_ASSIGN_BADGE_2)}</label>
        <input type='text' name='email' id='email' />

        {if isset($flash['error'])}
            <div class="alert alert-error">
                <strong>Warning!</strong> {$flash['error']}
            </div>
        {/if}

        <p>
            <button type='submit' class='btn btn-primary' name='submit'>
                <i class="icon-plus-sign icon-white"></i> {Localisation::getTranslation(Strings::COMMON_ASSIGN)}
            </button>
        </p>
    </form>

{include file="footer.tpl"}
