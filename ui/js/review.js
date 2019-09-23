<script type="text/javascript">

    function areRatingsSetThenCreateHiddenFields()
    {
        var corrRate;
        var gramRate;
        var spellRate;
        var consRate;
        var i;

//TEST
var form = $("#TaskReviewForm");
alert(
((typeof form.comment_8799 === "undefined") ? "8799 undefined, " : "8799 Defined, ") +
((typeof form.submitReview === "undefined") ? "submitReview undefined, " : "submitReview Defined, ") +
((typeof form.skip === "undefined") ? "skip undefined, " : "skip Defined, ")
);
return false;
//TEST
        for (i = 0; i < taskIds.length; i++) {
            try {
                corrRate = $("#rateit_corrections_" + taskIds[i].toString()).rateit('value');
                if (isNaN(corrRate)) {
                    corrRate = $("#rateit_accuracy_"    + taskIds[i].toString()).rateit('value');
                }
            } catch (err) {
                corrRate = $("#rateit_accuracy_"    + taskIds[i].toString()).rateit('value');
            }

            try {
                gramRate = $("#rateit_grammar_" + taskIds[i].toString()).rateit('value');
                if (isNaN(gramRate)) {
                    gramRate = $("#rateit_fluency_" + taskIds[i].toString()).rateit('value');
                }
            } catch (err) {
                gramRate = $("#rateit_fluency_" + taskIds[i].toString()).rateit('value');
            }

            try {
                spellRate = $("#rateit_spelling_"    + taskIds[i].toString()).rateit('value');
                if (isNaN(spellRate)) {
                    spellRate = $("#rateit_terminology_" + taskIds[i].toString()).rateit('value');
                }
            } catch (err) {
                spellRate = $("#rateit_terminology_" + taskIds[i].toString()).rateit('value');
            }

            try {
                consRate = $("#rateit_consistency_"    + taskIds[i].toString()).rateit('value');
                if (isNaN(consRate)) {
                    consRate = parseInt($("#rateit_style_" + taskIds[i].toString()).rateit('value')) + 10*parseInt($("#rateit_design_" + taskIds[i].toString()).rateit('value'));
                }
            } catch (err) {
                consRate = parseInt($("#rateit_style_" + taskIds[i].toString()).rateit('value')) + 10*parseInt($("#rateit_design_" + taskIds[i].toString()).rateit('value'));
            }

            if (corrRate == 3 && gramRate == 3 && spellRate == 3 && (consRate == 3 || consRate == 33)) {
                set_all_errors_for_submission();
                return false;
            }
        }

        for (i = 0; i < taskIds.length; i++) {
            var form = $("#TaskReviewForm");

            try {
                corrRate = $("#rateit_corrections_" + taskIds[i].toString()).rateit('value');
                if (isNaN(corrRate)) {
                    corrRate = $("#rateit_accuracy_"    + taskIds[i].toString()).rateit('value');
                }
            } catch (err) {
                corrRate = $("#rateit_accuracy_"    + taskIds[i].toString()).rateit('value');
            }
            var corrections = $("<input type=\"hidden\" name=\"corrections_" + taskIds[i] + "\" />").attr("value", corrRate);
            form.append(corrections);

            try {
                gramRate = $("#rateit_grammar_" + taskIds[i].toString()).rateit('value');
                if (isNaN(gramRate)) {
                    gramRate = $("#rateit_fluency_" + taskIds[i].toString()).rateit('value');
                }
            } catch (err) {
                gramRate = $("#rateit_fluency_" + taskIds[i].toString()).rateit('value');
            }
            var grammar = $("<input type=\"hidden\" name=\"grammar_" + taskIds[i] + "\" />").attr("value", gramRate);
            form.append(grammar);

            try {
                spellRate = $("#rateit_spelling_"    + taskIds[i].toString()).rateit('value');
                if (isNaN(spellRate)) {
                    spellRate = $("#rateit_terminology_" + taskIds[i].toString()).rateit('value');
                }
            } catch (err) {
                spellRate = $("#rateit_terminology_" + taskIds[i].toString()).rateit('value');
            }
            var spelling = $("<input type=\"hidden\" name=\"spelling_" + taskIds[i] + "\" />").attr("value", spellRate);
            form.append(spelling);

            try {
                consRate = $("#rateit_consistency_"    + taskIds[i].toString()).rateit('value');
                if (isNaN(consRate)) {
                    consRate = parseInt($("#rateit_style_" + taskIds[i].toString()).rateit('value')) + 10*parseInt($("#rateit_design_" + taskIds[i].toString()).rateit('value'));
                }
            } catch (err) {
                consRate = parseInt($("#rateit_style_" + taskIds[i].toString()).rateit('value')) + 10*parseInt($("#rateit_design_" + taskIds[i].toString()).rateit('value'));
            }
            var consistency = $("<input type=\"hidden\" name=\"consistency_" + taskIds[i] + "\" />").attr("value", consRate);
            form.append(consistency);
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
        var html;
        html  = '<div id="' + id_for_div + '" class="alert alert-error pull-left">';
        html += '<h3>Please correct the following errors:</h3>';
        html += '<ol>';
        html += '<li>At least one rating must be changed from 3 stars.</li>';
        html += '</ol>';
        html += '</div>';
        document.getElementById(id).innerHTML = html;
    }
</script>
