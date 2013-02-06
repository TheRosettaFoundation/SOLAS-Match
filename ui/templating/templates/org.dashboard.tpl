{include file="header.tpl"}

<div class="page-header">
	<h1>
        Organisation Dashboard <small>Overview of projects created by your organisation(s).</small>
    </h1>
</div>

{if isset($flash['success'])}
    <p class="alert alert-success">
        {$flash['success']}
    </p>
{/if}

{if isset($flash['error'])}
    <p class="alert alert-error">
        {$flash['error']}
    </p>
{/if}

{if isset($orgs)}
    <table class="table table-striped">
    {foreach $orgs as $org}
        {assign var="org_id" value=$org->getId()}
        <thead>
            <th style="text-align: left">
                <p style="margin-bottom:40px;"></p>
                <a href="{urlFor name="org-public-profile" options="org_id.$org_id"}">
                    <h4><i class="icon-briefcase"></i> {$org->getName()}</h4>
                </a>
            </th>
            <th>Deadline</th>
            <th>Status</th>
            <th>Word Count</th>
            <th>Created</th>
            <th>
                <a href="{urlFor name="org-private-profile" options="org_id.$org_id"}" class="btn btn-primary">
                    <i class="icon-wrench icon-white"></i> Edit Organisation
                </a>
            </th>
            <th>                    
                <a class="btn btn-success" href="{urlFor name="project-create" options="org_id.$org_id"}">
                    <i class="icon-upload icon-white"></i> Create Project
                </a>                    
            </th>  
        </thead>
        <tbody>
            
        {assign var="projectsData" value=$templateData[$org_id]}
        {if !is_null($projectsData)}
            {foreach from=$projectsData item=data}
                <tr>
                {assign var="projectObject" value=$data['project']}
                {assign var="project_id" value=$projectObject->getId()}
                    <td style="text-align: left">
                        <a href="{urlFor name="project-view" options="project_id.$project_id"}">{$projectObject->getTitle()}</a>
                    </td> 
                    <td>
                        {date(Settings::get("ui.date_format"), strtotime($data['project']->getDeadline()))}
                    </td>
                    <td><!-- Project Status - to be implemented -->
                            <b>0%</b>
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
            <td colspan="7" style="text-align: left">
                <p>This organisation has no projects listed.</p>
            </td>
        {/if}
   
        </tbody>
    {/foreach}
    </table> 
{else}
    <div class="alert alert-warning">
    <strong>What now?</strong> You don't have any tasks uploaded for your organisation. If you have content to be translated, please add a new task for that content.
    </div>
{/if}
<p style="margin-bottom:60px;"></p>
{include file="footer.tpl"}
