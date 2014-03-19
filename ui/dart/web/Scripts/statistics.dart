import 'dart:html';
import 'dart:async';
import 'dart:js';

import '../lib/SolasMatchDart.dart';

class PieChart
{
  var jsDataTable;
  var jsOptions;
  var jsChart;
  
  PieChart(Map data, Element element, Map options)
  {
    jsDataTable = new JsObject(context["google"]["visualization"]["DataTable"]);
    jsDataTable.callMethod('addColumn', ['string', 'data']);
    jsDataTable.callMethod('addColumn', ['number', 'quantity']);
    jsDataTable.callMethod('addRows', [new JsObject.jsify(data)]);
    jsOptions = new JsObject.jsify(options);
    jsChart = new JsObject(context["google"]["visualization"]['PieChart'], [element]);
    draw();
  }
  
  void draw()
  {
    jsChart.callMethod('draw', [jsDataTable, jsOptions]);
  }
  
  static Future load()
  {
    Completer c = new Completer();
    context['google'].callMethod('load',
        ['visualization', '1', new JsObject.jsify({'packages' : ['corechart'],
          'callback' : new JsFunction.withThis(c.complete)
        })]);
    return c.future;
  }
}

class LineChart
{
  var jsDataTable;
  var jsOptions;
  var jsChart;
  
  LineChart(Map data, Element element, Map options)
  {
    jsDataTable = new JsObject(context["google"]["visualization"]["DataTable"]);
    jsDataTable.callMethod('addColumn', ['string', 'data']);
    jsDataTable.callMethod('addColumn', ['number', 'quantity']);
    jsDataTable.callMethod('addRows', [new JsObject.jsify(data)]);
    jsOptions = new JsObject.jsify(options);
    jsChart = new JsObject(context["google"]["visualization"]['LineChart'], [element]);
    draw();
  }
  
  void draw()
  {
    jsChart.callMethod('draw', [jsDataTable, jsOptions]);
  }
  
  static Future load()
  {
    Completer c = new Completer();
    context['google'].callMethod('load',
        ['visualization', '1', new JsObject.jsify({'packages' : ['corechart'],
          'callback' : new JsFunction.withThis(c.complete)
        })]);
    return c.future;
  }
}

void main()
{
  Loader.load().then((_) {
    PieChart.load().then((_) {
      Element divElement = querySelector("#tasks_pie_chart");
      List<Future<bool>> completeList = new List<Future<bool>>();
      int waiting;
      completeList.add(StatisticDao.getStatistic("TasksWithPreReqs")
          .then((Statistic stat) {
            waiting = int.parse(stat.value);
            return true;
          }));
      int unclaimed;
      completeList.add(StatisticDao.getStatistic("UnclaimedTasks")
          .then((Statistic stat) {
            unclaimed = int.parse(stat.value);
            return true;
          }));
      int claimed;
      completeList.add(StatisticDao.getStatistic("ClaimedTasks")
          .then((Statistic stat) {
            claimed = int.parse(stat.value);
            return true;
          }));
      int complete;
      completeList.add(StatisticDao.getStatistic("CompleteTasks")
          .then((Statistic stat) {
            complete = int.parse(stat.value);
            return true;
          }));
      int archived;
      completeList.add(StatisticDao.getStatistic("ArchivedTasks")
          .then((Statistic stat) {
            archived = int.parse(stat.value);
            return true;
          }));
      Future.wait(completeList)
        .then((_) {
          var chartData = [
            ['Waiting', waiting],
            ['Unclaimed', unclaimed],
            ['Claimed', claimed],
            ['Complete', complete],
            ['Archived', archived]
          ];
          var options = {
            'title': "Task Data",
            'width': 400,
            'height': 300
          };
          PieChart userPieChart = new PieChart(chartData, divElement, options);
        });
    });
    LineChart.load().then((_) {
      Element divElement = querySelector("#user_activity");
      List<Future<int>> loadedList = new List<Future<int>>();
      List<String> countTitles = new List<String>();
      DateTime now = new DateTime.now();
      print("Now: " + now.toString());
      now = now.add(new Duration(days: 1));
      now = now.subtract(new Duration(hours: now.hour, minutes: now.minute, seconds: now.second));
      print("Now: " + now.toString());
      for (int i = 0; i < 14; i++) {
        DateTime other = now.subtract(new Duration(days: 1));
        loadedList.add(StatisticDao.getLoginCount(other.toString(), now.toString())
            .then((int count) {
              return count;
            }));
        switch (now.weekday) {
          case 1:
            countTitles.add("Monday");
            break;
          case 2:
            countTitles.add("Tuesday");
            break;
          case 3:
            countTitles.add("Wednesday");
            break;
          case 4:
            countTitles.add("Thursday");
            break;
          case 5:
            countTitles.add("Friday");
            break;
          case 6:
            countTitles.add("Saturday");
            break;
          case 7:
            countTitles.add("Sunday");
            break;
        }
        now = other;
      }
      
      var options = {
        'title': "User Activity",
        'width': 600,
        'height': 300
      };
      
      Future.wait(loadedList)
        .then((List<bool> logins) {
          var chartData = new List();
          for (int i = 13; i >= 0; i--) {
            chartData.add([countTitles.elementAt(i), logins.elementAt(i)]);
          }
          /*var chartData = [
            ['0', logins.elementAt(0)],
            ['1', logins.elementAt(1)],
            ['2', logins.elementAt(2)],
            ['3', logins.elementAt(3)],
            ['4', logins.elementAt(4)],
            ['5', logins.elementAt(5)],
            ['6', logins.elementAt(6)],
            ['7', logins.elementAt(7)],
            ['8', logins.elementAt(8)],
            ['9', logins.elementAt(9)],
            ['10', logins.elementAt(10)],
            ['11', logins.elementAt(11)],
            ['12', logins.elementAt(12)],
            ['13', logins.elementAt(13)]
          ];*/
          
          LineChart lineGraph = new LineChart(chartData, divElement, options);
        });
    });
  });
}