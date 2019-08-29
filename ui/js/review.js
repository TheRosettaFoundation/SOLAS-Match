<script type="text/javascript">

    function createHiddenFields()
    {
        for (var i = 0; i < taskIds.length; i++) {
            var form = $("#TaskReviewForm");
            var corrRate = $("#rateit_accuracy_" + taskIds[i].toString());
            var corrections = $("<input type=\"hidden\" name=\"corrections_" + taskIds[i] + "\" />").attr("value",
                    corrRate.rateit('value'));
            form.append(corrections);

            var grammar = $("<input type=\"hidden\" name=\"grammar_" + taskIds[i] + "\" />").attr("value",
                    $("#rateit_fluency_" + taskIds[i]).rateit('value'));
            form.append(grammar);

            var spelling = $("<input type=\"hidden\" name=\"spelling_" + taskIds[i] + "\" />").attr("value",
                    $("#rateit_terminology_" + taskIds[i]).rateit('value'));
            form.append(spelling);

            var consistency = $("<input type=\"hidden\" name=\"consistency_" + taskIds[i] + "\"/>").attr("value",
                    parseInt($("#rateit_style_" + taskIds[i]).rateit('value')) + 10*parseInt($("#rateit_design_" + taskIds[i]).rateit('value')));
            form.append(consistency);
        }
    }
</script>
