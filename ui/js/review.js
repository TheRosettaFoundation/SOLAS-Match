<script type="text/javascript">

    function createHiddenFields()
    {
        for (var i = 0; i < taskIds.length; i++) {
            var form = $("#TaskReviewForm");

            var corrRate;
            try {
                corrRate = $("#rateit_corrections_" + taskIds[i].toString()).rateit('value');
            } catch (err) {
alert("#rateit_accuracy_"    + taskIds[i].toString());
alert($('#rateit_accuracy_29409').rateit('value'));
                corrRate = $("#rateit_accuracy_"    + taskIds[i].toString()).rateit('value');
            }
alert(corrRate);
            var corrections = $("<input type=\"hidden\" name=\"corrections_" + taskIds[i] + "\" />").attr("value", corrRate);
            form.append(corrections);

            var gramRate;
            try {
                gramRate = $("#rateit_grammar_" + taskIds[i].toString()).rateit('value');
            } catch (err) {
                gramRate = $("#rateit_fluency_" + taskIds[i].toString()).rateit('value');
            }
            var grammar = $("<input type=\"hidden\" name=\"grammar_" + taskIds[i] + "\" />").attr("value", gramRate);
            form.append(grammar);

            var spellRate;
            try {
                spellRate = $("#rateit_spelling_"    + taskIds[i].toString()).rateit('value');
            } catch (err) {
                spellRate = $("#rateit_terminology_" + taskIds[i].toString()).rateit('value');
            }
            var spelling = $("<input type=\"hidden\" name=\"spelling_" + taskIds[i] + "\" />").attr("value", spellRate);
            form.append(spelling);

            var consRate;
            try {
                consRate = $("#rateit_consistency_"    + taskIds[i].toString()).rateit('value');
            } catch (err) {
                consRate = parseInt($("#rateit_style_" + taskIds[i].toString()).rateit('value')) + 10*parseInt($("#rateit_design_" + taskIds[i].toString()).rateit('value'));
            }
            var consistency = $("<input type=\"hidden\" name=\"consistency_" + taskIds[i] + "\" />").attr("value", consRate);
            form.append(consistency);
        }
    }
</script>
