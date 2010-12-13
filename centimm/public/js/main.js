//ogólne funkcje które mają działać wszędzie

$(document).ready (function () {
	$('input.datepicker').datepicker({
		dateFormat: 'yy-mm-dd',
		dayNamesMin: ['Nie', 'Pn', 'Wt', 'Śr', 'Czw', 'Pt', 'So'],
		firstDay: 1,
		monthNames: ['Styczeń', 'Luty', 'Marzec', 'Kwiecień', 'Maj', 'Czerwiec', 'Lipiec', 'Sierpień', 'Wrzesień', 'Październik', 'Listopad', 'Grudzień'],
	});
});