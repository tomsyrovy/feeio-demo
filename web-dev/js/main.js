function showAlert(type, message){

	var $alertContainer = $('.alert-js');
	$alertContainer.addClass('alert-'+type);
	$alertContainer.find('.message').html(message);
	$alertContainer.slideDown().delay(4000).slideUp();

}





$(document).ready(function() {



	// Pokud jsi na stránce Timesheet seznamu, focusni první input
	// if ( $('body').hasClass('page-timesheet-list') ) {
	// 	window.onload = function() {
	// 		var input = document.getElementById("appbundle_timesheet_date").focus();
	// 	}
	// }


	// FastClick
	FastClick.attach(document.body);


	// DataTables
	$('.timesheet-table').DataTable({
		"paging": false,
		"info": false,
		"language": {
			"url": "//cdn.datatables.net/plug-ins/1.10.10/i18n/Czech.json"
		},
		"columnDefs": [{
			"targets": 'no-orderable',
			"orderable": false
		}],
		"initComplete": function () {
			var r = '<tr class="row-filter">';
			this.api().columns().every(function(){
				r = r+'<th></th>';
			});
			r = r+'</th>';
			var $r = $('.timesheet-table').find('thead').append(r);
			this.api().columns('.filterable').every( function () {
				var column = this;
				var select = $('<select><option value=""></option></select>')
					.appendTo( $('.timesheet-table').find('.row-filter th:eq('+this.index()+')') )
					.on( 'change', function () {
						var val = $.fn.dataTable.util.escapeRegex(
							$(this).val()
						);

						column
							.search( val ? '^'+val+'$' : '', true, false )
							.draw();
					} );

				column.data().unique().sort().each( function ( d, j ) {
					var val = $('<div/>').html(d).text();
					select.append( '<option value="' + val + '">' + val + '</option>' );
				} );
			} );
			t.find('.row-filter select:eq(0)').val('otevřený').trigger('change');
		},
		"footerCallback": function ( row, data, start, end, display ) {
			var api = this.api();

			// Remove the formatting to get integer data for summation
			var intVal = function ( i ) {
				return typeof i === 'string' ?
				i.replace(/[\$, ]/g, '')*1 :
					typeof i === 'number' ?
						i : 0;
			};

			function thousandSeparator(x) {
				return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
			}

			api.columns('.sum', { page: 'current' }).every(function () {
				var sum = api
					.cells( null, this.index(), { page: 'current'} )
					.render('display')
					.reduce(function (a, b) {
						var x = intVal(a) || 0;
						var y = intVal(b) || 0;
						return x + y;
					}, 0);
				$(this.footer()).html(thousandSeparator(sum));
			});

			api.columns('.sum', { page: 'current' }).every(function () {
				api.cells( null, this.index(), { page: 'current'}).every(function() {
					var d = this.data();
					this.data(thousandSeparator(d));
				});
			});
		}
	});

	$('.table-datatable').each(function(){

		var t = $(this);

		var o = t.find('.orderable-default').index();
		if(o <= 0){
			o = 1;
		}

		t.DataTable({
			"paging": false,
			"info": false,
			"order" : [[o, "asc"]],
			"language": {
				"url": "//cdn.datatables.net/plug-ins/1.10.10/i18n/Czech.json"
			},
			"columnDefs": [{
				"targets": 'no-orderable',
				"orderable": false
			}],
			"initComplete": function () {
				var r = '<tr class="row-filter">';
				this.api().columns().every(function(){
					r = r+'<th></th>';
				});
				r = r+'</th>';
				var $r = t.find('thead').append(r);
				this.api().columns('.filterable').every( function () {
					var column = this;
					var select = $('<select><option value=""></option></select>')
						.appendTo( t.find('.row-filter th:eq('+this.index()+')') )
						.on( 'change', function () {
							var val = $.fn.dataTable.util.escapeRegex(
								$(this).val()
							);

							column
								.search( val ? '^'+val+'$' : '', true, false )
								.draw();
						} );

					column.data().unique().sort().each( function ( d, j ) {
						var val = $('<div/>').html(d).text();
						select.append( '<option value="' + val + '">' + val + '</option>' );
					} );
				} );
				t.find('.row-filter select:eq(0)').val('otevřený').trigger('change');
			},
			"footerCallback": function ( row, data, start, end, display ) {
				var api = this.api();

				// Remove the formatting to get integer data for summation
				var intVal = function ( i ) {
					return typeof i === 'string' ?
					i.replace(/[\$, ]/g, '')*1 :
						typeof i === 'number' ?
							i : 0;
				};

				function thousandSeparator(x) {
					return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
				}

				api.columns('.sum', { page: 'current' }).every(function () {
					var sum = api
						.cells( null, this.index(), { page: 'current'} )
						.render('display')
						.reduce(function (a, b) {
							var x = intVal(a) || 0;
							var y = intVal(b) || 0;
							return x + y;
						}, 0);
					$(this.footer()).html(thousandSeparator(sum));
				});

				api.columns('.sum', { page: 'current' }).every(function () {
					api.cells( null, this.index(), { page: 'current'}).every(function() {
						var d = this.data();
						this.data(thousandSeparator(d));
					});
				});
			}
		});

	});

	// Bootstrap Tooltips
	$("[data-toggle='tooltip']").tooltip();





	// Skrytí flash alertu
	$(".alert-dismiss").delay(4000).slideUp();

	/**
	 * Pokud stránka obsahuje druhý navbar, pak body přiřaď třídu .has-secondary-navbar,
	 * která obsahuje patřičný padding.
	 */
	// if ( $( ".navbar-secondary" ) [0] ) {
	// 	$( "body" ).addClass( "has-secondary-navbar" );
    // }





	// Navbar Toggle
	/**
	 * Po kliknutí přidej navbaru třídu active, která zajístí animování burgeru
	 */
	$(".navbar-toggle").on("click", function () {
		$(this).toggleClass("active");
	});

	// Bootstrap popover
	$('[data-toggle="popover"]').popover();


	// VAT
	$('#btn-import-by-in').on("click", function () {
		var ico = $('#appbundle_contact_vatnumber').val();
        if(ico.length >= 8) {
            $.ajax({
                url: $('#appbundle_contact_vatnumber').attr("url"),
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                data: "ico=" + ico,
                cache: false,
                success: function (data) {
                    if (data.stav == 'ok') {
                        $('#appbundle_contact_taxnumber').val(data.taxnumber);
                        $('#appbundle_contact_title').val(data.title);
                        $('#appbundle_contact_street').val(data.street);
                        $('#appbundle_contact_number').val(data.number);
                        $('#appbundle_contact_city').val(data.city);
                        $('#appbundle_contact_zipcode').val(data.zipcode);
                        //alert('Název a adresa kontaktu bylo vyplněno z databáze ARES.');
                    } else {
                        alert(data.stav);
                    }
                },
            });
        }
    });

    $(document).ajaxSend(function(event, request, settings) {
        $('.page-contact-create .fa-ares-loading').css( "opacity", 1 );
    });

    $(document).ajaxComplete(function(event, request, settings) {
        $('.page-contact-create .fa-ares-loading').css( "opacity", 0 );
    });

	// Table toggle
	$("a[data-type='toggle']").click(function(e){

		//do nothing
		e.preventDefault();

		//ser variables
		var $i = $(this).find("i");
		var $toggle = $i.hasClass("fa-toggle-on");

		//find table
		var $table = $("#" + $(this).attr("data-table"));

		if($table.length == 1){

			if($toggle){
				$i.removeClass("fa-toggle-on").addClass("fa-toggle-off");
			}else{
				$i.removeClass("fa-toggle-off").addClass("fa-toggle-on");
			}

			//for each value of data-value
			var values = $(this).attr("data-value").split(",");

			for(var i = 0; i < values.length; i++){

				var $value = values[i];

				if(!$toggle){
					$table.find("td[data-col='" + $(this).attr("data-col") + "'][data-value='" + $value + "']").closest("tr").show();
				}else{
					$table.find("td[data-col='" + $(this).attr("data-col") + "'][data-value='" + $value + "']").closest("tr").hide();
				}

			}

		}else{

			alert("Tabulka s ID #" + $(this).attr("data-table") + " neexistuje nebo více tabulek má toto ID stejné.");

		}

	});

	// Show table-icons on table hover
	// $('table').each(function(){
	// 	var html = '<div class="table-icons text-right"><p><button class="btn btn-sm btn-default"><i class="fa fa-fw fa-floppy-o"></i> Exportovat</button></p></div>';
	// 	$(this).parents(".panel").before(html);
	// });

	// Skryj sloupečky, které dle definice v TH mají být skryté
	$("table").each(function(){
		var table = $(this);
		var columns = $(this).find("th[data-visibility='0']");
		columns.each(function(){
			var letter = $(this).attr('data-cell').substr(0, 1);
			table.find("th[data-cell^='"+letter+"']").hide();
			table.find("td[data-cell^='"+letter+"']").hide();
		});
	});

	// Run calcx
	if($('table.table-calx').length > 0){
		$('table.table-calx').calx();
	}

	// Run highChartTable
	if($('table.table-highchart').length > 0){
		$('table.table-highchart').highchartTable();
	}

	$(".btn-data-graph").click(function(){
		$(".data-graph-parent").slideToggle();
	});
	$(".data-graph-parent").hide();


	//Toggle .container and .container-fluid
	$(".container-toggle").click(function(e){

		if ($(this).attr('currentLayout') == "container"){
			$(".container").addClass("container-fluid").removeClass("container");
			$(this).attr('currentLayout', 'container-fluid');
		} else {
			$(".container-fluid").addClass("container").removeClass("container-fluid");
			$(this).attr('currentLayout', 'container');
		}

		$(this).find('span').text( $(this).attr('data-text-' + $(this).attr('currentLayout')) );

		$.ajax({
			url: $(this).attr("url"),
			contentType: "application/json; charset=utf-8",
			dataType: "json",
			data: "currentLayout=" + $(this).attr('currentLayout'),
			cache: false
		});

	});

	//Table-report functions
	$('.table-report .hiearchy-tree-node a').click(function(e){
		e.preventDefault();

		var c = $(this).closest('.hiearchy-tree-node').attr('data-code');
		var d = $(this).closest('.hiearchy-tree-node').attr('data-depth');
		var s = $(this).closest('.hiearchy-tree-node').attr('data-status');

		if(s === "close"){
			$(this).closest('.hiearchy-tree-node').attr('data-status', 'open');
			$(this).find('i').removeClass('fa-caret-right');
			$(this).find('i').addClass('fa-caret-down');
			d = d*1+1;

			$('.table-report .hiearchy-tree-node[data-code*="' + c + '-"][data-depth="' + d + '"]').show();

		}

		if(s === "open"){
			$(this).closest('.hiearchy-tree-node').attr('data-status', 'close');
			$(this).find('i').removeClass('fa-caret-down');
			$(this).find('i').addClass('fa-caret-right');
			$('.table-report .hiearchy-tree-node[data-code*="' + c + '-"]').each(function(e){
				$(this).attr('data-status', 'close');
				$(this).find('a').find('i').removeClass('fa-caret-down');
				$(this).find('a').find('i').addClass('fa-caret-right');
				$(this).hide();
			});
		}

		toggleReportCells();

	});

	function toggleReportCells(){
		$('.table-report .cell').hide();
		$('.table-report .hiearchy-tree-node-vertical:visible').each(function(e){

			var vertical = $(this);

			$('.table-report .hiearchy-tree-node-horizontal:visible').each(function(e){

				var horizontal = $(this);
				$('.table-report .cell[data-vertical="' + vertical.attr('data-code') + '"][data-horizontal="' + horizontal.attr('data-code') + '"]').show();

			});

		});
	}

});

