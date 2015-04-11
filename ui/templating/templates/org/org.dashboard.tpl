{include file="header.tpl"}

    <div class="page-header" style="margin-bottom: 50px">
        <h1>
            {Localisation::getTranslation('org_dashboard_organisation_dashboard')} <small>{Localisation::getTranslation('org_dashboard_0')}</small>
        </h1>
    </div>

{if isset($flash['success'])}
    <p class="alert alert-success" style="margin-bottom: 50px">
        {$flash['success']}
    </p>
{/if}

{if isset($flash['error'])}
    <p class="alert alert-error" style="margin-bottom: 50px">
        {$flash['error']}
    </p>
{/if}


{if isset($orgs)}
    {foreach $orgs as $org}
        {assign var="org_id" value=$org->getId()}

        <div style="display: inline-block; overflow-wrap: break-word; word-break:break-all; font-weight: bold; font-size: large; max-width: 70%">
            <a href="{urlFor name="org-public-profile" options="org_id.$org_id"}">
                <i class="icon-briefcase"></i> {$org->getName()}
            </a>
        </div>
        <div style="display: inline-block; float: right; font-weight: bold; font-size: large">
            <a href="{urlFor name="org-private-profile" options="org_id.$org_id"}" class="btn btn-primary">
                <i class="icon-wrench icon-white"></i> {Localisation::getTranslation('org_dashboard_edit_organisation')}
            </a>
            <a class="btn btn-success" href="{urlFor name="project-create" options="org_id.$org_id"}">
                <i class="icon-upload icon-white"></i> {Localisation::getTranslation('common_create_project')}
            </a>  
        </div>
                
        <hr />           
        <table class="table table-striped" style="overflow-wrap: break-word; word-break:break-all; margin-bottom:50px">
        <thead>
            <th>{Localisation::getTranslation('common_title')}</th>
            <th>{Localisation::getTranslation('common_deadline')}</th>
            <th>{Localisation::getTranslation('common_status')}</th>
            <th>{Localisation::getTranslation('common_word_count')}</th>
            <th>{Localisation::getTranslation('common_created')}</th>
            <th>{Localisation::getTranslation('common_edit')}</th>
            <th>{Localisation::getTranslation('common_archive')}</th>  
        </thead>
        <tbody>
            
        {assign var="projectsData" value=$templateData[$org_id]}
        {if !is_null($projectsData)}
            {foreach from=$projectsData item=data}
                <tr style="overflow-wrap: break-word;">
                {assign var="projectObject" value=$data['project']}
                {assign var="project_id" value=$projectObject->getId()}
                <td width="27.5%">
                        <a href="{urlFor name="project-view" options="project_id.$project_id"}">{$projectObject->getTitle()}</a>
                    </td> 
                    <td>
                        {date(Settings::get("ui.date_format"), strtotime($data['project']->getDeadline()))}
                    </td>
                    <td>
                        {assign var="projectStatus" value=intval(($data['project']->getStatus()*100))}
                        
                        {if $projectStatus == 100}                            
                            <strong style="color: #2F8518">{$projectStatus}%</strong>
                        {else if $projectStatus > 66}
                            <strong style="color: #598518">{$projectStatus}%</strong>
                        {else if $projectStatus > 33}
                            <strong style="color: #857818">{$projectStatus}%</strong>
                        {else}
                            <strong style="color: #851818">{$projectStatus}%</strong>
                        {/if}
                    </td>
                    <td>                      
                        {if $data['project']->getWordCount() != ''}
                            {$data['project']->getWordCount()}
                        {else}
                            -
                        {/if}
                    </td>
                    <td>
                        {date(Settings::get("ui.date_format"), strtotime($data['project']->getCreatedTime()))}
                    </td>
                    <td>
                        <a href="{urlFor name="project-alter" options="project_id.$project_id"}" class="btn btn-small">
                            <i class="icon-wrench icon-black"></i> {Localisation::getTranslation('common_edit_project')}
                        </a>
                    </td>
                    <td>
                        <a href="{urlFor name="archive-project" options="project_id.$project_id"}" class="btn btn-inverse" 
                            onclick="return confirm('{Localisation::getTranslation('org_dashboard_1')}')">
                            <i class="icon-fire icon-white"></i> {Localisation::getTranslation('org_dashboard_archive_project')}
                        </a>
                    </td>
                </tr>
            {/foreach}
        {else}
        <td colspan="7">
                <p class="alert alert-info">
                    {Localisation::getTranslation('org_dashboard_3')}
                </p>
            </td>
        {/if}
   
        </tbody>
        </table> 
    {/foreach}

{else}
    <div class="alert alert-warning">
    <strong>{Localisation::getTranslation('common_what_happens_now')}?</strong> {Localisation::getTranslation('org_dashboard_3')}
    </div>
{/if}
<p style="margin-bottom:60px;"/>
{include file="footer.tpl"}
