{include file="header.tpl"}

<div class="page-header">
	<h1>Task is now live</h1>
</div>

<div class="alert alert-success">
	<strong>Success</strong> Your task has been uploaded.
</div>

<h1>What now? <small>Wait for translators</small></h1>

<p>Here's what will now happen:</p>

<ol>
	<li>Volunteer translators will see the new job listed</li>
	<li>If a volunteer translator is interested, they will download your job and upload it</li>
	<li>This may take several days or weeks, depending on the job</li>
</ol>

<p><a href="{urlFor name="home"}" class="btn btn-primary">Back to Home</a> <a href="{urlFor name="task-upload" options="org_id.$org_id"}" class="btn">Add a new task</a> </p>

{include file="footer.tpl"}
