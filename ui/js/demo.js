// (function($){
//     $(function(){
//         $('#id_0').datetimepicker({
//             "allowInputToggle": true,
//             "showClose": true,
//             "showClear": true,
//             "showTodayButton": true,
//             "format": "MM/DD/YYYY hh:mm:ss A",
//         });
//         $('#id_1').datetimepicker({
//             "allowInputToggle": true,
//             "showClose": true,
//             "showClear": true,
//             "showTodayButton": true,
//             "format": "MM/DD/YYYY HH:mm:ss",
//         });
//         $('#id_2').datetimepicker({
//             "allowInputToggle": true,
//             "showClose": true,
//             "showClear": true,
//             "showTodayButton": true,
//             "format": "hh:mm:ss A",
//         });
//         $('#id_3').datetimepicker({
//             "allowInputToggle": true,
//             "showClose": true,
//             "showClear": true,
//             "showTodayButton": true,
//             "format": "HH:mm:ss",
//         });
//         $('#id_4').datetimepicker({
//             "allowInputToggle": true,
//             "showClose": true,
//             "showClear": true,
//             "showTodayButton": true,
//             "format": "MM/DD/YYYY",
//         });
//     });
// })(jQuery);

$(".form_datetime").datetimepicker({
    //language:  'fr',
    weekStart: 1,
    todayBtn: 1,
    autoclose: 1,
    todayHighlight: 1,
    startView: 2,
    forceParse: 0,
    showMeridian: 1,
});
$(".form_date").datetimepicker({
    language: "fr",
    weekStart: 1,
    todayBtn: 1,
    autoclose: 1,
    todayHighlight: 1,
    startView: 2,
    minView: 2,
    forceParse: 0,
});
$(".form_time").datetimepicker({
    language: "fr",
    weekStart: 1,
    todayBtn: 1,
    autoclose: 1,
    todayHighlight: 1,
    startView: 1,
    minView: 0,
    maxView: 1,
    forceParse: 0,
});
