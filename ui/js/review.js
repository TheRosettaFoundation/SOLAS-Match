<script type="text/javascript">

    function createHiddenFields()
    {
        for (var i = 0; i < taskIds.length; i++) {
            var form = $("#TaskReviewForm");
            var corrRate = $("#rateit_corrections_" + taskIds[i].toString());
            var corrections = $("<input type=\"hidden\" name=\"corrections_" + taskIds[i] + "\" />").attr("value",
                    corrRate.rateit('value'));
            form.append(corrections);

            var grammar = $("<input type=\"hidden\" name=\"grammar_" + taskIds[i] + "\" />").attr("value",
                    $("#rateit_grammar_" + taskIds[i]).rateit('value'));
            form.append(grammar);

            var spelling = $("<input type=\"hidden\" name=\"spelling_" + taskIds[i] + "\" />").attr("value",
                    $("#rateit_spelling_" + taskIds[i]).rateit('value'));
            form.append(spelling);

            var consistency = $("<input type=\"hidden\" name=\"consistency_" + taskIds[i] + "\"/>").attr("value",
                    $("#rateit_consistency_" + taskIds[i]).rateit('value'));
            form.append(consistency);
        }
    }
</script>
