{include file="header.tpl"}

<div class="page-header">
	<h1>Task is now live</h1>
</div>

<div class="alert alert-success">
	<strong>Success</strong> Your task has been uploaded.
</div>

<h1>What now? <small>Wait for translators</small></h1>

<p>Here's what will now happen:</p>
<p style="margin-bottom:20px;"></p>
<ol>
	<li>Volunteer translators will see the new task listed</li>
	<li>If a volunteer translator is interested, they will download your job and upload it</li>
	<li>This may take several days or weeks, depending on the task</li>
</ol>
<p style="margin-bottom:20px;"></p>

<p>
    <a href="{urlFor name="home"}" class="btn btn-primary">
        <i class="icon-arrow-left icon-white"></i> Back to Home
    </a>
    <a href="{urlFor name="task-upload" options="org_id.$org_id"}" class="btn btn-success">
        <i class="icon-circle-arrow-up icon-white"></i> Add New Task
    </a> 
</p>

{include file="footer.tpl"}
