{include file="header.tpl"}

    <div class="page-header">
        <h1>
            {Localisation::getTranslation('org_dashboard_organisation_dashboard')} <small>{Localisation::getTranslation('org_dashboard_0')}</small>
        </h1>
    </div>

{if isset($flash['success'])}
    <p class="alert alert-success" style="margin-bottom: 50px">
        {TemplateHelper::uiCleanseHTMLKeepMarkup($flash['success'])}
    </p>
{/if}

{if isset($flash['error'])}
    <p class="alert alert-error" style="margin-bottom: 50px">
        {TemplateHelper::uiCleanseHTMLKeepMarkup($flash['error'])}
    </p>
{/if}


{if isset($orgs)}

{Localisation::getTranslation('task_twitter_0_org_dashboard')} <a class="twitter-share-button"
  href="https://twitter.com/intent/tweet?text={Localisation::getTranslation('task_twitter_2')}&url=https%3A%2F%2Ftrommons.org"
  data-size="large" data-counturl="https://trommons.org">
Tweet</a>
<br /><br />

    {foreach $orgs as $org}
        {assign var="org_id" value=$org->getId()}

        <div style="display: inline-block; overflow-wrap: break-word; word-break:break-all; font-weight: bold; font-size: large; max-width: 70%">
            <a href="{urlFor name="org-public-profile" options="org_id.$org_id"}">
                <i class="icon-briefcase"></i> {$org->getName()}
            </a>
            <a href="{urlFor name="org-projects" options="org_id.$org_id"}">
                (<i class="icon-briefcase"></i> All Projects)
            </a>
        </div>
        <div style="display: inline-block; float: right; font-weight: bold; font-size: large">

            {if $adminForOrg[$org_id]}
            <a href="{urlFor name="org-private-profile" options="org_id.$org_id"}" class="btn btn-primary">
                <i class="icon-wrench icon-white"></i> {Localisation::getTranslation('org_dashboard_edit_organisation')}
            </a>
            {/if}

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
                {assign var="project_id" value=$projectObject['id']}
                <td width="27.5%">
                        <a href="{urlFor name="project-view" options="project_id.$project_id"}">{TemplateHelper::uiCleanseHTMLNewlineAndTabs($projectObject['title'])}</a>
                    </td> 
                    <td>
                        <div class="convert_utc_to_local" style="visibility: hidden">{$data['project']['deadline']}</div>
                    </td>
                    <td>
                        {if is_numeric($data['project']['status'])}
                        {assign var="projectStatus" value=intval(($data['project']['status']*100))}
                        {else}
                        {assign var="projectStatus" value=0}
                        {/if}

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
                        {if $data['project']['wordCount'] != ''}
                            {$data['project']['wordCount']}
                        {else}
                            -
                        {/if}
                    </td>
                    <td>
                        <div class="convert_utc_to_local" style="visibility: hidden">{$data['project']['createdTime']}</div>
                    </td>
                    <td>
                        <a href="{urlFor name="project-alter" options="project_id.$project_id"}" class="btn btn-small">
                            <i class="icon-wrench icon-black"></i> {Localisation::getTranslation('common_edit_project')}
                        </a>
                    </td>
                    <td>
                        {if $isSiteAdmin}
                            {if !isset($sesskey)}{assign var="sesskey" value="0"}{/if}
                            <a href="{urlFor name="archive-project" options="project_id.$project_id|sesskey.{$sesskey}"}" class="btn btn-inverse" 
                            onclick="return confirm('{Localisation::getTranslation('org_dashboard_1')}')">
                            <i class="icon-fire icon-white"></i> {Localisation::getTranslation('org_dashboard_archive_project')}
                            </a>
                        {/if}
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
