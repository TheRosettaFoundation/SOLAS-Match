{include file="header.tpl"}

    <div class="page-header">
        <h1>Task is now live!</h1>
    </div>

    <div class="alert alert-success">
        <p>
            <strong>Success</strong> - You have created a new task.
        </p>
        <p>
            It is now listed on your <a href="{urlFor name="project-view" options="project_id.$project_id"}">project view</a>.
        </p>
        <p>
            You can view the task details on the <a href="{urlFor name="task-view" options="task_id.$task_id"}">task view.</a>
        <p>        
    </div>

    <h1>What now? <small>Wait for translators.</small></h1>

    <p>Here's what will now happen:</p>
    <p style="margin-bottom:20px;"/>
    <ol>
            <li>Volunteer translators will see the new Task.</li>
            <li>If a volunteer translator is interested, they will claim the task, download it and upload their work.</li>
            <li>This may take several days or weeks, depending on the tasks.</li>
    </ol>
    <p style="margin-bottom:20px;"/>

    <p>
        <a href="{urlFor name="home"}" class="btn btn-primary">
            <i class="icon-arrow-left icon-white"></i> Back to Home
        </a>
        <a href="{urlFor name="task-create" options="project_id.$project_id"}" class="btn btn-success">
            <i class="icon-circle-arrow-up icon-white"></i> Add New Task
        </a> 
    </p>

{include file="footer.tpl"}
