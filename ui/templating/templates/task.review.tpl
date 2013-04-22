{include file="header.tpl"}

<div class="page-header">
    <h1>Provide a Rating <small>How would you rate this task?</small></h1>
</div>

<p>
    Thank you for contributing to this project. Please provide a rating for the file(s) you just 
    {$action} based on the following criteria:
</p>

<form class="well" method="post" action="{urlFor name="task-review" options="task_id.$taskId"}">
    {foreach $tasks as $task}
        <h2>{$task->getTitle()}</h2>
        {if $task->getId() != null}
            {assign var="id" value=$task->getId()}
            <p>You can redownload this source file 
                <a href="{urlFor name="download-task-latest-version" options="task_id.$id"}">here</a>.
            </p>
        {else}
            {assign var="id" value=$task->getProjectId()}
            <p>You can download the project file
                <a href="{urlFor name="download-project-file" options="project_id.$id"}">here</a>.
            </p>
        {/if}
        <h3>Corrections <small>Were there many mistakes in the source file?</h3>
        <p><i>(1 - 5 where 5 = "few errors" and 1 = "a lot of errors")</i></p>
        <input type="text" name="corrections_{$id}" value="3" />

        <h3>Grammar <small>How was the use of grammar in the source file?</small></h3>
        <p><i>(1 - 5 where 5 = "few errors" and 1 = "a lot of errors")</i></p>
        <input type="text" name="grammar_{$id}" value="3" />

        <h3>Spelling <small>Were there many spelling errors in the source file?</small></h3>
        <p><i>(1 - 5 where 5 = "few errors" and 1 = "a lot of errors")</i></p>
        <input type="text" name="spelling_{$id}" value="3" />

        <h3>Consistency <small>Were there any errors in consistency in the source file?</small></h3>
        <p><i>(1 - 5 where 5 = "few errors" and 1 = "a lot of errors")</i></p>
        <input type="text" name="consistency_{$id}" value="3" />

        <h3>Comment <small>An optional comment about the file in general</small></h3>
        <textarea name="comment_{$id}" cols='40' rows='10' style="width: 80%">
        </textarea>
    {/foreach}

    <br />
    <button class="btn btn-primary" type="submit" name="submitReview">
        <i class="icon-upload icon-white"></i> Submit
    </button>
</form>

{include file="footer.tpl"}
