{include file="header.tpl"}
{assign var=task_id value=$task->getId()}

<section>
	<div class="page-header">
		<h1>{$task->getTitle()} <small>Downloading your file preview...</small></h1>
	</div>
    <p style="margin-bottom:40px;"></p>
    <p>You should now have been asked to save the task file.</p>

    <p>Please <strong>download the file to your desktop.</strong></p>

    <p>If you want to start again, <a href="{urlFor name="download-task" options="task_id.$task_id"}">download the file preview again.</a></p>

        <p style="margin-bottom:40px;"></p>
	<h2>When you have saved the file to your desktop...</h2>
	<p>
        
        <p style="margin-bottom:20px;"></p>
            <a class="btn btn-primary" href="{urlFor name="task-claim-page" options="task_id.$task_id"}">
                <i class="icon-circle-arrow-down icon-white"></i> I Have Saved The File To My Desktop
            </a>
	</p>
</section>

<iframe src="{urlFor name="download-task" options="task_id.$task_id"}" width="1" height="1" frameborder="no"></iframe>

{include file="footer.tpl"}
