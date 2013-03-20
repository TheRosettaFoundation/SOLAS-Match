function drawLines()
{
    //alert("Drawing Lines");
    var svg = document.getElementById("project-view");
    for (var i = 0; i < allTasks.length ; i++) {
        //alert("Searching for task " + allTasks[i]);
        var icon = $("#task_" + allTasks[i]);
        var rect = $("#rect_" + allTasks[i]);
        var X_s = parseInt(icon.attr("x"));
        var Y_s = parseInt(icon.attr("y")) + parseInt(rect.attr("height")) / 2;
        preReqList = preReqs[allTasks[i]];
        for (var j = 0; j < preReqList.length; j++) {
            //alert("searching for preReq " + preReqList[j]);
            var preReq = $("#task_" + preReqList[j]);
            rect = $("#rect_" + allTasks[i]);
            var X_e = parseInt(preReq.attr("x")) + parseInt(rect.attr("width"));
            var Y_e = parseInt(preReq.attr("y")) + parseInt(rect.attr("height")) / 2;
            var line = document.createElementNS('http://www.w3.org/2000/svg', "line");
            line.setAttribute("x1", X_s);
            line.setAttribute("y1", Y_s);
            line.setAttribute("x2", X_e);
            line.setAttribute("y2", Y_e);
            line.setAttribute("style", 'stroke:rgb(0,0,0);stroke-width:2');
            svg.appendChild(line);
        }
    }
}

function repositionElements()
{
    for (var i = 0; i < allTasks.length; i++) {
        if (preReqs[allTasks[i]].length > 0) {
            var newY = 0;
            preReqList = preReqs[allTasks[i]];
            for (var j = 0; j < preReqList.length; j++) {
                newY += $("#task_" + preReqList[j]).attr("y");
            }
            newY = newY / preReqList.length;
            document.getElementById("task_" + allTasks[i]).setAttribute("y", newY);
        }
    }
}

function prepareGraph()
{
    //repositionElements();
    drawLines();
}
