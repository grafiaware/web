
$('.editDate .button.toolsPage').click(function(){
    $(this).parent(".editDate").siblings(".editPage").css("display", "block");
    $(this).parent(".editDate").css("display", "none");
    $(this).parent(".editDate").siblings(".editDate").css("display", "none");
});
$('.editPage .button.toolsDate').click(function(){
    $(this).parent(".editPage").siblings(".editDate").css("display", "block");
    $(this).parent(".editPage").css("display", "none");
});

$('.edit_kalendar .ui.calendar').calendar({ 
    type: 'date',
    today: true,
    firstDayOfWeek: 1,
    text: {
        days: ['Ne', 'Po', 'Út', 'St', 'Čt', 'Pá', 'So'],
        months: ['Leden', 'Únor', 'Březen', 'Duben', 'Květen', 'Červen', 'Červenec', 'Srpen', 'Září', 'Říjen', 'Listopad', 'Prosinec'],
        today: 'Dnes'
    },
    formatter: {
      date: function (date, settings) {
        if (!date) return '';
        var day = date.getDate();
        var month = date.getMonth() + 1;
        var year = date.getFullYear();
        return day + '. ' + month + '. ' + year;}
    }
});


