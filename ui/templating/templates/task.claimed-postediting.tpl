{assign var=task_id value=$task->getId()}

<section>
    <div class="page-header">
        <h1>Post Editing task claimed <small>Please merge the relevent files!</small></h1>
    </div>
    
    <div class="alert alert-success">
        <strong>Success</strong> You have claimed the post editing task &ldquo;<strong>{$task->getTitle()}</strong>&rdquo;.
    </div>
</section>

<section>
    <h1>What now?</h1>

    <p>This this what you need to do (as soon as possible):</p>

    <ol>
        <li>Go <a href="{urlFor name="task" options="task_id.$task_id"}">here</a> to download each of the segments of the total file.</li>
        <li><strong>Merge the files back together</strong> and upload the merged file.</li>
    </ol>

    {if isset($user)}
        <p>We have also emailed you these instructions to <strong>{$user->getEmail()}</strong>.</p>
    {/if}
</section>

<section>
    <h3>Want to get started?</h3>
    <p></p>
    <p>
        <a href="{urlFor name="task" options="task_id.$task_id"}" class="btn btn-primary">
            <i class="icon-share-alt icon-white"></i> Merge Files
        </a>
        <a href="{urlFor name="home"}" class="btn">
            <i class="icon-arrow-left icon-black"></i> Go Back Home
        </a>
    </p>
</section>
