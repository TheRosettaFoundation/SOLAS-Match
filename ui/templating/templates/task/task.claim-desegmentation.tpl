{assign var=task_id value=$task->getId()}

    <section>
        <div class="page-header">
            <h1>{$task->getTitle()} <small>Post Editing task</small></h1>
        </div>
    </section>

    <section>
        <h2>Do you want to merge files related to this task? <small>After downloading</small></h2>
        <hr />
        <h3>Review this checklist before you claim <small>Will you be able to merge these files?</small></h3>
        <p style="margin-bottom:20px;"></p>
        <ol>
            {if $taskMetadata->getContentType() != ''}
                <li>Can you open files of type <strong>{$taskMetadata->getContentType()}</strong> on your computer?</li>
            {/if}
            <li><strong>Will you have enough time to merge</strong> these files? Check how long the file is.</li>
            <li>Do you think you're capable of merging files in <strong>{$targetLanguage->getName()}</strong>?</li>
        </ol>
    </section>

    <section>
        <h3>It&rsquo;s time to decide</h3>
        <p> 
            Do you want to merge these files? The files you need to merge can be downloaded after claiming.
        </p>
        <p> 
            <form class="well" method="post" action="{urlFor name="task-claim-page" options="task_id.$task_id"}">
                <button type="submit" class="btn btn-primary">
                    <i class="icon-ok-circle icon-white"></i> Yes, I promise I will merge these files
                </button>
                <a href="{urlFor name="task" options="task_id.$task_id"}" class="btn">
                    <i class="icon-ban-circle icon-black"></i> No, just bring me back to the task page
                </a>
            </form>
        </p>
    </section>
