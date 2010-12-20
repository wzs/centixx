var ROLE_DEPARTMENT_CHIEF = 5; //wyciagnac dynamicznie z PHP

//funkcje uzywane do formatowania etykiet na wykresach highcharts
function timeFormatter() {
	return '<b>'+ this.point.name +'</b>: '+ this.y +' godzin';
}
function cashFormatter() {
	return '<b>'+ this.point.name +'</b>: '+ this.y +' zł';
}


function addFlashMessage(msg, success)
{
	var infobox = $('<div>').addClass(success ? 'success' : 'error').text(msg);
	$('#flashMessages').append(infobox);
	setTimeout(function() {
		infobox.fadeOut("slow");
	}, 
	5000);	
}

//uzupelnianie na bieząco okienka z podglądem logów
function getLogs(container) {
	$.get(rootUrl + '/admin/get-logs', null, function(response) {
		var lines = eval(response);
		container.find('div').remove();
		while(line = lines.pop()) {
			container.prepend($('<div>').text(line));
		}
		
		setTimeout(function() {
			getLogs(container);
		}, 20000);
	});
}

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
	
	//potwierdzenie usuniecia projektu
	$('#project_delete').click(function() {
		if (!confirm('Czy na pewno chcesz usunąć ten projekt?')) {
			return false;
		}
		return true;
	});
	
	//AJAXowe tworzenie kopii bazy danych
	$('#dbBackup').click(function() {
		var btn = $(this);
		var form = btn.parent();
		var orgLabel = btn.val();
		btn.attr('disabled', 'disabled').val('Tworzenie kopii...');
		$.get(form.attr('action'), function(response) {
			btn.attr('disabled', false).val(orgLabel);
			addFlashMessage('Kopię zapisano do pliku ' + response, true);
		});
		
		return false;
	});
	
	//AJAXowe czyszczenie logów
	$('#clearLogs').click(function() {
		
		if (!confirm('Czy na pewno chcesz wyczyścić logi (zostaną one zarchiwizowane)?')) {
			return false;
		}
		
		var btn = $(this);
		var form = btn.parent();
		var orgLabel = btn.val();
		btn.attr('disabled', 'disabled').val('Czyszczenie...');
		$.get(form.attr('action'), function(response) {
			btn.attr('disabled', false).val(orgLabel);
			$('.logViewer pre').text(' ');
			addFlashMessage('Logi zostały wyczyszczone', true);
		});
		
		return false;
	});
	
	//znikanie komunikatów blyskawicznych
	$('#flashMessages .fading').each(function(i) {
		var li = $(this);
		setTimeout(function() {
			li.fadeOut("slow");
		}, 
		(2000 + 500 * i));
	});
	
	//odwierzanie okienka z logami
	if ($('.logViewer pre').size()) {
		setTimeout(function() {
			getLogs($('.logViewer pre'));
		}, 1000);
	}
	
	//zapewnienie, że user będzie miał rolę
	$('form#user-edit').submit(function() {
		if ($(this).find('input[type=checkbox]:checked').size() == 0) {
			alert('Musisz zaznaczyć conajmniej jedną rolę');
			return false;
		}
	});
	
	//ukrywam role, których nie powinno się zmieniać w module kadrowym
	$('#roles-element').find('br:eq(2), br:eq(3), label:eq(2), label:eq(3)').css('display', 'none');

	
	//pokazuje listę działów, gdy wybrano kierownika dzialu
	var departmentEl = $('#department-label, #department-element');
	departmentEl.hide();
	$('#roles-element input').click(function() {
		if ($(this).val() != ROLE_DEPARTMENT_CHIEF) return;

		if (this.checked) {
			departmentEl.slideDown('slow');
		} else {
			$('#department').val(0);
			departmentEl.slideUp('slow');
		}
	});
});