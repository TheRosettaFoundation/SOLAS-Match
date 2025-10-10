{include file="header.tpl"}

<div class="page-header">
    <h1>Complete Quality Task for <h2>{TemplateHelper::uiCleanseHTML($task->getTitle())} <small>What is your quality overview of these completed file/language segments?</small></h1>
</div>

{include file="handle-flash-messages.tpl"}

<p>
    {Localisation::getTranslation('task_review_1')}
</p>

{assign var="task_id" value=$task->getId()}
<form class="well" method="post" action="{urlFor name="task_complete" options="task_id.$task_id"}" onsubmit="return validate_text_size()" id="TaskReviewForm" accept-charset="utf-8">
    <div id="placeholder_for_errors_1"></div>

    <h3>Quality Overview (minimum: 50 characters, maximum: 1000 characters)</h3>
    <div id="count"></div>
    <textarea name="comment" cols='40' rows='10' style="width: 80%"></textarea>

    <div id="placeholder_for_errors_2"></div>
    <button class="btn btn-primary" type="submit" name="submitReview">
        <i class="icon-upload icon-white"></i> Submit Quality Overview
    </button>

    {if isset($sesskey)}<input type="hidden" name="sesskey" value="{$sesskey}" />{/if}
</form>

<script type="text/javascript">
    document.getElementById('comment').onkeyup = function () {
        document.getElementById('count').innerHTML = "Characters left: " + (1000 - this.value.length);
        if (this.value.length < 50 || this.value.length > 1000) document.getElementById("count").style.color = "red";
        else                                                    document.getElementById("count").style.color = "black";
    };

    function validate_text_size()
    {
        const length = document.getElementById("comment").textLength;
        if (length < 50 || length > 1000) {
            set_all_errors_for_submission();
            return false;
        }
        return true;
    }

    function set_all_errors_for_submission()
    {
        set_errors_for_submission("placeholder_for_errors_1", "error-box-top");
        set_errors_for_submission("placeholder_for_errors_2", "error-box-btm");
    }

    function set_errors_for_submission(id, id_for_div)
    {
        let html = '<div id="' + id_for_div + '" class="alert alert-error pull-left">';
        html += '<h3>Please correct the following errors:</h3>';
        html += '<ol>';
        if (length <   50) html += '<li>You must enter at least 50 characters.</li>';
        if (length > 1000) html += '<li>You cannot enter more than 1000 characters.</li>';
        html += '</ol>';
        html += '</div>';
        document.getElementById(id).innerHTML = html;
    }
</script>

{include file="footer.tpl"}
