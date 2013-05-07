{include file="header.tpl"}

    <div class="page-header" style="margin-bottom: 50px">
        <h1>
            Organisation Dashboard <small>Overview of projects created by your organisation(s).</small>
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
                <i class="icon-wrench icon-white"></i> Edit Organisation
            </a>
            <a class="btn btn-success" href="{urlFor name="project-create" options="org_id.$org_id"}">
                <i class="icon-upload icon-white"></i> Create Project
            </a>  
        </div>
                
        <hr />           
        <table class="table table-striped" style="overflow-wrap: break-word; word-break:break-all; margin-bottom:50px">
        <thead>
            <th>Title</th>
            <th>Deadline</th>
            <th>Status</th>
            <th>Word Count</th>
            <th>Created</th>
            <th>Edit</th>
            <th>Archive</th>  
        </thead>
        <tbody>
            
        {assign var="projectsData" value=$templateData[$org_id]}
        {if !is_null($projectsData)}
            {foreach from=$projectsData item=data}
                <tr style="overflow-wrap: break-word;">
                {assign var="projectObject" value=$data['project']}
                {assign var="project_id" value=$projectObject->getId()}
                    <td>
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
                            <i class="icon-wrench icon-black"></i> Edit Project
                        </a>
                    </td>
                    <td>
                        <a href="{urlFor name="archive-project" options="project_id.$project_id"}" class="btn btn-inverse">
                            <i class="icon-fire icon-white"></i> Archive Project
                        </a>
                    </td>
                </tr>
            {/foreach}
        {else}
        <td colspan="7">
                <p class="alert alert-info">
                    This organisation has no projects listed.
                </p>
            </td>
        {/if}
   
        </tbody>
        </table> 
    {/foreach}

{else}
    <div class="alert alert-warning">
    <strong>What now?</strong> You don't have any tasks uploaded for your organisation. If you have content to be translated, please add a new task for that content.
    </div>
{/if}
<p style="margin-bottom:60px;"/>
{include file="footer.tpl"}
