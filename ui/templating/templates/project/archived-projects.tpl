{include file='header.tpl'}

<div class='page-header'>
    <h1>
        {if isset($user) && $user->getDisplayName() != ''}{$user->getDisplayName()}'s {else} {Localisation::getTranslation(Strings::COMMON_YOUR)}{/if}
        {Localisation::getTranslation(Strings::ARCHIVED_PROJECTS_ARCHIVED_PROJECTS)} <small>{Localisation::getTranslation(Strings::ARCHIVED_PROJECTS_0)}</small>
    </h1>
</div>

{if isset($archived_projects) && count($archived_projects) > 0}
    {for $count=$top to $bottom}
        {assign var="project" value=$archived_projects[$count]}
        {include file="project/project.profile-display.tpl" project=$project}
    {/for}
    {include file="pagination.tpl" url_name="archived-projects" current_page=$page_no last_page=$last}
{/if}

{include file='footer.tpl'}
