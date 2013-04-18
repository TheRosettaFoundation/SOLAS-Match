<script type="text/javascript">

    function drawLines()
    {
        var svg = document.getElementById("project-view");
        for (var i = 0; i < languageList.length ; i++) {
            var taskArray = languageTasks[languageList[i]];
            for (var k = 0; k < taskArray.length; k++) {
                var languageBox = $("#graph_" + languageList[i]);
                var icon = $("#task_" + taskArray[k]);
                var rect = $("#rect_" + taskArray[k]);
                var X_e = parseInt(icon.attr("x")) + parseInt(languageBox.attr("x"));
                var Y_e = parseInt(icon.attr("y")) + parseInt(languageBox.attr("y")) + (parseInt(rect.attr("height")) / 2);
                preReqList = preReqs[taskArray[k]];
                for (var j = 0; j < preReqList.length; j++) {
                    var preReq = $("#task_" + preReqList[j]);
                    var colour = $("#task-status_" + preReqList[j]).attr("fill");
                    rect = $("#rect_" + preReqList[j]);
                    var X_s = parseInt(preReq.attr("x")) + parseInt(languageBox.attr("x")) + parseInt(rect.attr("width"));
                    var Y_s = parseInt(preReq.attr("y")) + parseInt(languageBox.attr("y")) + parseInt(rect.attr("height")) / 2;
                    var Y_m = Y_s + ((Y_e - Y_s) / 2);
                    var X_m = X_s + ((X_e - X_s) / 2);
                    var line = document.createElementNS('http://www.w3.org/2000/svg', "polyline");
                    line.setAttribute("points", X_s + "," + Y_s + " " + X_m + "," + Y_m + " " + X_e + "," + Y_e);
                    line.setAttribute("marker-mid", "url(#triangle)");
                    line.setAttribute("style", 'stroke-width:2;stroke:' + colour);
                    svg.appendChild(line);
                }
            }
        }
    }

    function repositionElements()
    {
        posArray = new Array();
        for (var i = 0; i < languageList.length; i++) {
            taskArray = languageTasks[languageList[i]];
            for (var j = 0; j < taskArray.length; j++) {
                preReqList = preReqs[taskArray[j]];
                if (preReqList.length > 1) {
                    total = 0;
                    for (var k = 0; k < preReqList.length; k++) {
                        total += parseInt($("#task_" + preReqList[k]).attr("y"));
                    }
                    $("#task_" + taskArray[j]).attr("y", total / preReqList.length);
                }
                postReqList = postReqs[taskArray[j]];
                if (postReqList.length > 1) {
                    total = 0;
                    for (var k = 0; k < postReqList.length; k++) {
                        total += parseInt($("#task_" + postReqList[k]).attr("y"));;
                    }
                    newY = (total / postReqList.length);
                    $("#task_" + taskArray[j]).attr("y", newY);
                }
            }
        }
    }

    function prepareGraph()
    {
        repositionElements();
        drawLines();
    }
</script>