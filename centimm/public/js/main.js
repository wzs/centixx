//ogólne funkcje które mają działać wszędzie
$(document).ready (function () {
	
	//datapickery
	$('input.datepicker').datepicker({
		dateFormat: 'yy-mm-dd',
		dayNamesMin: ['Nie', 'Pn', 'Wt', 'Śr', 'Czw', 'Pt', 'So'],
		firstDay: 1,
		monthNames: ['Styczeń', 'Luty', 'Marzec', 'Kwiecień', 'Maj', 'Czerwiec', 'Lipiec', 'Sierpień', 'Wrzesień', 'Październik', 'Listopad', 'Grudzień'],
	});
	
	//AJAXowe usuwanie uzytkowników (tabelka staffing)
	$('table.staffing a.delete').click(function() {
		if (confirm('Czy na pewno chcesz usunąć tego użytkownika?')) {
			var tr = $(this).parent().parent();
			$.get(this.href, null, function(response) {
				if (response == "true") {
					tr.fadeOut("slow");
				} else {
					alert('Wystąpił błąd!');
				}
			});
		} 
		
		return false;
	});
	
	//AJAXowe tworzenie kopii bazy danych
	$('#dbBackup').click(function() {
		var btn = $(this);
		var form = btn.parent();
		var orgLabel = btn.val();
		btn.attr('disabled', 'disabled').val('Tworzenie kopii...');
		$.get(form.attr('action'), function(response) {
			btn.attr('disabled', false).val(orgLabel);
			
			var infobox = $('<span>').addClass('info').text('Kopię zapisano do pliku ' + response);
			form.append(infobox);
			setTimeout(function() {
				infobox.fadeOut("slow");
			}, 
			6000);
		});
		
		return false;
	});
	
});