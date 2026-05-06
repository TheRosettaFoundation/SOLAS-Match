    $(".convert_utc_to_local_deadline_day_mon_year_stupid").each(function () {
        $(this).removeClass("convert_utc_to_local_deadline_day_mon_year_stupid");

        const formatter = new Intl.DateTimeFormat((new Intl.DateTimeFormat()).resolvedOptions().locale, {month: 'short', day: 'numeric', year: 'numeric'});

        $(this).html(formatter.format(new Date($(this).text().substring(0, 10))));

        $(this).css("visibility", "visible");
    });
