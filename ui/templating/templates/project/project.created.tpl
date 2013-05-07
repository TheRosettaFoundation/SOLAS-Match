{include file="header.tpl"}

    <div class="page-header">
            <h1>Project is now live!</h1>
    </div>

    <div class="alert alert-success">
        <p>
            <strong>Success</strong> - Your project has been uploaded.
        </p>
        <p>
            It is now listed on your organisations <a href="{urlFor name="org-dashboard" options="org_id.$org_id"}">dashboard</a>.
        </p>
        <p>
            You can view the project details and it's associated tasks on the <a href="{urlFor name="project-view" options="project_id.$project_id"}">project view.</a>
        <p>        
    </div>

    <h1>What now? <small>Wait for translators.</small></h1>

    <p>Here's what will now happen:</p>
    <p style="margin-bottom:20px;"/>
    <ol>
        <li>Volunteer translators will see the new Tasks associated with your project.</li>
        <li>If a volunteer translator is interested, they will claim a task, download it and upload their work.</li>
        <li>This may take several days or weeks, depending on the tasks.</li>
    </ol>
    <p style="margin-bottom:20px;"/>

    <p>
        <a href="{urlFor name="home"}" class="btn btn-primary">
            <i class="icon-arrow-left icon-white"></i> Back to Home
        </a>
        <a href="{urlFor name="project-create" options="org_id.$org_id"}" class="btn btn-success">
            <i class="icon-circle-arrow-up icon-white"></i> Add New Project
        </a> 
    </p>

{include file="footer.tpl"}
