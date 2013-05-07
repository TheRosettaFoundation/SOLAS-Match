{include file="header.tpl"}

<div class="page-header">
    <h1>Provide a Rating <small>How would you rate this task?</small></h1>
</div>

{include file="handle-flash-messages.tpl"}

<p>
    Thank you for contributing to this project. Please provide a rating for the file(s) you just 
    {$action} based on the following criteria:
</p>

{include file="task/task.review-form.tpl"}

{include file="footer.tpl"}
