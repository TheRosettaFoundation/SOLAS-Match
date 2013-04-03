<script type="text/javascript" src="{$app->urlFor("home")}ui/js/GraphHelper.js"></script>

<script type="text/javascript">
    window.onload = runStartup;

    function runStartup()
    {
        prepareGraph();
        $( \"#tabs\" ).tabs();
    }
</script>